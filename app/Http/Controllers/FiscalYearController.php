<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFiscalYearRequest;
use App\Http\Requests\UpdateFiscalYearRequest;
use App\Models\FiscalYear;

class FiscalYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fiscalYears = FiscalYear::latest()->paginate(10);

        return view('fiscal-years.index', compact('fiscalYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fiscal-years.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFiscalYearRequest $request)
    {
        FiscalYear::create($request->validated());

        flash()->success('Fiscal year created successfully.');

        return redirect()->route('fiscal-years.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FiscalYear $fiscalYear)
    {
        return view('fiscal-years.edit', compact('fiscalYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFiscalYearRequest $request, FiscalYear $fiscalYear)
    {
        $fiscalYear->update($request->validated());


        flash()->success('Fiscal year updated successfully.');

        return redirect()->route('fiscal-years.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FiscalYear $fiscalYear)
    {
        if ($fiscalYear->documents()->exists()) {
            flash()->error('Cannot delete a fiscal year that contains documents.');
            return back();
        }

        $fiscalYear->delete();

        flash()->success('Fiscal year deleted successfully.');

        return redirect()->route('fiscal-years.index');
    }
}
