<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Enums\ReportType;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Notifications\ReportStatusChangedNotification;
use App\Models\ReportImage;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    use AuthorizesRequests;

    public function createReport(Request $request)
    {
        $validated = $request->validate([
            'rep_sto_id'   => 'nullable|string|exists:stops,sto_id',
            'rep_rou_id'   => 'nullable|exists:routes,rou_id',
            'rep_message'  => 'required|string|max:1000',
            'rep_type'     => ['required', Rule::in(ReportType::values())],
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'images.*'     => 'image|max:2048',
        ]);

        $report = Report::create([
            'rep_use_id'   => Auth::id(),
            'rep_sto_id'   => $validated['rep_sto_id'] ?? null,
            'rep_rou_id'   => $validated['rep_rou_id'] ?? null,
            'rep_message'  => $validated['rep_message'],
            'rep_type'     => $validated['rep_type'],
            'rep_status'   => 'envoyé',
            'latitude'     => $validated['latitude'] ?? null,
            'longitude'    => $validated['longitude'] ?? null,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reports', 'public');
                ReportImage::create([
                    'report_id' => $report->rep_id,
                    'path' => $path,
                ]);
            }
        }

        return response()->json([
            'message' => 'Signalement enregistré',
            'data'    => $report->load('images')
        ], 201);
    }


    public function getAllReports()
    {
        if (!Gate::allows('is-admin')) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $reports = Report::with(['user', 'stop', 'route', 'images'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'message' => 'Liste complète des signalements',
            'data'    => $reports
        ]);
    }

    public function getMyReports()
    {
        $reports = Report::where('rep_use_id', Auth::id())
            ->with(['stop', 'route', 'images'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'message' => 'Liste des signalements',
            'data'    => $reports,
        ]);
    }


    public function getReportById($id)
    {
        $report = Report::where('rep_id', $id)
            ->where('rep_use_id', Auth::id())
            ->with(['user', 'stop', 'route', 'images'])
            ->first();

        if (!$report) {
            return response()->json(['message' => 'Signalement introuvable'], 404);
        }

        return response()->json([
            'message' => 'Détails du signalement',
            'data'    => $report
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
            'rep_type'    => ['required', Rule::in(ReportType::values())],
            'images.*'    => 'image|max:2048',
            'existing_images' => 'array',
            'existing_images.*' => 'string'
        ]);

        $report->rep_message = $validated['rep_message'];
        $report->rep_type    = $validated['rep_type'];
        $report->save();

        // Supprimer les anciennes images NON conservées
        $existingUrls = $request->input('existing_images', []);
        foreach ($report->images as $image) {
            $fullUrl = url(Storage::url($image->path)); // http://domain/storage/...
            if (!in_array($fullUrl, $existingUrls)) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        // Ajouter les nouvelles images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reports', 'public');
                \App\Models\ReportImage::create([
                    'report_id' => $report->rep_id,
                    'path' => $path,
                ]);
            }
        }

        return response()->json([
            'message' => 'Signalement modifié',
            'data'    => $report->load('images')
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
        if (!Gate::allows('is-admin')) {
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
        $user = Auth::user();

        $query = Report::with(['stop', 'route', 'images'])
            ->where('rep_use_id', $user->use_id)
            ->orderByDesc('created_at');

        if ($request->has('status')) {
            $query->where('rep_status', $request->status);
        }

        $reports = $query->paginate(50);

        return response()->json([
            'message' => 'Liste des signalements filtrés',
            'data' => $reports,
        ]);
    }


    public function changeStatus($id, Request $request)
    {
        if (!Gate::allows('is-admin')) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $report = Report::with('user')->findOrFail($id);
        $oldStatus = $report->rep_status;
        $newStatus = $request->input('status');

        if ($oldStatus === $newStatus) {
            return response()->json(['message' => 'Aucun changement détecté sur le statut']);
        }

        $report->rep_status = $newStatus;
        $report->save();

        if ($report->user) {
            $report->user->notify(new ReportStatusChangedNotification($newStatus, $report));
        }

        return response()->json(['message' => 'Statut mis à jour']);
    }

    public function getTypes()
    {
        return response()->json([
            'data' => ReportType::values()
        ]);
    }

}
