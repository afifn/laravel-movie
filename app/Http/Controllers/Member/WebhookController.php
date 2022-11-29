<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\UserPremium;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WebhookController extends Controller
{
    public function handler(Request $request)
    {
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        $notif = new \Midtrans\Notification();

        $transactionStatus = $notif->transaction_status;
        $orderId = $notif->order_id;
        $fraudStatus = $notif->fraud_status;

        $status = '';
        // Sample transactionStatus handling logic
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $status = 'challenge';
            } else if ($fraudStatus == 'accept') {
                $status = 'success';
            }
        } else if ($transactionStatus == 'settlement') {
            $status = 'settlement';
        } else if (
            $transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire'
        ) {
            $status = 'failure';
        } else if ($transactionStatus == 'pending') {
            $status = 'pending';
        }

        $transactions = Transaction::with('package')->where('transaction_code', $orderId)->first();

        if ($status === 'success') {
            $userPremium = UserPremium::where('user_id', $transactions->user_id)->first();

            // cek userPremiun masih premium
            if ($userPremium) {
                //renewal subscription
                $endOfSubscription = $userPremium->end_of_subscription;
                $date = Carbon::createFromFormat('Y-m-d', $endOfSubscription); // ubah ke object carbon
                $newEndOfSubscription = $date->addDays($transactions->package->max_days)->format('Y-m-d'); // tambah hari ke end_of_subscription

                $userPremium->update([
                    'package_id' => $transactions->package->id,
                    'end_of_subscription' => $newEndOfSubscription
                ]);
            } else {
                UserPremium::create([
                    'package_id' => $transactions->package->id,
                    'user_id' => $transactions->user_id,
                    'end_of_subscription' => now()->addDays($transactions->package->max_days),
                ]);
            }
        }

        $transactions->update(['status' => $status]);

        return response()->json(null);
    }
}
