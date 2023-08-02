<?php

namespace App\Http\Controllers\API;

use App\MessagesTemplate\MessagesTemplate;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = Auth::user();
        $cards = User::find($user->id)->cards;

        if (sizeof($cards) >= 1) {
            return response()->json([
                'cards' => $cards
            ], 201);
        } else {
            return response()->json([
                'message' => 'Cannot find Cards'
            ], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'provider' => 'present|string'
        ]);

        $user = Auth::user();

        if ($user->id !== $request->user_id) {
            return response()->json([
                "message" => "Unauthorized Request."
            ], 401);
        }

        if ($request->provider !== 'Visa') {

            $details = [
                'user_id' => $request->user_id,
                'title' => 'Card Failed',
                'body' => MessagesTemplate::message()['card_error']('Invalid Provider.')
            ];

            // Create Message
            MessageController::create($details);

            return response()->json([
                'message' => 'Invalid Provider'
            ], 422);
        }

        // Generate Card Number
        $card_number = $this->generate_card_number();

        $card = Card::create([
            'user_id' => $request->user_id,
            'number' => $card_number,
            'provider' => $request->provider
        ]);

        $details = [
            'user_id' => $request->user_id,
            'title' => 'Card Creation Success',
            'body' => MessagesTemplate::message()['card_success']()
        ];

        // Create Message
        MessageController::create($details);


        return response()->json([
            'message' => 'Card created successfully',
            'card' => $card
        ], 201);
    }

    public function generate_card_number()
    {
        $card_number = random_int(121212121212, 999999999999);
        $is_card_number = Card::where('number', $card_number)->first();

        if ($is_card_number) {
            $card_number = random_int(121212121212, 999999999999);
        }

        return $card_number;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $user = Auth::user();
        User::find($user->id)
            ->cards
            ->where('id', $request->id)
            ->delete();

        return response()->json([
            'message' => 'Card deleted successfully'
        ], 201);
    }
}