<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetTypeRequest;
use App\Http\Requests\UpdateBudgetTypeRequest;
use App\Models\BudgetType;

class BudgetTypeController extends Controller
{
    public function index()
    {
        $budgetTypes = BudgetType::latest()->paginate(10);

        return view('budget-types.index', compact('budgetTypes'));
    }

    public function create()
    {
        return view('budget-types.create');
    }

    public function store(StoreBudgetTypeRequest $request)
    {
        BudgetType::create($request->validated());

        flash()->success('Program Type created successfully.');

        return redirect()->route('budget-types.index');
    }

    public function edit(BudgetType $budgetType)
    {
        return view('budget-types.edit', compact('budgetType'));
    }

    public function update(UpdateBudgetTypeRequest $request, BudgetType $budgetType)
    {
        $budgetType->update($request->validated());

        flash()->success('Program Type updated successfully.');

        return redirect()->route('budget-types.index');
    }

    public function destroy(BudgetType $budgetType)
    {
        if ($budgetType->documents()->exists()) {
            flash()->error('Cannot delete a Program Type that contains documents.');

            return back();
        }

        $budgetType->delete();

        flash()->success('Program Type deleted successfully.');

        return redirect()->route('budget-types.index');
    }
}
