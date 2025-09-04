import { type SharedData } from '@/types';
import { AppShell } from '@/components/app-shell';
import { Head, Link, usePage } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

export default function Dashboard() {
    const { auth } = usePage<SharedData>().props;
    
    const userRole = auth.user?.role;
    const userName = auth.user?.name;

    const getRoleName = (role: string) => {
        switch (role) {
            case 'admin_hr': return 'HR Administrator';
            case 'admin_administrator': return 'System Administrator';
            case 'admin_gudang': return 'Warehouse Administrator';
            default: return 'Administrator';
        }
    };

    return (
        <AppShell>
            <Head title="Admin Dashboard" />
            <div className="space-y-8">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">💧 Gallon Distribution Dashboard</h1>
                        <p className="text-gray-600 mt-2">
                            Welcome, {userName} ({getRoleName(userRole || '')})
                        </p>
                    </div>
                    <Button asChild>
                        <Link href={route('home')}>
                            🏠 Back to Public System
                        </Link>
                    </Button>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {/* HR Admin Features */}
                    {userRole === 'admin_hr' && (
                        <>
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        👥 Employee Management
                                    </CardTitle>
                                    <CardDescription>
                                        Add, edit, and manage employee records
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-2">
                                        <Button asChild className="w-full">
                                            <Link href={route('admin.employees.index')}>
                                                View All Employees
                                            </Link>
                                        </Button>
                                        <Button asChild variant="outline" className="w-full">
                                            <Link href={route('admin.employees.create')}>
                                                Add New Employee
                                            </Link>
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        </>
                    )}

                    {/* Administrator Features */}
                    {userRole === 'admin_administrator' && (
                        <>
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        📋 Request Management
                                    </CardTitle>
                                    <CardDescription>
                                        View and approve gallon requests
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-2">
                                        <Button asChild className="w-full">
                                            <Link href={route('admin.requests.index')}>
                                                View All Requests
                                            </Link>
                                        </Button>
                                        <Button asChild variant="outline" className="w-full">
                                            <Link href={route('admin.requests.index', { filter: 'today' })}>
                                                Today's Requests
                                            </Link>
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        📊 Reports & Export
                                    </CardTitle>
                                    <CardDescription>
                                        Download activity reports
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <Button asChild variant="outline" className="w-full">
                                        <Link href={route('admin.requests.index')}>
                                            Export Data
                                        </Link>
                                    </Button>
                                </CardContent>
                            </Card>
                        </>
                    )}

                    {/* Warehouse Admin Features */}
                    {userRole === 'admin_gudang' && (
                        <>
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        📦 Warehouse Operations
                                    </CardTitle>
                                    <CardDescription>
                                        Manage gallon stock and preparation
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-2">
                                        <Button asChild className="w-full">
                                            <Link href={route('admin.requests.index', { status: 'approved' })}>
                                                Approved Requests
                                            </Link>
                                        </Button>
                                        <Button asChild variant="outline" className="w-full">
                                            <Link href={route('admin.requests.index', { status: 'ready' })}>
                                                Ready for Pickup
                                            </Link>
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        </>
                    )}

                    {/* Common Features */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                🔍 Quick Overview
                            </CardTitle>
                            <CardDescription>
                                System statistics and overview
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="text-center">
                                    <div className="text-2xl font-bold text-blue-600">
                                        Active System
                                    </div>
                                    <div className="text-sm text-gray-600">
                                        Gallon Distribution Running
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>🎯 System Features</CardTitle>
                        <CardDescription>
                            Overview of the gallon distribution management system
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="text-center space-y-2">
                                <div className="text-3xl">📊</div>
                                <h3 className="font-semibold">Quota Management</h3>
                                <p className="text-sm text-gray-600">
                                    Automatic quota allocation based on employee grade levels
                                </p>
                            </div>
                            <div className="text-center space-y-2">
                                <div className="text-3xl">🔄</div>
                                <h3 className="font-semibold">Request Workflow</h3>
                                <p className="text-sm text-gray-600">
                                    Complete request lifecycle from submission to pickup
                                </p>
                            </div>
                            <div className="text-center space-y-2">
                                <div className="text-3xl">📈</div>
                                <h3 className="font-semibold">Tracking & Reports</h3>
                                <p className="text-sm text-gray-600">
                                    Comprehensive tracking and exportable reports
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppShell>
    );
}