<?php

namespace App\Http\Controllers\API;

use App\MessagesTemplate\MessagesTemplate;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Deposit;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function recents()
    {
        $user = Auth::user();
        $deposits = User::find($user->id)->deposits->orderBy('created_at', 'desc')->limit(7)->get();

        return response()->json([
            'deposits' => $deposits
        ], 200);
    }

    public function index()
    {
        $user = Auth::user();
        $deposits = User::find($user->id)->deposits;

        if (!$deposits) {
            return response()->json([
                'deposits' => []
            ], 401);
        } else {
            return response()->json([
                'deposits' => $deposits
            ], 200);
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'amount' => 'required|numeric',
            'service' => 'required|string'
        ]);

        $transaction_id = $this->generate_deposit_id();

        $deposit = Deposit::create([
            'transaction_id' => $transaction_id,
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'service' => $request->service,
            'status' => 'pending'
        ]);

        response()->json([
            'message' => 'Deposit request confirmed',
            'deposit' => $deposit
        ], 201);

        return DepositController::execute_deposit($deposit);
    }

    public function execute_deposit($deposit)
    {
        $user = Auth::user();
        if ($user->id !== $deposit->user_id) {
            $deposit->status = 'failed';

            $details = [
                'user_id' => $user->id,
                'title' => 'Deposit Failed',
                'body' => MessagesTemplate::message()['deposit_error']('Unauthorized Transaction.'),

            ];

            // Create Message
            MessageController::create($details);

            return response()->json([
                'message' => 'Unauthorized Transaction.'
            ], 401);
        }

        $user = $deposit->user;
        $user->balance += $deposit->amount;
        $user->save();

        $deposit->status = 'completed';
        $deposit->save();

        $details = [
            'user_id' => $user->id,
            'title' => 'Deposit Success',
            'body' => MessagesTemplate::message()['deposit_success']($deposit->amount),

        ];

        MessageController::create($details);

        return response()->json([
            'message' => 'Deposit completed successfully',
            'deposit' => $deposit,
            'amount' => $deposit->amount
        ], 201);
    }

    public static function generate_deposit_id()
    {
        $transaction_id = 'DPT-' . random_int(100000, 999999);
        $is_deposit = Deposit::where('transaction_id', $transaction_id)->first();

        error_log($transaction_id);

        if ($is_deposit) {
            DepositController::generate_deposit_id();
        }

        return $transaction_id;
    }
}