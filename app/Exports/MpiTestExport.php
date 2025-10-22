<?php

namespace App\Exports;

use App\Models\MpiTest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class MpiTestExport implements FromCollection
{
    protected $testId;

    public function __construct($testId)
    {
        $this->testId = $testId;
    }

    /**
     * Return a Collection of rows (each row is an array).
     * We'll output:
     * - beberapa baris header (test info)
     * - blank row
     * - header kolom items
     * - rows items
     */
    public function collection()
    {
        $test = MpiTest::with(['items.materials','items.posisis'])->findOrFail($this->testId);

        $rows = [];

        // Test header rows
        $rows[] = ['MPI Test ID', $test->id];
        $rows[] = ['Nama PT', $test->nama_pt];
        $rows[] = ['Tanggal Running', $test->tanggal_running ? $test->tanggal_running->format('Y-m-d') : ''];
        $rows[] = ['Tanggal Inspection', $test->tanggal_inspection ? $test->tanggal_inspection->format('Y-m-d') : ''];
        $rows[] = ['Person', $test->person];
        $rows[] = []; // blank

        // Items header
        $rows[] = ['Item ID','Nama Jurulas','Proses Las','Posisi Uji','Materials (name|qty|note)','Foto Jurulas','Foto KTP','Foto Sebelum','Foto During','Foto Hasil','Foto Sebelum MPI','Foto Setelah MPI'];

        // Items rows
        foreach ($test->items as $item) {
            $pos = $item->posisis->pluck('nama_posisi')->join(';');
            $materials = $item->materials->map(function($m){
                return ($m->nama_material ?? '') . '|' . ($m->qty ?? '') . '|' . ($m->note ?? '');
            })->join(';;');

            $rows[] = [
                $item->id,
                $item->nama_jurulas,
                $item->proses_las,
                $pos,
                $materials,
                $item->foto_jurulas ? asset('storage/'.$item->foto_jurulas) : '',
                $item->foto_ktp ? asset('storage/'.$item->foto_ktp) : '',
                $item->foto_sebelum ? asset('storage/'.$item->foto_sebelum) : '',
                $item->foto_during ? asset('storage/'.$item->foto_during) : '',
                $item->foto_hasil ? asset('storage/'.$item->foto_hasil) : '',
                $item->foto_sebelum_mpi ? asset('storage/'.$item->foto_sebelum_mpi) : '',
                $item->foto_setelah_mpi ? asset('storage/'.$item->foto_setelah_mpi) : '',
            ];
        }

        return new Collection($rows);
    }
}
