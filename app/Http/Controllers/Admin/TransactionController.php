<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $relations = [
            'package', // nama function yang ada di model
            'user'
        ];
        $transaction = Transaction::with($relations)->get();
        return view('admin.transaction', ['transactions' => $transaction]);
    }
}
