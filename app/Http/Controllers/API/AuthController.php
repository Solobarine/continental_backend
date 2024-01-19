<?php

namespace App\Http\Controllers\API;

use App\MessagesTemplate\MessagesTemplate;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\UserTrait;

class AuthController extends Controller
{
    //
    use UserTrait;

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|regex:/^(?=.*?[A-Za-z])(?=.*?[0-9]).{8,20}$/',
            'confirm_password' => 'required|string|same:password'
        ]);

        $is_user = User::where('email', $request->email)->first();

        if ($is_user) {
            return response()->json([
                'message' => 'User Already Exists'
            ], 422);
        }

        $account_number = UserController::generate_account_number();

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'account_number' => $account_number,
            'date_of_birth' => null,
            'country' => null,
            'state' => null,
            'city' => null,
            'verified' => false
        ]);

        $details = [
            'user_id' => $user->id,
            'title' => 'Welcome To Continental',
            'body' => MessagesTemplate::message()['welcome_message']($user->first_name)
        ];

        // Create Message
        $message = MessageController::create($details);

        // Login Automatically
        $credentials = $request->only(['email', 'password']);
        $token = Auth::attempt($credentials);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer'
            ],
            'welcome' => $message
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only(['email', 'password']);
        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'message' => 'Invalid Email or Password'
            ], 401);
        }
        $user = Auth::user();

        return response()->json([
            'message' => ' Logged in successfully',
            'user' => $user,
            'recents' => $this->recents(),
            'authorization' => [
                'token' => $token,
                'type' => 'bearer'
            ]
        ], 201);
    }

    public function user()
    {
        return ['user' => Auth::user(), 'recents' => $this->recents()];
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Logged out successfully'
        ], 201);
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}

