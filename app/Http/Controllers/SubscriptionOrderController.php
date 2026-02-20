<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_orders.index');

        } catch (Exception $e) {

            info('Error showing Subscription Orders!', [$e]);

            return redirect()->back()->with('error', 'Subscription Orders showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Subscription Orders $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_orders.create');

        } catch (Exception $e) {

            info('Error showing Subscription Orders!', [$e]);

            return redirect()->back()->with('error', 'Subscription Orders showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Subscription Orders $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_orders.edit');

        } catch (Exception $e) {

            info('Error showing Subscription Orders!', [$e]);

            return redirect()->back()->with('error', 'Subscription Orders showing failed!.');

        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function show(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_orders.view');

        } catch (\Exception $e) {

            info('Subscription Orders data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Subscription Orders showing failed!.');
        }
    }
}
