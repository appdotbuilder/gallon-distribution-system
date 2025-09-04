<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\GallonRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GallonSystemController extends Controller
{
    /**
     * Display the main gallon system interface.
     */
    public function index()
    {
        return Inertia::render('gallon-system');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Handle different actions based on request data
        $action = $request->input('action', 'lookup');
        
        switch ($action) {
            case 'lookup':
                return $this->processLookup($request);
            case 'request':
                return $this->processRequest($request);
            case 'pickup':
                return $this->processPickup($request);
            default:
                return $this->processLookup($request);
        }
    }

    /**
     * Process employee lookup.
     */
    protected function processLookup(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string|exists:employees,employee_id',
        ]);

        $employee = Employee::where('employee_id', $request->employee_id)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return back()->withErrors([
                'employee_id' => 'Employee not found or inactive.'
            ]);
        }

        $pendingPickups = $employee->getPendingPickupRequests();
        $gallonHistory = $employee->getGallonHistory();

        return Inertia::render('gallon-system', [
            'employee' => $employee,
            'pendingPickups' => $pendingPickups,
            'gallonHistory' => $gallonHistory,
        ]);
    }

    /**
     * Process gallon request.
     */
    protected function processRequest(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string|exists:employees,employee_id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $employee = Employee::where('employee_id', $request->employee_id)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return back()->withErrors([
                'employee_id' => 'Employee not found or inactive.'
            ]);
        }

        // Check if employee has enough quota
        if ($employee->remaining_quota < $request->quantity) {
            return back()->withErrors([
                'quantity' => 'Requested quantity exceeds remaining quota. Available: ' . $employee->remaining_quota . ' gallons.'
            ])->with('employee', $employee);
        }

        // Create the request
        GallonRequest::create([
            'employee_id' => $employee->id,
            'quantity' => $request->quantity,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        $pendingPickups = $employee->getPendingPickupRequests();
        $gallonHistory = $employee->getGallonHistory();

        return Inertia::render('gallon-system', [
            'employee' => $employee->fresh(),
            'pendingPickups' => $pendingPickups,
            'gallonHistory' => $gallonHistory,
            'success' => 'Gallon request submitted successfully!',
        ]);
    }

    /**
     * Process gallon pickup.
     */
    protected function processPickup(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string|exists:employees,employee_id',
            'request_id' => 'required|integer|exists:gallon_requests,id',
        ]);

        $employee = Employee::where('employee_id', $request->employee_id)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return back()->withErrors([
                'employee_id' => 'Employee not found or inactive.'
            ]);
        }

        $gallonRequest = GallonRequest::where('id', $request->request_id)
            ->where('employee_id', $employee->id)
            ->where('status', 'ready')
            ->first();

        if (!$gallonRequest) {
            return back()->withErrors([
                'request_id' => 'Request not found or not ready for pickup.'
            ]);
        }

        $gallonRequest->complete();

        $pendingPickups = $employee->getPendingPickupRequests();
        $gallonHistory = $employee->getGallonHistory();

        return Inertia::render('gallon-system', [
            'employee' => $employee->fresh(),
            'pendingPickups' => $pendingPickups,
            'gallonHistory' => $gallonHistory,
            'success' => 'Gallon pickup confirmed successfully!',
        ]);
    }
}