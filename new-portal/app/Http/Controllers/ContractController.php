<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts;
use App\CoWalletsJunctions;
use App\CurrentContracts;
use App\Customers;
use App\Users;
use App\Wallets;
use Exception;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    /**
     * Constructor, verifies if the user is authenticated
     *
     * @return void
    */
    public function __construct() {
        $this->middleware('auth');  // verifies if the user is authenticated
    }

    /**
     * Saves a new contract in database
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
    */
    public function saveContract(Request $request) {
        // ***********************
        // TODO: generate the log
        // ***********************

        // transaction block:
        return DB::transaction(function () use ($request) {
            $result = Contracts::createByRequest($request);

            if($response = handler()->handleThis($result)->ifValidationFailsRedirect('/')) {
                DB::rollBack();
                return $response;
            }

            DB::commit();
            return back()->with('success_message', 'Contract successly saved');
        });
    }

    /**
     * Disable a Contract from database
     *
     * @param Illuminate\Http\Request $request
     * @param App\Contracts $contract
     * @return \Illuminate\Http\RedirectResponse
    */
    public function disableContract(Request $request, Contracts $contract) {
        $this->middleware('auth:consultant');

        // ***********************
        // TODO: generate the log
        // ***********************

        $contract->delete();

        return back()->with('success_message', 'Contract disabled successfuly');
    }

    /**
     * Shows the details of the Contract instance
     *
     * @param App\Contracts $contract
    */
    public function showsContractDetails(Contracts $contract) {
        $data['contract'] = $contract;
        $data['current_contract'] = $contract->currentContract();
        $data['customers'] = $contract->customers()->get();
        $data['withdrawals'] = $contract->withdrawals()->get();
        $data['yields'] = $contract->yields()->get();
        return view('contract.show_details', $data);
    }

    /**
     * Gets all Contracts from the current user, a specifc user or all contracts
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    */
    public function showsContracts(Request $request) {
        $this->middleware('auth');

        $contracts = [];
        if ($request->ajax()) {
            return $this->ajaxAllContractsResponse();
        } else if(auth('consultant')->check() || auth('admin')->check()) {
            $contracts = Contracts::join('CurrentContracts', 'CurrentContracts.id', 'Contracts.id')
                            ->get();
        } else if (auth('customer')->check()) {
            $contracts = auth('customer')->user()->currentContracts()->paginate(15);
        } else {
            throw new Exception('Incorrect values to get contract.');
        }

        return view('contract.show_all', ['contracts' => $contracts]);
    }

    /**
     * Shows the save contract form
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showSaveContractForm () {
        return view('contract.save_form');
    }

    /**
     * Returns a ajax response of all contracts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxAllContractsResponse() {
        if(auth('customer')->check()) {
            $customer = Customers::findOrFail(auth('customer')->id());
            $contracts = $customer->contracts();
        } else {
            $contracts = CurrentContracts::join('Contracts', 'Contracts.id', 'CurrentContracts.id')
                                           ->limit(200)->paginate(20);
        }

        return response()->json($contracts);
    }
}
