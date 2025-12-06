<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\ToastMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToastController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $toasts = ToastMessage::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Помечаем все полученные сообщения как прочитанные
        ToastMessage::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'data' => $toasts,
        ]);
    }
}
