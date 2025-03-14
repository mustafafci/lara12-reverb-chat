<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('typing.{receiverId}', function ($user, $receiverId) {
    return (int) $user->id !== (int) $receiverId;
});