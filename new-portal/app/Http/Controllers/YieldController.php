<?php

namespace App\Http\Controllers;

use App\Contracts;
use App\Yields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YieldController extends Controller
{
    /**
     * Makes a yield and saves it on database
     *
     * @return \App\Yields
     */
    public function makeAYield(Request $request, Contracts $contract) {
        DB::beginTransaction();
        $new_yield = Yields::createByRequest($request);

        // verifies if an invalidating error has occurred
        if($response = handler($new_yield)->ifValidationFailsRedirect('/')) {
            DB::rollBack();
            return $response->withErrors($new_yield);
        }
        DB::commit();

        // else, all has been saved correctely
        return back()->with('success_message', 'Yield saved successfully');
    }

    /**
     * Shows all Yields
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showsYields(Request $request) {
        $yields = null;
        if(auth('customer')->check()) {
            $yields = auth('customer')->user()->yields()->paginate(20);
        } else {
            $yields = Yields::paginate(20);
        }

        // TODO: shows all the yields
        return view('yield.show_all', [
            'yields' => $yields
        ]);
    }

    /**
     * Shows the details of the yield
     *
     * @param \App\Yields $yield
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showsYieldDetails(Yields $yield) {
        return view('yield.show_details', ['yield' => $yield]);
    }

    /**
     * Disable a Yields from database
     *
     * @param Illuminate\Http\Request $request
     * @param App\Yields $yield
     * @return \Illuminate\Http\RedirectResponse
    */
    public function disableYield(Request $request, Yields $yield) {
        $this->middleware('auth:consultant');

        // ***********************
        // TODO: generate the log
        // ***********************

        $yield->delete();

        return back()->with('success_message', 'Yield disabled successfuly');
    }
}
