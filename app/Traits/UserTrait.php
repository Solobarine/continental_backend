<?php

namespace App\Traits;

use App\Models\Transfer;
use App\Models\Deposit;
use Illuminate\Support\Facades\Auth;

trait UserTrait
{
    public function recents()
    {
        $user = Auth::user();
        $deposits = Deposit::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(5)->get();

        $transfers = Transfer::join('users', 'users.id', '=', 'transfers.payee_id')->where('user_id', $user->id)->orWhere('payee_id', $user->id)->orderBy('created_at', 'desc')->take(5)->get(['transfers.*', 'users.first_name', 'users.last_name']);

        return ['deposits' => $deposits, 'transfers' => $transfers];
    }
}
