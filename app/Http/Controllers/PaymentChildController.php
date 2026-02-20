<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentChildController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.payment_children.index');

        } catch (Exception $e) {

            info('Error showing Payment Children!', [$e]);

            return redirect()->back()->with('error', 'Payment Children showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Payment Children $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.payment_children.create');

        } catch (Exception $e) {

            info('Error showing Payment Children!', [$e]);

            return redirect()->back()->with('error', 'Payment Children showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Payment Children $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.payment_children.edit');

        } catch (Exception $e) {

            info('Error showing Payment Children!', [$e]);

            return redirect()->back()->with('error', 'Payment Children showing failed!.');

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

            return view('admin.payment_children.view');

        } catch (\Exception $e) {

            info('Payment Children data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Payment Children showing failed!.');
        }
    }
}
