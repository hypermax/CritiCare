<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Journal d'audit — réservé au rôle ADMIN
     * (middleware role:ADMIN appliqué dans routes/web.php).
     */
    public function index(): View
    {
        return view('admin.audit.index', [
            'logs' => AuditLog::with('user')->latest('id')->paginate(50),
        ]);
    }
}
