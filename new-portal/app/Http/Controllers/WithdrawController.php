<?php

namespace App\Http\Controllers;

use App\Contracts;
use App\Withdrawals;
use Exception;
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
        $withdrawals = null;
        if(auth('customer')->check()) {
            $user = auth('customer')->user();
            $withdrawals = $user->withdrawals()
                                ->orderBy('withdrawn_at', 'desc')
                                ->paginate(20);
        } else {
            $withdrawals = Withdrawals::orderBy('withdrawn_at', 'desc')->paginate(20);
        }

        return view('withdraw.show_all', [
            'withdrawals' => $withdrawals
        ]);
    }

    /**
     * Disable a Withdrawals from database
     *
     * @param App\Withdrawals $withdraw
     * @return \Illuminate\Http\RedirectResponse
    */
    public function disableWithdrawn(Withdrawals $withdraw) {
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
     * @param \App\Withdrawals $withdraw
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    */
    public function showsWithdrawDetails(Withdrawals $withdraw) {
        $data['withdraw'] = $withdraw;
        $data['contract'] = $withdraw->contract();
        $data['current_contract'] = $withdraw->currentContract();
        $data['customers'] = $withdraw->contract()->customers();
        return view('withdraw.show_details', $data);
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
     * @param \Illuminate\Http\Request
     * @param \App\Contracts
     * @return null|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\App\Withdrawals
     */
    public function makeAWithdraw(Request $request, Contracts $contract) {
        DB::beginTransaction();
        $withdraw_value = $request->input('value');

        $request->merge([
            'id_contract' => $contract->id,
            'id_wallet' => $contract->id_wallet,
            'value' => $withdraw_value,
        ]);

        $result = Withdrawals::createByRequest($request);
        if($response = handler()->handleThis($result)->ifValidationFailsRedirect($request->url())) {
            DB::rollback();
            return $response;
        }

        // ***********************
        // TODO: generate the log
        // ***********************

        DB::commit();
        return back()->with('success_message', 'The withdraw has been successfuly saved');
    }
}
