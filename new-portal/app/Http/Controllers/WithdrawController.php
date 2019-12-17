<?php

namespace App\Http\Controllers;

use App\Contracts;
use App\Withdrawals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    /**
     * Constructor, verifies if the user is authenticated
     *
     * @return void
    */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Shows all withdrawals
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showsWithdraws() {
        return view('withdraw.show_all');
    }

    /**
     * Saves a new Withdraw in database
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
    */
    public function saveWithdraw(Request $request) {
        $this->middleware('auth:consultant');

        // transaction block:
        return DB::transaction(function () use ($request) {
            $result = handler()->handleThis(Withdrawals::createByRequest($request));

            if($response = $result->ifValidationFailsRedirect('/'))
                return $response;

            DB::commit();
            return redirect()->route('home')->with('success_message', 'Withdraw successly saved');
        });
    }

    /**
     * Disable a Withdrawals from database
     *
     * @param Illuminate\Http\Request $request
     * @param App\Withdrawals $withdraw
     * @return \Illuminate\Http\RedirectResponse
    */
    public function disableWithdrawals(Request $request, Withdrawals $withdraw) {
        $this->middleware('auth:consultant');

        // ***********************
        // TODO: generate the log
        // ***********************

        $withdraw->delete();

        return back()->with('success_message', 'Withdraw disabled successfuly');
    }

    /**
     * Shows the details of the Withdraw instance
     *
     * @param App\Withdrawals $withdraw
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    */
    public function showsWithdrawDetails(Withdrawals $withdraw) {
        // TODO: makes a inner join with customer
        return view('withdraw.show_details', ['withdraw' => $withdraw]);
    }

    /**
     * Shows the form to make a withdraw
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showWithdrawForm(?Contracts $contract = null) {
        return view('withdraw.make_withdraw', ['contract' => $contract]);
    }

    /**
     * Makes a withdraw of a Contract and save it on the database
     *
     * @return App\Withdrawals
     */
    public function makeAWithdraw(Contracts $contract) {
        // TODO: make the withdraw
    }
}
