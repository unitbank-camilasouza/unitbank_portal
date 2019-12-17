<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts;
use App\CoWalletsJunctions;
use App\CurrentContracts;
use App\Customers;
use App\Wallets;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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
        $this->middleware('auth:consultant');

        // ***********************
        // TODO: generate the log
        // ***********************

        // transaction block:
        return DB::transaction(function () use ($request) {
            $result = handler()->handleThis(Contracts::createByRequest($request));

            if($response = $result->ifValidationFailsRedirect('/'))
                return $response;

            DB::commit();
            return redirect()->route('home')->with('success_message', 'Contract successly saved');
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

        // TODO: makes a inner join with customer
        return view('contract.show_details', ['contract' => $contract]);
    }

    /**
     * Gets all Contracts from the current user, a specifc user or all contracts
     *
     * @param Illuminate\Http\Request $request
     * @param null|App\Customers $customer
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    */
    public function showsContracts(Request $request) {
        $this->middleware('auth');

        $contracts = [];
        if ($request->ajax()) {
            if(auth('customer')->check()) {
                $customer = Customers::findOrFail(auth('customer')->id());
                $contracts = $customer->contracts();
            } else {
                $contracts = CurrentContracts::join('Contracts', 'Contracts.id', 'CurrentContracts.id')
                                               ->limit(200)->paginate(20);
            }

            return response()->json($contracts);
        } else if (auth('consultant')->check() || auth('admin')->check()) {
            $contracts = DB::table('Contracts')
                             ->join('CoWalletsJunctions',
                                    'CoWalletsJunctions.id_wallet', '=', 'Contracts.id_wallet')
                             ->join('Customers',
                                    'Customers.id', '=', 'CoWalletsJunctions.id_customer')
                             ->get();
        } else if (auth('customer')->check()) {
            $customer = Customers::findOrFail(auth('customer')->id());
            $contracts = $customer->contracts();
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
}