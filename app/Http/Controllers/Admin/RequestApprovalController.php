<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GallonRequest;
use Illuminate\Http\Request;

class RequestApprovalController extends Controller
{
    /**
     * Store a newly created resource in storage (approve request).
     */
    public function store(Request $request, GallonRequest $gallonRequest)
    {
        if ($gallonRequest->status !== 'pending') {
            return back()->withErrors([
                'status' => 'Only pending requests can be approved.'
            ]);
        }

        $gallonRequest->approve($request->user());

        return back()->with('success', 'Request approved successfully.');
    }
}