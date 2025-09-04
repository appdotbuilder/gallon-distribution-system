<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GallonRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;


class RequestController extends Controller
{
    /**
     * Display a listing of gallon requests.
     */
    public function index(Request $request)
    {
        $query = GallonRequest::with(['employee', 'approvedBy', 'preparedBy']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('requested_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('requested_at', '<=', $request->date_to);
        }

        // Filter today's requests
        if ($request->filter === 'today') {
            $query->today();
        }

        $requests = $query->latest('requested_at')->paginate(15);

        return Inertia::render('admin/requests/index', [
            'requests' => $requests,
            'filters' => [
                'status' => $request->status,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'filter' => $request->filter,
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(GallonRequest $request)
    {
        $request->load(['employee', 'approvedBy', 'preparedBy']);

        return Inertia::render('admin/requests/show', [
            'gallonRequest' => $request,
        ]);
    }


}