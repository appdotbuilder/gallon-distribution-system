<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GallonRequestExportService;
use Illuminate\Http\Request;

class RequestExportController extends Controller
{
    /**
     * Display the specified resource (export requests to CSV).
     */
    public function show(Request $request, GallonRequestExportService $exportService)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $data = $exportService->getExportData($request->date_from, $request->date_to);
        $csv = $exportService->generateCsv($data);

        $filename = 'gallon-requests-' . $request->date_from . '-to-' . $request->date_to . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}