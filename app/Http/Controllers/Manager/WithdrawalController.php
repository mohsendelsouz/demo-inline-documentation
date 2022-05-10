<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\WithdrawalResource;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        return WithdrawalResource::collection(
            executeQuery(Withdrawal::query()->with('user'))
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|in:wow,tip,commission',
            'amount' => 'required|numeric|min:0',
        ]);

        $withdrawal = Withdrawal::create($data);
        $this->acceptWithdrawal($withdrawal);

        return new WithdrawalResource($withdrawal);
    }

    public function reject(Withdrawal $withdrawal)
    {
        if ($withdrawal->status === 0) {
            $withdrawal->status = 2;
            return $withdrawal->save();
        }

        return false;
    }

    public function acceptWithdrawal(Withdrawal $withdrawal)
    {
        $user = User::find($withdrawal->user_id);

        Transaction::create([
            'user_id' => $withdrawal->user_id,
            'date' => Carbon::now(),
            'type' => ucwords($withdrawal->type. ' Withdrawal'),
            'amount' => $withdrawal->amount * -1
        ]);

        switch ($withdrawal->type) {
            case 'wow':
                $user->decrement('wow_wallet', $withdrawal->amount);
                break;
            case 'tip':
                $user->decrement('tip_wallet', $withdrawal->amount);
                break;
            case 'commission':
                $user->decrement('commission_wallet', $withdrawal->amount);
                break;
        }

        $withdrawal->status = 1;

        return $withdrawal->save();
    }
}
