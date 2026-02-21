<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;

class CheckoutViewController extends Controller
{
    /**
     * Display checkout page.
     */
    public function index($plan_id)
    {
        return view('website.checkout.index', compact('plan_id'));
    }
}
