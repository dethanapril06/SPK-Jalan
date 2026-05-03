<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyorRequest;
use App\Http\Requests\UpdateSurveyorRequest;
use App\Models\Surveyor;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SurveyorController extends Controller
{
    private const DEFAULT_SURVEYOR_PASSWORD = 'password';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $surveyors = Surveyor::with('user')
            ->orderBy('code')
            ->get();

        return view('admin.surveyors.index', compact('surveyors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.surveyors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSurveyorRequest $request)
    {
        DB::transaction(function () use ($request): void {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => self::DEFAULT_SURVEYOR_PASSWORD,
                'role' => 'surveyor',
            ]);

            Surveyor::create([
                'user_id' => $user->id,
                'code' => $request->code,
                'phone' => $request->phone,
                'is_active' => $request->boolean('is_active', true),
            ]);
        });

        return redirect()->route('admin.surveyors.index')
            ->with('success', 'Surveyor berhasil ditambahkan. Akun login dibuat dengan password default: ' . self::DEFAULT_SURVEYOR_PASSWORD);
    }

    /**
     * Reset password surveyor ke password default.
     */
    public function resetPassword(Surveyor $surveyor)
    {
        $user = $surveyor->user;

        if (! $user) {
            return back()->with('error', 'Akun user untuk surveyor ini tidak ditemukan.');
        }

        $user->update([
            'password' => self::DEFAULT_SURVEYOR_PASSWORD,
            'role' => 'surveyor',
        ]);

        return back()->with('success', 'Password surveyor berhasil direset ke default: ' . self::DEFAULT_SURVEYOR_PASSWORD);
    }

    /**
     * Display the specified resource.
     */
    public function show(Surveyor $surveyor)
    {
        $surveyor->load('user');

        return view('admin.surveyors.show', compact('surveyor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Surveyor $surveyor)
    {
        $surveyor->load('user');

        return view('admin.surveyors.edit', compact('surveyor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSurveyorRequest $request, Surveyor $surveyor)
    {
        DB::transaction(function () use ($request, $surveyor): void {
            $surveyor->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'surveyor',
            ]);

            $surveyor->update([
                'code' => $request->code,
                'phone' => $request->phone,
                'is_active' => $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('admin.surveyors.index')
            ->with('success', 'Data surveyor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Surveyor $surveyor)
    {
        $user = $surveyor->user;

        if ($user) {
            $user->delete();
        } else {
            $surveyor->delete();
        }

        return redirect()->route('admin.surveyors.index')
            ->with('success', 'Surveyor berhasil dihapus.');
    }
}
