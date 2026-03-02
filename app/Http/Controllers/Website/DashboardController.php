<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Show subscriptions page
     */
    public function dashboard()
    {
        return view('website.dashboard.index');
    }
  /**
     * Show subscriptions page
     */
    public function subscriptions()
    {
        return view('website.dashboard.subscriptions');
    }

    /**
     * Show invoices page
     */
    public function invoices()
    {
        return view('website.dashboard.invoices');
    }

    /**
     * Show payment methods page
     */
    public function paymentMethods()
    {
        return view('website.dashboard.payment-methods');
    }

    /**
     * Show usage statistics page
     */
    public function usage()
    {
        return view('website.dashboard.usage');
    }

    /**
     * Show profile page
     */
    public function profile()
    {
        return view('website.dashboard.profile');
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        return view('website.dashboard.settings');
    }
}
