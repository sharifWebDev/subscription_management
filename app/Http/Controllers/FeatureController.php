<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.features.index');

        } catch (Exception $e) {

            info('Error showing Features!', [$e]);

            return redirect()->back()->with('error', 'Features showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Features  $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.features.create');

        } catch (Exception $e) {

            info('Error showing Features!', [$e]);

            return redirect()->back()->with('error', 'Features showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Features  $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.features.edit');

        } catch (Exception $e) {

            info('Error showing Features!', [$e]);

            return redirect()->back()->with('error', 'Features showing failed!.');

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

            return view('admin.features.view');

        } catch (\Exception $e) {

            info('Features data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Features showing failed!.');
        }
    }
}
