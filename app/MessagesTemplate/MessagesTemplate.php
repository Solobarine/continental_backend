<?php

namespace App\MessagesTemplate;

class MessagesTemplate
{
    public static function message()
    {
        return [
            'welcome_message' => function ($first_name) {
                return 'Welcome to Continental, ' . $first_name . '. We hope this will be a start to a beautiful partnership. Thank you for choosing us.';
            },
            'deposit_error' => function ($reason) {
                if ($reason === 'Unauthorized Transaction.') {
                    return 'Your Deposit request could not be processed. This is because You are Unauthorized to carry out this request';
                }

                return 'Transaction Error. Try again later';
            },
            'deposit_success' => function ($amount) {
                return 'Your Deposit request has been completed. $' . $amount . ' has been added to your Account Balance. Thank you for choosing Continental.';
            },
            'transfer_error' => function ($reason) {
                if ($reason === 'This Account does not exist. Transaction Failed') {
                    return 'This Transfer request could not be processed. The Receiver does not exist. Check the account number and Try Again.';
                }

                if ($reason === 'Unauthorized Transaction') {
                    return 'Your Transfer request could not be completed. This is an Unauthorized Request.';
                }

                if ($reason === 'Insufficient Funds') {
                    return 'Your Transfer Request has been cancelled due to Insufficient Funds. Top Up your Account and Try Again.';
                }

                return 'Transfer Error. Try Again Later';
            },
            'transfer_success' => function ($amount, $name) {
                return 'Your Transfer Request has been completed. $' . $amount . ' has been sent to ' . $name['first'] . ' ' . $name['last'] . '. Have a wonderful day';
            },
            'credit_success' => function ($details) {
                return $details['first'] . ' ' . $details['last'] . '  Transferred $' . $details['amount'] . ' to your account. Your balance is now $' . $details['balance'] . '.';
            },
            'card_error' => function ($reason) {
                if ($reason === 'Invalid Provider') {
                    return 'Sorry, Your Card could not be created. Please select Visa or MasterCard as your Card Provider';
                }

                return 'An Error Occurred. Could Not Create Your Card';
            },
            'card_success' => function () {
                return 'Your Debit Card has been created. You can now send money to your loved ones, pay bills and conduct business. Please do not disclose your confidential details to anyone. Have a Nice Day.';
            },
            'profile_update' => function () {
                return 'Your Profile has been updated successfully';
            },
            'email_update' => function ($reason) {
                return 'Your Account Email has been updated successfully.';
            },
            'password_update' => function ($reason) {
                return 'Your Account Password Updated Successfully';
            }
        ];
    }
}