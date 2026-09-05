<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Hospitalization;
use App\Models\Setting;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Hub d'administration — réservé au rôle ADMIN
     * (middleware role:ADMIN appliqué dans routes/web.php).
     */
    public function index(): View
    {
        return view('admin.index', [
            'usersCount'  => User::count(),
            'logsCount'   => AuditLog::count(),
            'activeStays' => Hospitalization::where('status', 'active')->count(),
            'nbBeds'      => Setting::nbBeds(),
        ]);
    }
}
