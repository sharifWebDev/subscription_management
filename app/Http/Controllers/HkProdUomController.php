<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HkProdUomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {

            return view('admin.hk_prod_uoms.index');

        } catch (Exception $e) {

            info('Error showing Hk Prod Uoms!', [$e]);

            return redirect()->back()->with('error', 'Hk Prod Uoms showing failed!.');

        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Hk Prod Uoms $singularTableName
     * @return \Illuminate\View\View
     */
    public function create(): View|RedirectResponse
    {
        try {

            return view('admin.hk_prod_uoms.create');

        } catch (Exception $e) {

            info('Error showing Hk Prod Uoms!', [$e]);

            return redirect()->back()->with('error', 'Hk Prod Uoms showing failed!.');

        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  Request  $request
     * @param  Hk Prod Uoms $singularTableName
     * @return \Illuminate\View\View
     */
    public function edit(): View|RedirectResponse
    {
        try {

            return view('admin.hk_prod_uoms.edit');

        } catch (Exception $e) {

            info('Error showing Hk Prod Uoms!', [$e]);

            return redirect()->back()->with('error', 'Hk Prod Uoms showing failed!.');

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

            return view('admin.hk_prod_uoms.view');

        } catch (\Exception $e) {

            info('Hk Prod Uoms data showing failed!', [$e]);

            return redirect()->back()->with('error', 'Hk Prod Uoms showing failed!.');
        }
    }
}
