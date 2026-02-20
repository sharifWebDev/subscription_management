<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.plan_features.index');

        } catch (Exception $e) {

            info('Error showing Plan Features!', [$e]);

            return redirect()->back()->with('error', 'Plan Features showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Plan Features $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.plan_features.create');

        } catch (Exception $e) {

            info('Error showing Plan Features!', [$e]);

            return redirect()->back()->with('error', 'Plan Features showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Plan Features $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.plan_features.edit');

        } catch (Exception $e) {

            info('Error showing Plan Features!', [$e]);

            return redirect()->back()->with('error', 'Plan Features showing failed!.');

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

            return view('admin.plan_features.view');

        } catch (\Exception $e) {

            info('Plan Features data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Plan Features showing failed!.');
        }
    }
}
