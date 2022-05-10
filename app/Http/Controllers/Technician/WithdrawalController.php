<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Http\Resources\WithdrawalResource;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return WithdrawalResource::collection(
            executeQuery(Withdrawal::query()
                ->where('user_id', Auth::user()->id)
                ->with('user')
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return WithdrawalResource
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        switch ($request->type) {
            case 'wow':
                $max = $user->wow_wallet;
                break;
            case 'tip':
                $max = $user->tip_wallet;
                break;
            case 'commission':
                $max = $user->commission_wallet;
                break;
            default:
                $max = 0;
        }

        $data = $request->validate([
            'type' => 'required|in:wow,tip,commission',
            'amount' => 'required|numeric|min:0|max:'.$max,
        ]);

        $data['user_id'] = $user->id;

        return new WithdrawalResource(Withdrawal::create($data));
    }
}
