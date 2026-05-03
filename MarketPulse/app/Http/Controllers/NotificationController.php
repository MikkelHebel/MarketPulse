<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function poll(Request $request): JsonResponse
    {
        $unread = $request->user()->unreadNotifications;
        $unread->markAsRead();

        $deduped = $unread->unique(fn($n) => $n->data['ticker'].':'.$n->data['type'])
            ->map(fn($n) => $n->data)
            ->values();

        return response()->json($deduped);
    }
}
