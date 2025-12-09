<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    /**
     * List semua user.
     */
    public function index()
    {
        $users = User::with('role')->latest()->get();

        return response()->json([
            'data' => $users
        ]);
    }

    /**
     * Form create user (resource stub).
     */
    public function create()
    {
        return response()->json([
            'message' => 'Not implemented: create user form (admin).'
        ], 200);
    }

    /**
     * Simpan user baru (resource stub).
     */
    public function store(Request $request)
    {
        return response()->json([
            'message' => 'Not implemented: store user (admin).'
        ], 200);
    }

    /**
     * Detail user tertentu.
     */
    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);

        return response()->json([
            'data' => $user
        ]);
    }

    /**
     * Form edit user (resource stub).
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'message' => 'Not implemented: edit user form (admin).',
            'data' => $user
        ], 200);
    }

    /**
     * Update user (resource stub).
     */
    public function update(Request $request, $id)
    {
        return response()->json([
            'message' => 'Not implemented: update user (admin).'
        ], 200);
    }

    /**
     * Hapus user (resource stub).
     */
    public function destroy($id)
    {
        return response()->json([
            'message' => 'Not implemented: destroy user (admin).'
        ], 200);
    }

    /**
     * Update role user (pakai role_id).
     * Route: PUT /admin/users/{id}/role (web)
     * Route: PUT /api/admin/users/{id}/role (api)
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,merchant,customer'
        ]);

        $user = User::findOrFail($id);
        $role = Role::byName($request->role)->firstOrFail();

        $user->update(['role_id' => $role->id]);

        return response()->json([
            'message' => 'Role user diperbarui.',
            'data' => $user->load('role')
        ]);
    }

    /**
     * Nonaktifkan user.
     * Route: PUT /admin/users/{id}/deactivate (web/api)
     */
    public function deactivate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['active' => false]);

        return response()->json([
            'message' => 'User dinonaktifkan.',
            'data' => $user
        ]);
    }

    /**
     * Aktifkan kembali user.
     * Route: PUT /admin/users/{id}/activate (web/api)
     */
    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['active' => true]);

        return response()->json([
            'message' => 'User diaktifkan kembali.',
            'data' => $user
        ]);
    }
}
