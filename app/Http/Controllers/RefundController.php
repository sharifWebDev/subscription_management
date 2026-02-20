<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RefundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.refunds.index');

        } catch (Exception $e) {

            info('Error showing Refunds!', [$e]);

            return redirect()->back()->with('error', 'Refunds showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Refunds  $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.refunds.create');

        } catch (Exception $e) {

            info('Error showing Refunds!', [$e]);

            return redirect()->back()->with('error', 'Refunds showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Refunds  $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.refunds.edit');

        } catch (Exception $e) {

            info('Error showing Refunds!', [$e]);

            return redirect()->back()->with('error', 'Refunds showing failed!.');

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

            return view('admin.refunds.view');

        } catch (\Exception $e) {

            info('Refunds data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Refunds showing failed!.');
        }
    }
}
