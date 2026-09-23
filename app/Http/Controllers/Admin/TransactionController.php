<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);
        $totalTopupUsd = Transaction::where('type', 'topup')->sum('amount_usd');

        return view('admin.transactions.index', compact('transactions', 'totalTopupUsd'));
    }
}
