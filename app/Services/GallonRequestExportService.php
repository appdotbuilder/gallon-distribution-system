<?php

namespace App\Services;

use App\Models\GallonRequest;

class GallonRequestExportService
{
    /**
     * Get requests data for export.
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getExportData($dateFrom, $dateTo): array
    {
        $requests = GallonRequest::with(['employee', 'approvedBy', 'preparedBy'])
            ->whereBetween('requested_at', [$dateFrom, $dateTo])
            ->orderBy('requested_at', 'desc')
            ->get();

        $data = [];
        $data[] = [
            'Request ID',
            'Employee ID',
            'Employee Name',
            'Grade',
            'Quantity',
            'Status',
            'Requested At',
            'Approved At',
            'Approved By',
            'Ready At',
            'Prepared By',
            'Completed At',
            'Notes',
        ];

        foreach ($requests as $request) {
            $data[] = [
                $request->id,
                $request->employee->employee_id,
                $request->employee->name,
                $request->employee->grade,
                $request->quantity,
                ucfirst($request->status),
                $request->requested_at->format('d-m-Y H:i:s'),
                $request->approved_at?->format('d-m-Y H:i:s') ?? '',
                $request->approvedBy->name ?? '',
                $request->ready_at?->format('d-m-Y H:i:s') ?? '',
                $request->preparedBy->name ?? '',
                $request->completed_at?->format('d-m-Y H:i:s') ?? '',
                $request->notes ?? '',
            ];
        }

        return $data;
    }

    /**
     * Generate CSV content from data.
     *
     * @param array $data
     * @return string
     */
    public function generateCsv(array $data): string
    {
        $output = fopen('php://temp', 'r+');
        
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}