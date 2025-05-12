<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Enums\ReportType;
use Illuminate\Validation\Rule;



class ReportController extends Controller
{
    use AuthorizesRequests;

    public function createReport(Request $request)
    {
        $validated = $request->validate([
            'rep_sto_id' => 'required|string|exists:stops,sto_id',
            'rep_rou_id' => 'nullable|exists:routes,rou_id',
            'rep_message' => 'required|string|max:1000',
            'rep_type' => ['required', Rule::in(ReportType::values())],
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $report = Report::create([
            'rep_use_id' => Auth::id(),
            'rep_sto_id' => $validated['rep_sto_id'],
            'rep_rou_id' => $validated['rep_rou_id'] ?? null,
            'rep_message' => $validated['rep_message'],
            'rep_type' => $validated['rep_type'],
            'rep_status' => 'envoyé',
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

    
        return response()->json([
            'message' => 'Signalement enregistré',
            'data' => $report
        ], 201);
    }

    public function getAllReports()
    {
        // Vérifie que l'utilisateur connecté est admin
    if (!Auth::user() || Auth::user()->role !== 'admin') {
        return response()->json(['message' => 'Accès non autorisé'], 403);
    }

    $reports = Report::with(['user', 'stop', 'route'])
        ->orderByDesc('created_at')
        ->get();

    return response()->json([
        'message' => 'Liste complète des signalements',
        'data' => $reports
    ]);
    }

    public function getMyReports() {
        $reports = Report::where('rep_use_id', Auth::id())
        ->with(['stop', 'route'])
        ->orderByDesc('created_at')
        ->get();

        return response()->json([
            'message' => 'Liste des signalements',
            'data' => $reports,
        ]);
    }

    public function updateReport(Request $request, $id)
    {
        $report = Report::where('rep_id', $id)
            ->where('rep_use_id', Auth::id())
            ->first();

        if (!$report) {
            return response()->json(['message' => 'Signalement introuvable'], 404);
        }

        if ($report->rep_status !== 'envoyé') {
            return response()->json(['message' => 'Impossible de modifier ce signalement'], 403);
        }

        $validated = $request->validate([
            'rep_message' => 'required|string|max:1000',
            'rep_type' => ['required', Rule::in(ReportType::values())],
        ]);

        $report->rep_message = $validated['rep_message'];
        $report->rep_type = $validated['rep_type'];
        $report->save();

        return response()->json([
            'message' => 'Signalement modifié',
            'data' => $report
        ]);
    }


    public function deleteReport($id)
    {
        $report = Report::where('rep_id', $id)
            ->where('rep_use_id', Auth::id())
            ->first();

        if (!$report) {
            return response()->json(['message' => 'Signalement introuvable'], 404);
        }

        if ($report->rep_status !== 'envoyé') {
            return response()->json(['message' => 'Impossible de supprimer ce signalement'], 403);
        }

        $report->delete();

        return response()->json(['message' => 'Signalement supprimé avec succès']);
    }

    public function getStatsPerStop()
    {
        if (!Auth::check() || Auth::user()->use_role !== 'admin') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $stats = Report::select('rep_sto_id', \DB::raw('count(*) as total'))
            ->groupBy('rep_sto_id')
            ->with('stop:sto_id,sto_name') 
            ->get();

        return response()->json(['data' => $stats]);
    }


    public function filterReports(Request $request)
    {
        $query = Report::query()->where('rep_use_id', auth()->id());

        if ($request->has('status')) {
            $query->where('rep_status', $request->status);
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        if ($request->has('stop_id')) {
            $query->where('rep_sto_id', $request->stop_id);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function changeStatus($id, Request $request)
    {
        $request->validate([
            'status' => ['required', 'string'], 
        ]);

        $report = Report::findOrFail($id);
        $report->rep_status = $request->input('status');
        $report->save();

        return response()->json(['message' => 'Statut mis à jour']);
    }


}