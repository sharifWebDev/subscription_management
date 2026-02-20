<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_items.index');

        } catch (Exception $e) {

            info('Error showing Subscription Items!', [$e]);

            return redirect()->back()->with('error', 'Subscription Items showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Subscription Items $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_items.create');

        } catch (Exception $e) {

            info('Error showing Subscription Items!', [$e]);

            return redirect()->back()->with('error', 'Subscription Items showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Subscription Items $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.subscription_items.edit');

        } catch (Exception $e) {

            info('Error showing Subscription Items!', [$e]);

            return redirect()->back()->with('error', 'Subscription Items showing failed!.');

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

            return view('admin.subscription_items.view');

        } catch (\Exception $e) {

            info('Subscription Items data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Subscription Items showing failed!.');
        }
    }
}
