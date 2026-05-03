<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssessmentAspectRequest;
use App\Http\Requests\UpdateAssessmentAspectRequest;
use App\Models\AssessmentAspect;
use App\Models\SubCriteria;

class AssessmentAspectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assessmentAspects = AssessmentAspect::with(['subCriteria.criteria'])
            ->orderBy('sub_criteria_id')
            ->orderBy('order')
            ->get();

        return view('admin.assessment-aspects.index', compact('assessmentAspects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subCriterias = SubCriteria::with('criteria')
            ->orderBy('criteria_id')
            ->orderBy('order')
            ->get();

        return view('admin.assessment-aspects.create', compact('subCriterias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssessmentAspectRequest $request)
    {
        AssessmentAspect::create($request->validated());

        return redirect()->route('admin.assessment-aspects.index')
            ->with('success', 'Aspek penilaian berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AssessmentAspect $assessmentAspect)
    {
        $assessmentAspect->load(['subCriteria.criteria']);

        return view('admin.assessment-aspects.show', compact('assessmentAspect'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssessmentAspect $assessmentAspect)
    {
        $subCriterias = SubCriteria::with('criteria')
            ->orderBy('criteria_id')
            ->orderBy('order')
            ->get();

        return view('admin.assessment-aspects.edit', compact('assessmentAspect', 'subCriterias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssessmentAspectRequest $request, AssessmentAspect $assessmentAspect)
    {
        $assessmentAspect->update($request->validated());

        return redirect()->route('admin.assessment-aspects.index')
            ->with('success', 'Aspek penilaian berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssessmentAspect $assessmentAspect)
    {
        $assessmentAspect->delete();

        return redirect()->route('admin.assessment-aspects.index')
            ->with('success', 'Aspek penilaian berhasil dihapus.');
    }
}
