<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Message;
use Illuminate\Support\Facades\Log;

class ContactsController extends Controller
{
    public function get()
    {
        $contacts = User::where('id', '!=', auth()->id())->get();

        $unreadIds = Message::select(\DB::raw('`from_user_id` as sender_id, count(`from_user_id`) as message_count'))
            ->where('to_user_id', auth()->id())
            ->where('status', false)
            ->groupBy('from_user_id')
            ->get();

        $contacts = $contacts->map(function($contact) use($unreadIds) {
           $contactUnread = $unreadIds->where('sender_id', $contact->id)->first();

           $contact->unread = $contactUnread ? $contactUnread->message_count : 0;

           return $contact;
        });

        return response()->json($contacts);
    }
}
