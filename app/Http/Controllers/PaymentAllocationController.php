<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentAllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.payment_allocations.index');

        } catch (Exception $e) {

            info('Error showing Payment Allocations!', [$e]);

            return redirect()->back()->with('error', 'Payment Allocations showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Payment Allocations $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.payment_allocations.create');

        } catch (Exception $e) {

            info('Error showing Payment Allocations!', [$e]);

            return redirect()->back()->with('error', 'Payment Allocations showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Payment Allocations $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.payment_allocations.edit');

        } catch (Exception $e) {

            info('Error showing Payment Allocations!', [$e]);

            return redirect()->back()->with('error', 'Payment Allocations showing failed!.');

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

            return view('admin.payment_allocations.view');

        } catch (\Exception $e) {

            info('Payment Allocations data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Payment Allocations showing failed!.');
        }
    }
}
