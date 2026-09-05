<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Gestion des utilisateurs — réservée au rôle ADMIN
     * (middleware role:ADMIN appliqué dans routes/web.php).
     */
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::with('role')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role_id'  => ['required', Rule::exists('roles', 'id')],
        ]);

        // Assignation explicite : fonctionne que role_id soit ou non dans $fillable
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role_id = $data['role_id'];
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Compte créé : '.$user->email);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role_id'  => ['required', Rule::exists('roles', 'id')],
        ]);

        // Garde-fou : impossible de retirer son propre rôle ADMIN
        $newRole = Role::find($data['role_id']);
        if ($user->id === $request->user()->id && $newRole?->code !== 'ADMIN') {
            return back()->with('error', 'Vous ne pouvez pas retirer votre propre rôle administrateur.');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role_id = $data['role_id'];
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Compte mis à jour : '.$user->email);
    }
}
