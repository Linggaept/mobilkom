<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role  = $request->get('role', 'pelapor');
        $users = User::where('role', $role)
            ->when($request->search, fn($q, $v) =>
                $q->where('name', 'like', "%$v%")->orWhere('email', 'like', "%$v%")
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.user.index', compact('users', 'role'));
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'no_hp'    => 'nullable|string|max:20',
            'jabatan'  => 'nullable|string|max:100',
            'role'     => 'required|in:pelapor,teknisi,admin,pimpinan',
            'site'     => 'nullable|string',
            'password' => ['required', Password::min(6)],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'no_hp'    => $request->no_hp,
            'jabatan'  => $request->jabatan,
            'role'     => $request->role,
            'site'     => $request->site,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index', ['role' => $request->role])
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'no_hp'    => 'nullable|string|max:20',
            'jabatan'  => 'nullable|string|max:100',
            'role'     => 'required|in:pelapor,teknisi,admin,pimpinan',
            'site'     => 'nullable|string',
            'password' => ['nullable', Password::min(6)],
        ]);

        $data = $request->only(['name', 'email', 'no_hp', 'jabatan', 'role', 'site']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index', ['role' => $user->role])
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User berhasil $status.");
    }
}