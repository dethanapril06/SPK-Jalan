<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreCriteriaRequest;
use App\Http\Requests\UpdateCriteriaRequest;
use App\Http\Controllers\Controller;
use App\Models\Criteria;

class CriteriaController extends Controller
{
    /**
         * Display a listing of the resource.
         */
    public function index()
    {
        $criterias = Criteria::orderBy('order')->get();
        return view('admin.criteria.index', compact('criterias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.criteria.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCriteriaRequest $request)
    {
        if (! $this->isTotalWeightValid((float) $request->weight)) {
            return back()
                ->withInput()
                ->withErrors(['weight' => 'Total bobot semua kriteria tidak boleh lebih dari 1.00.']);
        }

        Criteria::create($request->only('code', 'name', 'description', 'weight', 'order'));

        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Criteria $criteria)
    {
        return view('admin.criteria.show', compact('criteria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Criteria $criteria)
    {
        return view('admin.criteria.edit', compact('criteria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCriteriaRequest $request, Criteria $criteria)
    {
        if (! $this->isTotalWeightValid((float) $request->weight, $criteria)) {
            return back()
                ->withInput()
                ->withErrors(['weight' => 'Total bobot semua kriteria tidak boleh lebih dari 1.00.']);
        }

        $criteria->update($request->only('code', 'name', 'description', 'weight', 'order'));

        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Criteria $criteria)
    {
        $criteria->delete();
        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }

    private function isTotalWeightValid(float $weight, ?Criteria $excludeCriteria = null): bool
    {
        $query = Criteria::query();

        if ($excludeCriteria) {
            $query->whereKeyNot($excludeCriteria->id);
        }

        $totalExistingWeight = (float) $query->sum('weight');

        return ($totalExistingWeight + $weight) <= 1.0;
    }
}
