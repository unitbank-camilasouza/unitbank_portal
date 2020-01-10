<?php
/**
 * This Controller is responsible to be the intermediate
 * between the soliciations view and solicitations Models
 *
 * Solicitations included:
 *  - Cancel
 *  - Withdraw
 *  - Contract
 */

namespace App\Http\Controllers;

use App\CancellationsSolicitations;
use App\WithdrawalsSolicitations;
use Illuminate\Http\Request;

class SolicitationController extends Controller
{
    /**
     * Constructor of this class
     *
     * Defines all the middlewares of this class
     */
    public function __construct() {
        $this->middleware('auth')->except('makeContractSolicitation');
        $this->middleware('auth:customer')->except('makeContractSolicitation');
    }

    /**
     * Makes a Withdraw Solicitation
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeWithdrawSolicitation(Request $request) {
        $request->merge([
            'id_customer' => auth('customer')->id()
        ]);

        $creation_result = WithdrawalsSolicitations::createByRequest($request);
        if ($response = handler($creation_result)->ifValidationFailsRedirect($request->url())) {
            return $response->withErrors($creation_result);
        }

        if($request->ajax())
            return response()->json($creation_result);

        return back()->with('success_message', 'Withdraw solicitation successfuly sended');
    }

    /**
     * Makes a Contract Solicitation
     *
     * @param \Illuminate\Http\Request $request
     */
    public function makeContractSolicitation(Request $request) {
        // TODO: makes a contract soliciation and save it on DB
    }

    /**
     * Makes a Cancel Solictation
     *
     * @param \Illuminate\Http\Request $request
     */
    public function makeCancelSolicitation(Request $request) {
        $request->merge([
            'id_customer' => auth('customer')->id()
        ]);

        $creation_result = CancellationsSolicitations::createByRequest($request);
        if ($response = handler($creation_result)->ifValidationFailsRedirect($request->url())) {
            return $response->withErrors($creation_result);
        }

        if($request->ajax())
            return response()->json($creation_result);

        return back()->with('success_message', 'Cancelation solicitation successfuly sended');
    }
}
