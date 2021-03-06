<?php

namespace App\Http\Controllers;

use App\Contracts;
use Illuminate\Http\Request;
use App\CoWalletsJunctions;
use App\Customers;
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
    public function index() {
        $contracts = [];

        if(auth('consultant')->check() || auth('admin')->check()) {
            $contracts = Contracts::join('CurrentContracts', 'CurrentContracts.id', 'Contracts.id')
                                  ->paginate(15);
        } else if (auth('customer')->check()) {
            $contracts = auth('customer')->user()->currentContracts()->paginate(15);
        }

        return view('home', ['contracts' => $contracts]);
    }
}
