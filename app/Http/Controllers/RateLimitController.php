<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RateLimitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.rate_limits.index');

        } catch (Exception $e) {

            info('Error showing Rate Limits!', [$e]);

            return redirect()->back()->with('error', 'Rate Limits showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Rate Limits $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.rate_limits.create');

        } catch (Exception $e) {

            info('Error showing Rate Limits!', [$e]);

            return redirect()->back()->with('error', 'Rate Limits showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Rate Limits $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.rate_limits.edit');

        } catch (Exception $e) {

            info('Error showing Rate Limits!', [$e]);

            return redirect()->back()->with('error', 'Rate Limits showing failed!.');

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

            return view('admin.rate_limits.view');

        } catch (\Exception $e) {

            info('Rate Limits data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Rate Limits showing failed!.');
        }
    }
}
