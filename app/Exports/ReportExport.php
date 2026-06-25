<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tickets;
    protected $type;

    public function __construct($tickets, $type = 'local')
    {
        $this->tickets = $tickets;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->tickets;
    }

    public function headings(): array
    {
        return [
            'TRAVEL ORDER NO.',
            'NAME OF OFFICIAL/PERSONNEL',
            'OFFICE',
            'TRAVEL DATE',
            'PURPOSE OF TRAVEL',
            'HIGHLIGHTS/OUTPUTS',
            'SIGNED BY'
        ];
    }

    public function map($ticket): array
    {
        if ($this->type === 'national') {
            $startDate = $ticket->departure?->format('M d, Y') ?? '';
            $endDate = $ticket->return_date?->format('M d, Y') ?? '';
            $travelDate = ($startDate || $endDate) ? "{$startDate} - {$endDate}" : '';

            $personnelNames = [];
            if (is_array($ticket->personnel)) {
                foreach ($ticket->personnel as $person) {
                    if (isset($person['name'])) {
                        $personnelNames[] = $person['name'];
                    }
                }
            }
            $travelers = implode(', ', $personnelNames);

            return [
                $ticket->to_no ?? '',
                !empty($travelers) ? $travelers : ($ticket->client->name ?? ''),
                $ticket->client->office ?? 'FAS',
                $travelDate,
                $ticket->purpose ?? $ticket->route,
                $ticket->toReport->outputs ?? '',
                '', 
            ];
        }

        // Local (Trip Ticket) mapping logic
        $startDate = $ticket->start_date?->format('M d, Y') ?? '';
        $endDate = $ticket->end_date?->format('M d, Y') ?? '';
        $travelDate = ($startDate || $endDate) ? "{$startDate} - {$endDate}" : '';

        $travelers = is_array($ticket->passengers) ? implode(', ', $ticket->passengers) : '';

        return [
            $ticket->to_no ?? '',
            !empty($travelers) ? $travelers : ($ticket->user->name ?? ''),
            $ticket->user->office ?? 'FAS',
            $travelDate,
            $ticket->purpose ?? $ticket->destination,
            $ticket->toReport->outputs ?? '',
            '', 
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $this->tickets->count() + 1;

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'name' => 'Cambria',
                'size' => 12,
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E06666',
                ],
            ],
        ]);

        if ($totalRows > 1) {
            $sheet->getStyle("A2:G{$totalRows}")->applyFromArray([
                'font' => [
                    'name' => 'Arial',
                    'size' => 10,
                    'bold' => false,
                ],
            ]);
        }

        return [];
    }
}