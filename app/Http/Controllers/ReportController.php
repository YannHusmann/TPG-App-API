<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;

class ReportController extends Controller
{
    public function createReport(Request $request)
    {
        $validated = $request->validate([
            'rep_sto_id' => 'required|string|exists:stops,sto_id',
            'rep_rou_id' => 'nullable|exists:routes,rou_id',
            'rep_message' => 'required|string|max:1000',
        ]);

        $report = Report::create([
            'rep_use_id' => Auth::id(),
            'rep_sto_id' => $validated['rep_sto_id'],
            'rep_rou_id' => $validated['rep_rou_id'] ?? null,
            'rep_message' => $validated['rep_message'],
            'rep_status' => 'envoyé',
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
        ]);

        $report->rep_message = $validated['rep_message'];
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
}