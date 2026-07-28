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
        $criterias = Criteria::orderBy('code')->get();
        return view('admin.criteria.index', compact('criterias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nextCode = Criteria::generateNextCode();
        $existingCriterias = Criteria::orderBy('code')->get();
        $totalExistingWeight = (float) $existingCriterias->sum('weight');
        $existingNames = $existingCriterias->pluck('name')->map(fn($n) => mb_strtolower(trim($n)))->values()->toArray();
        return view('admin.criteria.create', compact('nextCode', 'totalExistingWeight', 'existingCriterias', 'existingNames'));
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

        $data = $request->only('name', 'description', 'weight');
        $data['code'] = Criteria::generateNextCode();

        Criteria::create($data);

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
        $totalExistingWeight = (float) Criteria::where('id', '!=', $criteria->id)->sum('weight');
        $existingNames = Criteria::where('id', '!=', $criteria->id)->pluck('name')->map(fn($n) => mb_strtolower(trim($n)))->values()->toArray();
        return view('admin.criteria.edit', compact('criteria', 'totalExistingWeight', 'existingNames'));
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

        $data = $request->only('name', 'description', 'weight');

        $criteria->update($data);

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

    /**
     * Update weight directly via inline edit.
     */
    public function updateWeight(\Illuminate\Http\Request $request, Criteria $criteria)
    {
        $request->validate([
            'weight' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        $newWeight = (float) $request->weight;

        if (! $this->isTotalWeightValid($newWeight, $criteria)) {
            return response()->json([
                'success' => false,
                'message' => 'Total bobot semua kriteria tidak boleh lebih dari 1.00.',
            ], 422);
        }

        $criteria->update(['weight' => $newWeight]);

        $totalWeight = (float) Criteria::sum('weight');

        return response()->json([
            'success' => true,
            'message' => 'Bobot kriteria ' . $criteria->code . ' berhasil diperbarui.',
            'weight' => number_format($newWeight, 2),
            'total_weight' => number_format($totalWeight, 2),
        ]);
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
