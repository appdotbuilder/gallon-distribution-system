import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Separator } from '@/components/ui/separator';

interface Employee {
    id: number;
    employee_id: string;
    name: string;
    grade: string;
    monthly_quota: number;
    current_month_taken: number;
    remaining_quota: number;
}

interface GallonRequest {
    id: number;
    quantity: number;
    status: string;
    requested_at: string;
    approved_at?: string;
    ready_at?: string;
    completed_at?: string;
    notes?: string;
}

interface Props {
    employee?: Employee;
    pendingPickups?: GallonRequest[];
    gallonHistory?: GallonRequest[];
    success?: string;
    [key: string]: unknown;
}

export default function GallonSystem({ employee, pendingPickups, gallonHistory, success }: Props) {
    const [activeTab, setActiveTab] = useState<'check' | 'request' | 'pickup'>('check');
    
    const lookupForm = useForm({
        employee_id: '',
        action: 'lookup',
    });

    const requestForm = useForm({
        employee_id: employee?.employee_id || '',
        quantity: 1,
        action: 'request',
    });

    const pickupForm = useForm({
        employee_id: employee?.employee_id || '',
        request_id: '',
        action: 'pickup',
    });

    const handleLookup = (e: React.FormEvent) => {
        e.preventDefault();
        lookupForm.setData('action', 'lookup');
        lookupForm.post(route('gallon-system.store'), {
            preserveScroll: true,
        });
    };

    const handleRequest = (e: React.FormEvent) => {
        e.preventDefault();
        requestForm.setData('action', 'request');
        requestForm.post(route('gallon-system.store'), {
            preserveScroll: true,
        });
    };

    const handlePickup = (requestId: number) => {
        pickupForm.setData({
            employee_id: employee?.employee_id || '',
            request_id: requestId.toString(),
            action: 'pickup',
        });
        
        pickupForm.post(route('gallon-system.store'), {
            preserveScroll: true,
        });
    };

    const getStatusBadge = (status: string) => {
        const variants: Record<string, "default" | "secondary" | "destructive" | "outline"> = {
            pending: 'secondary',
            approved: 'outline',
            ready: 'default',
            completed: 'default',
        };
        
        return <Badge variant={variants[status] || 'default'}>{status.toUpperCase()}</Badge>;
    };

    return (
        <>
            <Head title="Gallon Distribution System" />
            
            <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
                <div className="max-w-6xl mx-auto space-y-8">
                    {/* Header */}
                    <div className="text-center space-y-4">
                        <h1 className="text-4xl font-bold text-gray-900">
                            💧 Gallon Distribution Management System
                        </h1>
                        <p className="text-lg text-gray-600">
                            Manage your monthly gallon allocation efficiently
                        </p>
                    </div>

                    {/* Success Message */}
                    {success && (
                        <Alert className="bg-green-50 border-green-200">
                            <AlertDescription className="text-green-800">
                                {success}
                            </AlertDescription>
                        </Alert>
                    )}

                    {/* Employee Lookup */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                🆔 Employee Identification
                            </CardTitle>
                            <CardDescription>
                                Enter your Employee ID to access the gallon distribution system
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleLookup} className="space-y-4">
                                <div className="flex gap-4">
                                    <div className="flex-1">
                                        <Label htmlFor="employee_id">Employee ID</Label>
                                        <Input
                                            id="employee_id"
                                            type="text"
                                            placeholder="Enter your Employee ID (e.g., EMP001)"
                                            value={lookupForm.data.employee_id}
                                            onChange={(e) => lookupForm.setData('employee_id', e.target.value)}
                                            className="font-mono"
                                            required
                                        />
                                        {lookupForm.errors.employee_id && (
                                            <p className="text-sm text-red-600 mt-1">
                                                {lookupForm.errors.employee_id}
                                            </p>
                                        )}
                                    </div>
                                    <div className="flex items-end">
                                        <Button type="submit" disabled={lookupForm.processing}>
                                            {lookupForm.processing ? 'Looking up...' : 'Lookup Employee'}
                                        </Button>
                                    </div>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    {/* Employee Information */}
                    {employee && (
                        <>
                            <Card className="bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        👤 Employee Information
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <Label className="text-sm font-medium text-gray-600">Employee ID</Label>
                                            <p className="font-mono text-lg font-semibold">{employee.employee_id}</p>
                                        </div>
                                        <div>
                                            <Label className="text-sm font-medium text-gray-600">Name</Label>
                                            <p className="text-lg font-semibold">{employee.name}</p>
                                        </div>
                                        <div>
                                            <Label className="text-sm font-medium text-gray-600">Grade</Label>
                                            <p className="text-lg font-semibold">{employee.grade}</p>
                                        </div>
                                        <div>
                                            <Label className="text-sm font-medium text-gray-600">Monthly Quota</Label>
                                            <p className="text-lg font-semibold">{employee.monthly_quota} gallons</p>
                                        </div>
                                    </div>
                                    
                                    <Separator />
                                    
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div className="bg-white p-4 rounded-lg border">
                                            <Label className="text-sm font-medium text-gray-600">Taken This Month</Label>
                                            <p className="text-2xl font-bold text-red-600">{employee.current_month_taken} gallons</p>
                                        </div>
                                        <div className="bg-white p-4 rounded-lg border">
                                            <Label className="text-sm font-medium text-gray-600">Remaining Quota</Label>
                                            <p className="text-2xl font-bold text-green-600">{employee.remaining_quota} gallons</p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Action Tabs */}
                            <div className="flex flex-wrap gap-2 border-b">
                                <Button
                                    variant={activeTab === 'check' ? 'default' : 'ghost'}
                                    onClick={() => setActiveTab('check')}
                                    className="mb-2"
                                >
                                    📊 Check Quota & History
                                </Button>
                                <Button
                                    variant={activeTab === 'request' ? 'default' : 'ghost'}
                                    onClick={() => setActiveTab('request')}
                                    className="mb-2"
                                >
                                    📝 Request Gallons
                                </Button>
                                {pendingPickups && pendingPickups.length > 0 && (
                                    <Button
                                        variant={activeTab === 'pickup' ? 'default' : 'ghost'}
                                        onClick={() => setActiveTab('pickup')}
                                        className="mb-2"
                                    >
                                        📦 Pickup Gallons ({pendingPickups.length})
                                    </Button>
                                )}
                            </div>

                            {/* Tab Content */}
                            {activeTab === 'check' && gallonHistory && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle>📋 Gallon Request History</CardTitle>
                                        <CardDescription>
                                            Complete history of your gallon requests and pickups
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent>
                                        {gallonHistory.length === 0 ? (
                                            <p className="text-gray-500 text-center py-8">
                                                No gallon requests found in your history.
                                            </p>
                                        ) : (
                                            <div className="space-y-3">
                                                {gallonHistory.map((request) => (
                                                    <div key={request.id} className="flex items-center justify-between p-4 border rounded-lg">
                                                        <div className="space-y-1">
                                                            <div className="flex items-center gap-2">
                                                                <span className="font-semibold">{request.quantity} gallons</span>
                                                                {getStatusBadge(request.status)}
                                                            </div>
                                                            <p className="text-sm text-gray-600">
                                                                Requested: {new Date(request.requested_at).toLocaleString('id-ID', {
                                                                    day: '2-digit',
                                                                    month: '2-digit',
                                                                    year: 'numeric',
                                                                    hour: '2-digit',
                                                                    minute: '2-digit',
                                                                    second: '2-digit',
                                                                })}
                                                            </p>
                                                            {request.completed_at && (
                                                                <p className="text-sm text-gray-600">
                                                                    Completed: {new Date(request.completed_at).toLocaleString('id-ID', {
                                                                        day: '2-digit',
                                                                        month: '2-digit',
                                                                        year: 'numeric',
                                                                        hour: '2-digit',
                                                                        minute: '2-digit',
                                                                        second: '2-digit',
                                                                    })}
                                                                </p>
                                                            )}
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        )}
                                    </CardContent>
                                </Card>
                            )}

                            {activeTab === 'request' && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle>📝 Request Gallons</CardTitle>
                                        <CardDescription>
                                            Submit a request for gallon allocation from your monthly quota
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent>
                                        <form onSubmit={handleRequest} className="space-y-4">
                                            <div>
                                                <Label htmlFor="quantity">Quantity (gallons)</Label>
                                                <Input
                                                    id="quantity"
                                                    type="number"
                                                    min="1"
                                                    max={employee.remaining_quota}
                                                    value={requestForm.data.quantity}
                                                    onChange={(e) => requestForm.setData('quantity', parseInt(e.target.value) || 1)}
                                                    className="w-32"
                                                    required
                                                />
                                                <p className="text-sm text-gray-600 mt-1">
                                                    Maximum available: {employee.remaining_quota} gallons
                                                </p>
                                                {requestForm.errors.quantity && (
                                                    <p className="text-sm text-red-600 mt-1">
                                                        {requestForm.errors.quantity}
                                                    </p>
                                                )}
                                            </div>
                                            <Button 
                                                type="submit" 
                                                disabled={requestForm.processing || employee.remaining_quota === 0}
                                                className="w-full"
                                            >
                                                {requestForm.processing ? 'Submitting Request...' : 'Submit Request'}
                                            </Button>
                                            {employee.remaining_quota === 0 && (
                                                <p className="text-sm text-red-600">
                                                    You have exhausted your monthly quota. Quota will reset next month.
                                                </p>
                                            )}
                                        </form>
                                    </CardContent>
                                </Card>
                            )}

                            {activeTab === 'pickup' && pendingPickups && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle>📦 Pickup Ready Gallons</CardTitle>
                                        <CardDescription>
                                            Confirm pickup of gallons that are ready for collection
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent>
                                        {pendingPickups.length === 0 ? (
                                            <p className="text-gray-500 text-center py-8">
                                                No gallons are ready for pickup at this time.
                                            </p>
                                        ) : (
                                            <div className="space-y-3">
                                                {pendingPickups.map((request) => (
                                                    <div key={request.id} className="flex items-center justify-between p-4 border rounded-lg bg-green-50 border-green-200">
                                                        <div className="space-y-1">
                                                            <div className="flex items-center gap-2">
                                                                <span className="font-semibold text-green-800">
                                                                    {request.quantity} gallons ready for pickup
                                                                </span>
                                                                {getStatusBadge(request.status)}
                                                            </div>
                                                            <p className="text-sm text-gray-600">
                                                                Ready since: {request.ready_at && new Date(request.ready_at).toLocaleString('id-ID', {
                                                                    day: '2-digit',
                                                                    month: '2-digit',
                                                                    year: 'numeric',
                                                                    hour: '2-digit',
                                                                    minute: '2-digit',
                                                                    second: '2-digit',
                                                                })}
                                                            </p>
                                                        </div>
                                                        <Button 
                                                            onClick={() => handlePickup(request.id)}
                                                            disabled={pickupForm.processing}
                                                            className="bg-green-600 hover:bg-green-700"
                                                        >
                                                            {pickupForm.processing ? 'Confirming...' : 'Confirm Pickup'}
                                                        </Button>
                                                    </div>
                                                ))}
                                            </div>
                                        )}
                                    </CardContent>
                                </Card>
                            )}
                        </>
                    )}

                    {/* Admin Login Link */}
                    <div className="text-center">
                        <Card className="bg-gray-50 border-gray-200">
                            <CardContent className="pt-6">
                                <div className="space-y-2">
                                    <p className="text-sm text-gray-600">
                                        Are you an administrator?
                                    </p>
                                    <Button
                                        variant="outline"
                                        onClick={() => router.visit(route('login'))}
                                    >
                                        🔐 Admin Login
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </>
    );
}