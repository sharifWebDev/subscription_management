<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.payment_masters.index');

        } catch (Exception $e) {

            info('Error showing Payment Masters!', [$e]);

            return redirect()->back()->with('error', 'Payment Masters showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Payment Masters $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.payment_masters.create');

        } catch (Exception $e) {

            info('Error showing Payment Masters!', [$e]);

            return redirect()->back()->with('error', 'Payment Masters showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Payment Masters $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.payment_masters.edit');

        } catch (Exception $e) {

            info('Error showing Payment Masters!', [$e]);

            return redirect()->back()->with('error', 'Payment Masters showing failed!.');

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

            return view('admin.payment_masters.view');

        } catch (\Exception $e) {

            info('Payment Masters data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Payment Masters showing failed!.');
        }
    }
}
