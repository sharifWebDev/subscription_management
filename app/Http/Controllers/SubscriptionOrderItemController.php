<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionOrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_order_items.index');

        } catch (Exception $e) {

            info('Error showing Subscription Order Items!', [$e]);

            return redirect()->back()->with('error', 'Subscription Order Items showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Subscription Order Items $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_order_items.create');

        } catch (Exception $e) {

            info('Error showing Subscription Order Items!', [$e]);

            return redirect()->back()->with('error', 'Subscription Order Items showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Subscription Order Items $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_order_items.edit');

        } catch (Exception $e) {

            info('Error showing Subscription Order Items!', [$e]);

            return redirect()->back()->with('error', 'Subscription Order Items showing failed!.');

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

            return view('admin.subscription_order_items.view');

        } catch (\Exception $e) {

            info('Subscription Order Items data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Subscription Order Items showing failed!.');
        }
    }
}
