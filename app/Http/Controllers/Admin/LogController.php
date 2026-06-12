<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminLog;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = AdminLog::with('admin');

        if ($search) {
            $query->where('aksi', 'like', "%{$search}%")
                  ->orWhere('tabel_target', 'like', "%{$search}%")
                  ->orWhereHas('admin', function($q) use ($search) {
                      $q->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.logs.index', compact('logs', 'search'));
    }
}
