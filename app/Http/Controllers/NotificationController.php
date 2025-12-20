<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Services\QueryService;
use Exception;

class NotificationController extends Controller
{
    /**
     * Menampilkan daftar notifikasi pengguna.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $query = Notification::where('user_id', $user->id);

            if ($request->filled('type')) {
                $query->where('type', $request->input('type'));
            }

            if ($request->has('is_read')) {
                $query->where('is_read', filter_var($request->input('is_read'), FILTER_VALIDATE_BOOLEAN));
            }

            $perPage = QueryService::perPage($request);
            [$sortBy, $sortOrder] = QueryService::sort($request, ['created_at', 'type']);

            $notifications = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $notifications
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil notifikasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan statistik notifikasi.
     */
    public function stats(Request $request)
    {
        try {
            $user = $request->user();
            $stats = [
                'total' => Notification::where('user_id', $user->id)->count(),
                'unread' => Notification::where('user_id', $user->id)->where('is_read', false)->count(),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil statistik notifikasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail notifikasi tertentu.
     */
    public function show(Request $request, $id)
    {
        try {
            $notification = Notification::where('user_id', $request->user()->id)
                ->where('id', $id)
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => $notification
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notifikasi tidak ditemukan.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Menandai notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $notification = Notification::where('user_id', $request->user()->id)
                ->where('id', $id)
                ->firstOrFail();

            $notification->update(['is_read' => true]);

            return response()->json([
                'status' => 'success',
                'message' => 'Notifikasi telah ditandai sebagai sudah dibaca.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menandai notifikasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menandai beberapa notifikasi sebagai sudah dibaca.
     */
    public function markMultipleAsRead(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'integer'
        ]);

        try {
            Notification::where('user_id', $request->user()->id)
                ->whereIn('id', $request->notification_ids)
                ->update(['is_read' => true]);

            return response()->json([
                'status' => 'success',
                'message' => 'Notifikasi telah ditandai sebagai sudah dibaca.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menandai notifikasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menandai semua notifikasi sebagai sudah dibaca.
     */
    public function markAllAsRead(Request $request)
    {
        try {
            Notification::where('user_id', $request->user()->id)
                ->update(['is_read' => true]);

            return response()->json([
                'status' => 'success',
                'message' => 'Semua notifikasi telah ditandai sebagai sudah dibaca.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menandai notifikasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus notifikasi.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $notification = Notification::where('user_id', $request->user()->id)
                ->where('id', $id)
                ->firstOrFail();

            $notification->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Notifikasi berhasil dihapus.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus notifikasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
