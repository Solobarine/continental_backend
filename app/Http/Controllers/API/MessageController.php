<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['create']]);
    }

    public function index()
    {
        $user = Auth::user();

        // $messages = User::find($user->id)->messages;
        $messages = Message::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'messages' => $messages
        ], 201);
    }

    public static function create($details)
    {
        $message = Message::create([
            'user_id' => $details['user_id'],
            'sender' => 'Continental',
            'title' => $details['title'],
            'body' => $details['body'],
            'starred' => false,
            'important' => false,
            'opened' => false,
            'archived' => false
        ]);

        return $message;
    }

    public function update(Request $request)
    {
        $request->validate([
            'starred' => 'boolean',
            'important' => 'boolean',
            'opened' => 'boolean',
            'archived' => 'boolean'
        ]);

        error_log($request->starred);

        $user = Auth::user();

        $fields = $request->only(['starred', 'important', 'opened', 'archived']);

        $message = Message::find($request->id);
        error_log($message->starred);
        if ($message->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized Request. Cannot Update message.'
            ], 401);
        }

        $response_message = 'No need for Updating. values are the same';

        if (array_key_exists('starred', $fields)) {

            $message->starred = $fields['starred'];
            error_log('here');
            $response_message = 'Message starred successfully';
            $message->save();

            error_log($message->starred);
            return response()->json([
                'message' => $response_message
            ], 201);
        }

        if (array_key_exists('important', $fields)) {
            $message->important = $fields['important'];
            $response_message = 'Message set as important';
            error_log('im');
            $message->save();

            return response()->json([
                'message' => $response_message
            ], 201);
        }

        if (array_key_exists('opened', $fields) && $message->opened === false) {
            $message->opened = $fields['opened'];
            $response_message = 'Message opened successfully';
            error_log('op');
            $message->save();

            return response()->json([
                'message' => $response_message
            ], 201);
        }

        if (array_key_exists('archived', $fields)) {
            $message->archived = $fields['archived'];
            $response_message = 'Message successfully archived';
            error_log('ar');
            $message->save();

            return response()->json([
                'message' => $response_message
            ], 201);
        }

        $message->save();

        return response()->json([
            'message' => $response_message
        ], 201);
    }
}