<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CheckoutViewController extends Controller
{
    /**
     * Display checkout page.
     */
    public function index($plan_id)
    {
        return view('website.checkout.index', compact('plan_id'));
    }

    /**
     * Process checkout.
     */
    public function process(Request $request)
    {
        $request->validate([
            'plan_id' => 'required',
            'price_id' => 'required',
            'payment_method' => 'required|in:card,paypal,bank_transfer',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'terms' => 'required|accepted',
        ]);

        // Here you would typically call your API to create the subscription
        // For now, we'll redirect to success page

        return redirect()->route('website.plans.index')
            ->with('success', 'Subscription created successfully!');
    }
}
