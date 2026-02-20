<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.payments.index');

        } catch (Exception $e) {

            info('Error showing Payments!', [$e]);

            return redirect()->back()->with('error', 'Payments showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Payments  $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.payments.create');

        } catch (Exception $e) {

            info('Error showing Payments!', [$e]);

            return redirect()->back()->with('error', 'Payments showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Payments  $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.payments.edit');

        } catch (Exception $e) {

            info('Error showing Payments!', [$e]);

            return redirect()->back()->with('error', 'Payments showing failed!.');

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

            return view('admin.payments.view');

        } catch (\Exception $e) {

            info('Payments data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Payments showing failed!.');
        }
    }
}
