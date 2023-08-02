<?php

namespace App\Http\Controllers\API;

use App\MessagesTemplate\MessagesTemplate;
use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function recents()
    {
        $user = Auth::user();
        $transfers = Transfer::where('user_id', $user->id)->orWhere('payee_id', $user->id)->orderBy('created_at', 'desc')->limit(7)->get();
        return response()->json([
            'transfers' => $transfers
        ], 200);
    }

    public function index()
    {
        $user = Auth::user();
        $transfers = Transfer::join('users', 'users.id', '=', 'transfers.payee_id')->where('user_id', $user->id)->orWhere('payee_id', $user->id)->orderBy('created_at', 'desc')->get(['transfers.*', 'users.first_name', 'users.last_name']);

        return response()->json([
            'transfers' => $transfers
        ], 201);
    }

    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'account_number' => 'required|integer',
            'amount' => 'required|numeric',
        ]);

        error_log($request->account_number);

        $transaction_id = TransferController::generate_transfer_id();

        $transfer = Transfer::create([
            'transaction_id' => $transaction_id,
            'payee_id' => $request->payee_id,
            'user_id' => $request->user_id,
            'account_number' => $request->account_number,
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        $user = Auth::user();
        $payee = User::where('account_number', $request->account_number)->first();

        error_log($payee->first_name);
        if (!$payee) {
            $transfer->status = 'failed';

            $details = [
                'user_id' => $user->id,
                'title' => 'Transfer Failed',
                'body' => MessagesTemplate::message()['transfer_error']('This Account does not exist. Transaction Failed'),
            ];

            // Create Message
            MessageController::create($details);

            return response()->json([
                'messege' => 'This Account does not exist. Transaction Failed'
            ], 404);
        }

        return TransferController::execute_transfer($transfer, $payee);
    }

    public function execute_transfer($transfer_body, $payee)
    {
        $user = Auth::user();
        if ($user->id === $payee->id) {
            $transfer_body->status = 'failed';

            return response()->json([
                'messege' => 'You cannot transfer money to your Account. Only Deposits'
            ], 404);
        }

        if ($user->balance < $transfer_body->amount) {
            $transfer_body->status = 'failed';

            $details = [
                'user_id' => $user->id,
                'title' => 'Transfer Failed',
                'body' => MessagesTemplate::message()['transfer_error']('Insufficient Funds')
            ];

            // Create Message
            MessageController::create($details);

            return response()->json([
                'message' => 'Insufficient Funds'
            ], 405);
        }

        error_log($transfer_body->user);

        if ($user->id !== $transfer_body->user->id) {
            $transfer_body->status = 'failed';
            $transfer_body->save();

            $details = [
                'user_id' => $user->id,
                'title' => 'Transfer Failed',
                'body' => MessagesTemplate::message()['transfer_error']('Unauthorized Transaction'),

            ];

            // Create Message
            MessageController::create($details);

            return response()->json([
                'message' => 'Unauthorized Transaction'
            ], 401);
        }

        $user =  $transfer_body->user;
        $user->balance -= $transfer_body->amount;
        $user->save();

        $payee->balance += $transfer_body->amount;
        $payee->save();

        $transfer_body->status = 'completed';
        $transfer_body->save();

        $name = [
            'first' => $payee->first_name,
            'last' => $payee->last_name
        ];

        $details = [
            'user_id' => $user->id,
            'title' => 'Transfer Failed',
            'body' => MessagesTemplate::message()['transfer_success']($transfer_body->amount, $name),

        ];

        // Create Message
        MessageController::create($details);

        $summary = [
            "first" => $user->first_name,
            "last" => $user->last_name,
            "amount" => $transfer_body->amount,
            "balance" => $payee->balance
        ];
        $details = [
            'user_id' => $payee->id,
            'title' => 'Credit Alert',
            'body' => MessagesTemplate::message()['credit_success']($summary),

        ];

        // Create Message
        MessageController::create($details);

        return response()->json([
            'message' => 'Transfer completed successfully',
            'amount' => $transfer_body->amount,
            'transfer' => $transfer_body
        ], 201);
    }

    public function generate_transfer_id()
    {
        $transaction_id = 'TRF-' . random_int(100000, 999999);
        $is_transfer_id = Transfer::where('transaction_id', $transaction_id)->first();

        if ($is_transfer_id) {
            TransferController::generate_transfer_id();
        }

        return $transaction_id;
    }
}