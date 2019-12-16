<?php

namespace App\Http\Controllers;

use App\Contracts;
use Illuminate\Http\Request;
use App\CoWalletsJunctions;
use App\Wallets;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $contracts = [];

        if(auth('consultant')->check() || auth('admin')->check()) {
            $contracts = Contracts::join('CurrentContracts', 'CurrentContracts.id', 'Contracts.id')
                            ->get();
        } else if (auth('customer')->check()) {
            $co_wallet_junction = CoWalletsJunctions::where(
                'id_customer', auth('customer')->id()
            )->first();

            $wallet = Wallets::findOrFail($co_wallet_junction->id_wallet);
            $contracts = Contracts::where('id_wallet', $wallet->id)
                                    ->crossJoin('CurrentContracts')->paginate(20);
        }

        return view('home', ['contracts' => $contracts]);
    }
}
