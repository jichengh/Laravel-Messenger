<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\Events\NewMessage;

class ConversationController extends Controller
{
    public function getMessagesFor($id)
    {
        Message::where('from_user_id', $id)
            ->where('to_user_id', auth()->id())
            ->update(['status' => true]);

        $messages = Message::where(function($q) use($id) {
            $q->where('from_user_id', auth()->id());
            $q->where('to_user_id', $id);
        })->orWhere(function($q) use($id) {
            $q->where('from_user_id', $id);
            $q->where('to_user_id', auth()->id());
        })->get();

        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $message = Message::create([
            'from_user_id' => auth()->id(),
            'to_user_id' => $request->contact_id,
            'message' => $request->message,
        ]);

        broadcast(new NewMessage($message));

        return response()->json($message);
    }
}
