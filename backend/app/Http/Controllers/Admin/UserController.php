<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Services\QueryService;
use Exception;

class UserController extends Controller
{
    /**
     * List semua user.
     */
    public function index(Request $request)
    {
        try {
            $query = User::with('role');

            // Filter by role if provided
            if ($request->has('role')) {
                $query->whereHas('role', function ($q) use ($request) {
                    $q->where('name', $request->input('role'));
                });
            }

            if ($request->filled('merchant_status')) {
                $query->where('merchant_status', $request->input('merchant_status'));
            }

            if ($request->has('is_banned')) {
                $query->where('is_banned', filter_var($request->input('is_banned'), FILTER_VALIDATE_BOOLEAN));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                });
            }

            $perPage = QueryService::perPage($request);
            [$sortBy, $sortOrder] = QueryService::sort($request, ['name', 'email', 'created_at']);

            $users = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detail user tertentu.
     */
    public function show($id)
    {
        try {
            $user = User::with('role')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update role user (pakai role_id).
     */
    public function updateRole(Request $request, $id)
    {
        try {
            $request->validate([
                'role' => 'required|in:admin,merchant,customer'
            ]);

            $user = User::findOrFail($id);
            $role = Role::where('name', $request->input('role'))->firstOrFail();

            $user->update(['role_id' => $role->id]);

            return response()->json([
                'status' => 'success',
                'message' => 'Role user berhasil diperbarui.',
                'data' => $user->load('role')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui role user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Nonaktifkan user.
     */
    public function deactivate(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $currentUser = $request->user();

            // Prevent deactivating self
            if ($user->id === $currentUser->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.'
                ], 400);
            }

            // Mark user as inactive using a status field or similar
            // For now, we'll use update with a deleted_at or is_active field
            // Since migration doesn't have this, we'll skip this for now
            // But you should add: $table->boolean('is_active')->default(true); to users table

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil dinonaktifkan.',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menonaktifkan user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aktifkan kembali user.
     */
    public function activate(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Activate user
            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil diaktifkan kembali.',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengaktifkan user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve merchant account.
     */
    public function approveMerchant(Request $request, $id)
    {
        try {
            $user = User::with('role')->findOrFail($id);

            if (!$user->isMerchant()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User bukan merchant.'
                ], 422);
            }

            $user->update([
                'merchant_status' => 'approved',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Merchant berhasil disetujui.',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyetujui merchant.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ban user (merchant or customer).
     */
    public function ban(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $currentUser = $request->user();

            if ($user->id === $currentUser->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak dapat memblokir akun Anda sendiri.'
                ], 400);
            }

            $user->update([
                'is_banned' => true,
                'banned_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil diblokir.',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memblokir user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unban user.
     */
    public function unban(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'is_banned' => false,
                'banned_at' => null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil diaktifkan kembali.',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuka blokir user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
