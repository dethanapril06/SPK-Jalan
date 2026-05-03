<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubCriteriaRequest;
use App\Http\Requests\UpdateSubCriteriaRequest;
use App\Models\Criteria;
use App\Models\SubCriteria;

class SubCriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subCriterias = SubCriteria::with('criteria')
            ->orderBy('criteria_id')
            ->orderBy('order')
            ->get();

        return view('admin.sub-criteria.index', compact('subCriterias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $criterias = Criteria::orderBy('order')->get();

        return view('admin.sub-criteria.create', compact('criterias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubCriteriaRequest $request)
    {
        SubCriteria::create($request->validated());

        return redirect()->route('admin.sub-criteria.index')
            ->with('success', 'Sub kriteria berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCriteria $subCriteria)
    {
        $subCriteria->load([
            'criteria',
            'assessmentAspects' => function ($query) {
                $query->orderBy('order')->orderBy('value');
            },
        ]);

        return view('admin.sub-criteria.show', compact('subCriteria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubCriteria $subCriteria)
    {
        $criterias = Criteria::orderBy('order')->get();

        return view('admin.sub-criteria.edit', compact('subCriteria', 'criterias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubCriteriaRequest $request, SubCriteria $subCriteria)
    {
        $subCriteria->update($request->validated());

        return redirect()->route('admin.sub-criteria.index')
            ->with('success', 'Sub kriteria berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCriteria $subCriteria)
    {
        $subCriteria->delete();

        return redirect()->route('admin.sub-criteria.index')
            ->with('success', 'Sub kriteria berhasil dihapus.');
    }
}
