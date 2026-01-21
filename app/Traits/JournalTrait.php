<?php

namespace App\Traits;

use App\Models\AccountingSetting;
use App\Models\Coa;
use App\Models\Journal;
use App\Models\JournalLine;
use Illuminate\Support\Facades\DB;

trait JournalTrait
{
    /**
     * CREATE journal generik (flexible untuk invoice, kwitansi, FPU, dll)
     */
    protected function createJournal(array $header, array $lines, array $options = []): Journal
    {
        return DB::transaction(function () use ($header, $lines, $options) {

            if (empty($header['journal_date'])) {
                throw new \Exception('journal_date wajib diisi');
            }
            if (empty($header['type'])) {
                throw new \Exception('type jurnal wajib diisi');
            }

            $status = $header['status'] ?? 'draft';
            $userId = auth()->id();

            if (!is_array($lines) || count($lines) < 2) {
                throw new \Exception('Journal lines minimal 2 baris');
            }

            // ✅ VALIDASI berdasarkan lines yang dikirim (bukan $d / bukan $invoice)
            $lines = $this->normalizeJournalLines($lines);
            $this->validateCoaNotGroup($lines);
            $this->validateJournalBalance($lines);

            $journalNo = $options['journal_no'] ?? $this->generateJournalNo();

            $journal = Journal::create([
                'journal_no'   => $journalNo,
                'journal_date' => $header['journal_date'],
                'type'         => $header['type'],
                'category'     => $header['category'] ?? null,
                'reference_no' => $header['reference_no'] ?? null,
                'memo'         => $header['memo'] ?? null,
                'status'       => $status,
                'created_by'   => $userId,
                'posted_by'    => $status === 'posted' ? $userId : null,
                'posted_at'    => $status === 'posted' ? now() : null,
            ]);

            $payload = [];
            foreach ($lines as $i => $l) {
                $payload[] = [
                    'journal_id'  => $journal->id,
                    'line_no'     => $l['line_no'] ?? ($i + 1),
                    'coa_id'      => $l['coa_id'],
                    'description' => $l['description'] ?? null,
                    'debit'       => $l['debit'],
                    'credit'      => $l['credit'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }

            JournalLine::insert($payload);

            return $journal;
        });
    }


    // =========================
    // UTILITIES (internal)
    // =========================

    protected function normalizeJournalLines(array $lines): array
    {
        $out = [];

        foreach ($lines as $i => $l) {
            $coaId = $l['coa_id'] ?? null;
            if (!$coaId) {
                throw new \Exception("Line ke-" . ($i + 1) . " COA wajib diisi");
            }

            $debit  = (float) ($l['debit'] ?? 0);
            $credit = (float) ($l['credit'] ?? 0);

            if ($debit > 0 && $credit > 0) {
                throw new \Exception("Line ke-" . ($i + 1) . " tidak boleh debit & credit sekaligus");
            }
            if ($debit <= 0 && $credit <= 0) {
                throw new \Exception("Line ke-" . ($i + 1) . " wajib isi debit atau credit");
            }

            $out[] = [
                'line_no' => $l['line_no'] ?? ($i + 1),
                'coa_id' => (int) $coaId,
                'description' => $l['description'] ?? null,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        return $out;
    }

    protected function validateJournalBalance(array $lines): void
    {
        $debit  = collect($lines)->sum('debit');
        $credit = collect($lines)->sum('credit');

        if (round($debit, 2) !== round($credit, 2)) {
            throw new \Exception("Jurnal tidak balance (Debit={$debit}, Credit={$credit})");
        }
        if ($debit <= 0) {
            throw new \Exception("Total jurnal harus lebih dari 0");
        }
    }

    protected function validateCoaNotGroup(array $lines): void
    {
        $coaIds = collect($lines)->pluck('coa_id')->unique();
        $invalid = Coa::whereIn('id', $coaIds)
            ->where('set_as_group', true)
            ->exists();

        if ($invalid) {
            throw new \Exception("COA group tidak boleh digunakan di jurnal");
        }
    }

    protected function generateJournalNo(): string
    {
        $setting = AccountingSetting::lockForUpdate()->first();

        $prefix  = $setting?->journal_prefix ?? 'JR';
        $running = (int) ($setting?->journal_running_number ?? 1);
        $year    = now()->format('Y');

        $journalNo = sprintf('%s-%s-%06d', $prefix, $year, $running);

        if ($setting) {
            $setting->update(['journal_running_number' => $running + 1]);
        }

        return $journalNo;
    }

    /**
     * Helper ambil default COA mapping
     */
    protected function journalDefaults(): array
    {
        $s = AccountingSetting::first();
        return [
            'cash'   => $s?->default_cash_coa_id,
            'bank'   => $s?->default_bank_coa_id,
            'ar'     => $s?->default_ar_coa_id,
            'ap'     => $s?->default_ap_coa_id,
            'sales'  => $s?->default_sales_coa_id,
            'tax'    => $s?->default_tax_payable_coa_id,
            'expense' => $s?->default_expense_coa_id,
        ];
    }
}
