<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.payment_gateways.index');

        } catch (Exception $e) {

            info('Error showing Payment Gateways!', [$e]);

            return redirect()->back()->with('error', 'Payment Gateways showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Payment Gateways $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.payment_gateways.create');

        } catch (Exception $e) {

            info('Error showing Payment Gateways!', [$e]);

            return redirect()->back()->with('error', 'Payment Gateways showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Payment Gateways $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.payment_gateways.edit');

        } catch (Exception $e) {

            info('Error showing Payment Gateways!', [$e]);

            return redirect()->back()->with('error', 'Payment Gateways showing failed!.');

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

            return view('admin.payment_gateways.view');

        } catch (\Exception $e) {

            info('Payment Gateways data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Payment Gateways showing failed!.');
        }
    }
}
