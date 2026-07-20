<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGovernmentLevelRequest;
use App\Http\Requests\UpdateGovernmentLevelRequest;
use App\Models\GovernmentLevel;

class GovernmentLevelController extends Controller
{
    public function index()
    {
        $governmentLevels = GovernmentLevel::latest()->paginate(10);

        return view('government-levels.index', compact('governmentLevels'));
    }

    public function create()
    {
        return view('government-levels.create');
    }

    public function store(StoreGovernmentLevelRequest $request)
    {
        GovernmentLevel::create($request->validated());

        flash()->success('Created successfully.');

        return redirect()->route('government-levels.index');
    }

    public function edit(GovernmentLevel $governmentLevel)
    {
        return view('government-levels.edit', compact('governmentLevel'));
    }

    public function update(UpdateGovernmentLevelRequest $request, GovernmentLevel $governmentLevel)
    {
        $governmentLevel->update($request->validated());

        flash()->success('Updated successfully.');

        return redirect()->route('government-levels.index');
    }

    public function destroy(GovernmentLevel $governmentLevel)
    {
        if ($governmentLevel->documents()->exists()) {
            flash()->error('Cannot delete a department that contains documents.');

            return back();
        }

        $governmentLevel->delete();

        flash()->success('Deleted successfully.');

        return redirect()->route('government-levels.index');
    }
}
