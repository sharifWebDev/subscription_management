<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.plans.index');

        } catch (Exception $e) {

            info('Error showing Plans!', [$e]);

            return redirect()->back()->with('error', 'Plans showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Plans  $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.plans.create');

        } catch (Exception $e) {

            info('Error showing Plans!', [$e]);

            return redirect()->back()->with('error', 'Plans showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Plans  $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.plans.edit');

        } catch (Exception $e) {

            info('Error showing Plans!', [$e]);

            return redirect()->back()->with('error', 'Plans showing failed!.');

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

            return view('admin.plans.view');

        } catch (\Exception $e) {

            info('Plans data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Plans showing failed!.');
        }
    }
}
