<?php

namespace App\Http\Controllers;

use App\Contracts;
use App\Yields;
use Illuminate\Http\Request;

class YieldController extends Controller
{
    /**
     * Makes a yield and saves it on database
     *
     * @return App\Yields
     */
    public function makeAYield(Contracts $contract) {
        // TODO: make a yield and save it on database
    }

    /**
     * Shows all Yields
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showsYields() {
        // TODO: shows all the yields
        return view('yield.show_all');
    }

    /**
     * Shows the details of the yield
     *
     * @param \App\Yields $yield
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showsYieldDetails(Yields $yield) {
        // TODO: shows yield details
    }
}
