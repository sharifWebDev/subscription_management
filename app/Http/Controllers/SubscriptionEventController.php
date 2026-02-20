<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_events.index');

        } catch (Exception $e) {

            info('Error showing Subscription Events!', [$e]);

            return redirect()->back()->with('error', 'Subscription Events showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Subscription Events $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_events.create');

        } catch (Exception $e) {

            info('Error showing Subscription Events!', [$e]);

            return redirect()->back()->with('error', 'Subscription Events showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Subscription Events $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_events.edit');

        } catch (Exception $e) {

            info('Error showing Subscription Events!', [$e]);

            return redirect()->back()->with('error', 'Subscription Events showing failed!.');

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

            return view('admin.subscription_events.view');

        } catch (\Exception $e) {

            info('Subscription Events data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Subscription Events showing failed!.');
        }
    }
}
