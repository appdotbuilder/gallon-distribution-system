<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GallonRequest;
use Illuminate\Http\Request;

class RequestPreparationController extends Controller
{
    /**
     * Store a newly created resource in storage (mark request as ready).
     */
    public function store(Request $request, GallonRequest $gallonRequest)
    {
        if ($gallonRequest->status !== 'approved') {
            return back()->withErrors([
                'status' => 'Only approved requests can be marked as ready.'
            ]);
        }

        $gallonRequest->markReady($request->user());

        return back()->with('success', 'Request marked as ready for pickup.');
    }
}