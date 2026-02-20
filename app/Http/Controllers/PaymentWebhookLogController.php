<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentWebhookLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.payment_webhook_logs.index');

        } catch (Exception $e) {

            info('Error showing Payment Webhook Logs!', [$e]);

            return redirect()->back()->with('error', 'Payment Webhook Logs showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Payment Webhook Logs $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.payment_webhook_logs.create');

        } catch (Exception $e) {

            info('Error showing Payment Webhook Logs!', [$e]);

            return redirect()->back()->with('error', 'Payment Webhook Logs showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Payment Webhook Logs $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.payment_webhook_logs.edit');

        } catch (Exception $e) {

            info('Error showing Payment Webhook Logs!', [$e]);

            return redirect()->back()->with('error', 'Payment Webhook Logs showing failed!.');

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

            return view('admin.payment_webhook_logs.view');

        } catch (\Exception $e) {

            info('Payment Webhook Logs data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Payment Webhook Logs showing failed!.');
        }
    }
}
