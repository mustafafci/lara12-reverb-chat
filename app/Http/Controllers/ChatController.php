<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Events\UserTyping;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function users()
    {
        $users = User::where('id', '!=', Auth::id())->paginate(10);
        return view('chat.users', compact('users'));
    }

    public function chat($receiverId)
    {
        $receiver = User::findOrFail($receiverId);

        $messages = Message::where(function ($q) use ($receiver) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $receiver->id);
        })->orWhere(function ($q) use ($receiver) {
            $q->where('sender_id', $receiver->id)->where('receiver_id', Auth::id());
        })->get();
        
        $users = User::where('id', '!=', Auth::id())->paginate(10);
        return view('chat.chat', compact('receiver', 'users', 'messages'));
    }

    public function sendMessage(Request $request, $receiverId)
    {
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'message' => $request['message']
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => 'message sent successfully'
        ]);
    }

    public function typing()
    {
        broadcast(new UserTyping(Auth::id()))->toOthers();
        return response()->json([
            'status' => 'success',
            'message' => 'user typing'
        ]);
    }

    public function setOnline()
    {
        Cache::put('user-is-online-' . Auth::id(), true, now()->addMinutes(5));
        return response()->json([
            'status' => 'success',
            'message' => 'user is online'
        ]);
    }


    public function setOffline()
    {
        Cache::forget('user-is-online-' . Auth::id());
        return response()->json([
            'status' => 'success',
            'message' => 'user is offline'
        ]);
    }
}
