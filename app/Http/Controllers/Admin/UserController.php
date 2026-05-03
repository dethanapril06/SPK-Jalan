<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    private const DEFAULT_PASSWORD = 'password';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::query()
            ->whereIn('role', ['admin', 'kepala_dinas'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => self::DEFAULT_PASSWORD,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan. Password default: ' . self::DEFAULT_PASSWORD);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->ensureManageable($user);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->ensureManageable($user);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->ensureManageable($user);

        $user->update($request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->ensureManageable($user);

        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak dapat menghapus akun yang sedang digunakan.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password user ke password default.
     */
    public function resetPassword(User $user)
    {
        $this->ensureManageable($user);

        $user->update([
            'password' => self::DEFAULT_PASSWORD,
        ]);

        return back()->with('success', 'Password user berhasil direset ke default: ' . self::DEFAULT_PASSWORD);
    }

    private function ensureManageable(User $user): void
    {
        abort_unless(in_array($user->role, ['admin', 'kepala_dinas'], true), 404);
    }
}
