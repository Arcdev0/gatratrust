<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    // Halaman utama (view)
    public function index()
    {
        return view('log.index');
    }

    // Data untuk datatable (ajax)
  public function data(Request $request)
    {
        if ($request->ajax()) {
            $logs = ActivityLog::with('user')
                ->select(['id', 'user_id', 'reference', 'description', 'created_at'])
                ->orderBy('id', 'desc');

            if ($request->filterRange) {
                $dates = explode(' s/d ', $request->filterRange);
                if (count($dates) == 2) {
                    $start = $dates[0] . ' 00:00:00';
                    $end   = $dates[1] . ' 23:59:59';
                    $logs->whereBetween('created_at', [$start, $end]);
                }
            }

            return DataTables::of($logs)
                ->addIndexColumn()
                ->addColumn('user_name', function ($row) {
                    return $row->user->name ?? 'Unknown';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? date('H:i d M Y', strtotime($row->created_at)) : '';
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info view-detail" data-id="'.$row->id.'">
                                <i class="fas fa-eye"></i>
                            </button>';
                })
                ->rawColumns(['action']) // biar html button tidak di-escape
                ->make(true);
        }
    }


    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);

        return response()->json([
            'id'          => $log->id,
            'user'        => $log->user->name ?? 'Unknown',
            'reference'   => $log->reference,
            'description' => $log->description,
            'old_data' => $log->old_data ? json_decode($log->old_data, true) : null,
            'new_data' => $log->new_data ? json_decode($log->new_data, true) : null,
            'created_at'  => Carbon::parse($log->created_at)->format('H:i d M Y'),
        ]);
    }




}
