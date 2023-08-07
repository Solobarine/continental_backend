<?php

namespace App\Http\Controllers\API;

use App\MessagesTemplate\MessagesTemplate;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email',
            'date_of_birth' => 'required|date',
            'country' => 'required|string',
            'state' => 'required|string',
            'city' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->get();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->date_of_birth = $request->date_of_birth;
        $user->country = $request->country;
        $user->state = $request->state;
        $user->city = $request->city;
        $user->verified = true;
        $user->save();

        $details = [
            'user_id' => $user->id,
            'title' => 'Profile Update',
            'body' => MessagesTemplate::message()['profile_update']()
        ];

        // Create Message
        MessageController::create($details);

        return response()->json([
            'message' => 'User Profile successfully updated',
            'user' => $user
        ], 201);
    }

    public function update_password(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string',
            'confirm_new_password' => 'required|match:new_password'
        ]);

        $check_hash = Hash::check($request->old_password, Auth::user()->password);

        if ($check_hash) {
            $user = User::where('password', $request->password)->first();

            $user->password = Hash::make($request->new_password);
            $user->save();

            $details = [
                'user_id' => $user->id,
                'title' => 'Password Update',
                'body' => MessagesTemplate::message()['password_update']()
            ];

            // Create Message
            MessageController::create($details);

            return response()->json([
                'message' => 'Password changed successfully'
            ], 201);
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function update_email(Request $request)
    {
        $request->validate([
            'old_email' => 'required|string|email',
            'new_email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($request->old_email === $user->email && $request->password === Hash::check($request->password)) {
            $user->email = $request->new_email;
            $user->save();

            $details = [
                'user_id' => $user->id,
                'title' => 'Email Update',
                'body' => MessagesTemplate::message()['email_update']()
            ];

            // Create Message
            MessageController::create($details);

            return response()->json([
                'message' => 'Email changed successfully'
            ], 201);
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public static function generate_account_number()
    {
        $account_no = random_int(1212121212, 9999999999);
        $is_account_no = User::where('account_number', $account_no)->first();

        if ($is_account_no) {
            $account_no = random_int(1212121212, 9999999999);
        }

        return $account_no;
    }

    public function receiver(Request $request)
    {
        $user = User::where('account_number', $request->account_number)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not Found'
            ], 404);
        }

        return response()->json([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'id' => $user->id
        ], 200);
    }

    public function recents()
    {
        $user = Auth::user();
        $deposits = Deposit::where('id', $user->id)->orderBy('created_at', 'desc')->take(5)->get();

        $transfers = Transfer::join('users', 'users.id', '=', 'transfers.payee_id')->where('user_id', $user->id)->orWhere('payee_id', $user->id)->orderBy('created_at', 'desc')->take(5)->get(['transfers.*', 'users.first_name', 'users.last_name']);

        return response()->json(['deposits' => $deposits, 'transfers' => $transfers], 200);
    }
}