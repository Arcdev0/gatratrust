<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
                ->make(true);
        }
    }


}
