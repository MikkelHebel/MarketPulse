<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function poll(Request $request): JsonResponse
    {
        $notifications = $request->user()->unreadNotifications->map(fn($n) => $n->data);
        $request->user()->unreadNotifications->markAsRead();
        return response()->json($notifications);
    }
}
