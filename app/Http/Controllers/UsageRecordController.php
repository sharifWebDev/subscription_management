<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UsageRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.usage_records.index');

        } catch (Exception $e) {

            info('Error showing Usage Records!', [$e]);

            return redirect()->back()->with('error', 'Usage Records showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Usage Records $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.usage_records.create');

        } catch (Exception $e) {

            info('Error showing Usage Records!', [$e]);

            return redirect()->back()->with('error', 'Usage Records showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Usage Records $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.usage_records.edit');

        } catch (Exception $e) {

            info('Error showing Usage Records!', [$e]);

            return redirect()->back()->with('error', 'Usage Records showing failed!.');

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

            return view('admin.usage_records.view');

        } catch (\Exception $e) {

            info('Usage Records data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Usage Records showing failed!.');
        }
    }
}
