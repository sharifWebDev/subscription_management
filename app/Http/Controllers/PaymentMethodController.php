<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.payment_methods.index');

        } catch (Exception $e) {

            info('Error showing Payment Methods!', [$e]);

            return redirect()->back()->with('error', 'Payment Methods showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Payment Methods $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.payment_methods.create');

        } catch (Exception $e) {

            info('Error showing Payment Methods!', [$e]);

            return redirect()->back()->with('error', 'Payment Methods showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Payment Methods $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.payment_methods.edit');

        } catch (Exception $e) {

            info('Error showing Payment Methods!', [$e]);

            return redirect()->back()->with('error', 'Payment Methods showing failed!.');

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

            return view('admin.payment_methods.view');

        } catch (\Exception $e) {

            info('Payment Methods data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Payment Methods showing failed!.');
        }
    }
}
