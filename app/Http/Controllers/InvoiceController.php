<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.invoices.index');

        } catch (Exception $e) {

            info('Error showing Invoices!', [$e]);

            return redirect()->back()->with('error', 'Invoices showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Invoices  $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.invoices.create');

        } catch (Exception $e) {

            info('Error showing Invoices!', [$e]);

            return redirect()->back()->with('error', 'Invoices showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Invoices  $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.invoices.edit');

        } catch (Exception $e) {

            info('Error showing Invoices!', [$e]);

            return redirect()->back()->with('error', 'Invoices showing failed!.');

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

            return view('admin.invoices.view');

        } catch (\Exception $e) {

            info('Invoices data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Invoices showing failed!.');
        }
    }
}
