<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.discounts.index');

        } catch (Exception $e) {

            info('Error showing Discounts!', [$e]);

            return redirect()->back()->with('error', 'Discounts showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Discounts  $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.discounts.create');

        } catch (Exception $e) {

            info('Error showing Discounts!', [$e]);

            return redirect()->back()->with('error', 'Discounts showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Discounts  $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.discounts.edit');

        } catch (Exception $e) {

            info('Error showing Discounts!', [$e]);

            return redirect()->back()->with('error', 'Discounts showing failed!.');

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

            return view('admin.discounts.view');

        } catch (\Exception $e) {

            info('Discounts data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Discounts showing failed!.');
        }
    }
}
