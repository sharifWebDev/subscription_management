<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.subscriptions.index');

        } catch (Exception $e) {

            info('Error showing Subscriptions!', [$e]);

            return redirect()->back()->with('error', 'Subscriptions showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Subscriptions  $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.subscriptions.create');

        } catch (Exception $e) {

            info('Error showing Subscriptions!', [$e]);

            return redirect()->back()->with('error', 'Subscriptions showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Subscriptions  $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.subscriptions.edit');

        } catch (Exception $e) {

            info('Error showing Subscriptions!', [$e]);

            return redirect()->back()->with('error', 'Subscriptions showing failed!.');

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

            return view('admin.subscriptions.view');

        } catch (\Exception $e) {

            info('Subscriptions data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Subscriptions showing failed!.');
        }
    }
}
