<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlternativeRequest;
use App\Http\Requests\UpdateAlternativeRequest;
use App\Models\Alternative;

class AlternativeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alternatives = Alternative::query()
            ->orderBy('order')
            ->orderBy('code')
            ->get();

        return view('admin.alternatives.index', compact('alternatives'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.alternatives.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlternativeRequest $request)
    {
        Alternative::create($request->validated());

        return redirect()->route('admin.alternatives.index')
            ->with('success', 'Alternatif berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Alternative $alternative)
    {
        return view('admin.alternatives.show', compact('alternative'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Alternative $alternative)
    {
        return view('admin.alternatives.edit', compact('alternative'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlternativeRequest $request, Alternative $alternative)
    {
        $alternative->update($request->validated());

        return redirect()->route('admin.alternatives.index')
            ->with('success', 'Alternatif berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alternative $alternative)
    {
        $alternative->delete();

        return redirect()->route('admin.alternatives.index')
            ->with('success', 'Alternatif berhasil dihapus.');
    }
}
