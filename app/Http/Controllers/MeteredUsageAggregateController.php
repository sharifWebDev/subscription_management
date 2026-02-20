<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MeteredUsageAggregateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.metered_usage_aggregates.index');

        } catch (Exception $e) {

            info('Error showing Metered Usage Aggregates!', [$e]);

            return redirect()->back()->with('error', 'Metered Usage Aggregates showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Metered Usage Aggregates $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.metered_usage_aggregates.create');

        } catch (Exception $e) {

            info('Error showing Metered Usage Aggregates!', [$e]);

            return redirect()->back()->with('error', 'Metered Usage Aggregates showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Metered Usage Aggregates $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.metered_usage_aggregates.edit');

        } catch (Exception $e) {

            info('Error showing Metered Usage Aggregates!', [$e]);

            return redirect()->back()->with('error', 'Metered Usage Aggregates showing failed!.');

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

            return view('admin.metered_usage_aggregates.view');

        } catch (\Exception $e) {

            info('Metered Usage Aggregates data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Metered Usage Aggregates showing failed!.');
        }
    }
}
