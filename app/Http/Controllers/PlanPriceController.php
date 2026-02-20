<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.plan_prices.index');

        } catch (Exception $e) {

            info('Error showing Plan Prices!', [$e]);

            return redirect()->back()->with('error', 'Plan Prices showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Plan Prices $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.plan_prices.create');

        } catch (Exception $e) {

            info('Error showing Plan Prices!', [$e]);

            return redirect()->back()->with('error', 'Plan Prices showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Plan Prices $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.plan_prices.edit');

        } catch (Exception $e) {

            info('Error showing Plan Prices!', [$e]);

            return redirect()->back()->with('error', 'Plan Prices showing failed!.');

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

            return view('admin.plan_prices.view');

        } catch (\Exception $e) {

            info('Plan Prices data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Plan Prices showing failed!.');
        }
    }
}
