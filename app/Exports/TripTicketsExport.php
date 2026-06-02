<?php

namespace App\Exports;

use App\Models\TripTicket;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TripTicketsExport implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents
{
    protected $filters;
    protected $officeHeaderRows = [];

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function collection()
    {
        $query = TripTicket::with(['driver', 'vehicle']);

        if (!empty($this->filters['office'])) {
            // Using trim handles accidental leading/trailing spaces in DB records
            $query->where('office', trim($this->filters['office']));
        }

        if (!empty($this->filters['period'])) {
            $now = Carbon::now();
            switch ($this->filters['period']) {
                case 'day':
                    $query->whereDate('start_date', $now->today());
                    break;
                case 'week':
                    $query->whereBetween('start_date', [$now->startOfWeek()->toDateTimeString(), $now->endOfWeek()->toDateTimeString()]);
                    break;
                case 'month':
                    $query->whereMonth('start_date', $now->month)->whereYear('start_date', $now->year);
                    break;
                case 'year':
                    $query->whereYear('start_date', $now->year);
                    break;
            }
        }
        if (empty($this->filters['period'])) {
            if (!empty($this->filters['custom_start_date'])) {
                $query->whereDate('start_date', '>=', $this->filters['custom_start_date']);
            }
            if (!empty($this->filters['custom_end_date'])) {
                $query->whereDate('end_date', '<=', $this->filters['custom_end_date']);
            }
        }

        $tickets = $query->orderBy('office', 'asc')
            ->orderBy('start_date', 'desc')
            ->get();

        $exportData = collect();
        $currentOffice = null;
        $currentRowNum = 5;

        foreach ($tickets as $ticket) {
            if ($ticket->office !== $currentOffice) {
                $currentOffice = $ticket->office;
                $this->officeHeaderRows[] = $currentRowNum;

                $exportData->push([
                    'OFFICE: ' . strtoupper($currentOffice),
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                ]);
                $currentRowNum++;
            }

            $passengerList = is_array($ticket->passengers)
                ? implode(', ', $ticket->passengers)
                : $ticket->passengers;

            $exportData->push([
                '',
                $ticket->start_date ? $ticket->start_date->format('Y-m-d') : '',
                $ticket->end_date ? $ticket->end_date->format('Y-m-d') : '',
                $ticket->destination,
                $ticket->purpose,
                $passengerList ?? 'None',
                $ticket->driver->name ?? 'Unassigned',
                $ticket->vehicle->model ?? $ticket->vehicle->plate_number ?? 'No Vehicle Assigned',
            ]);
            $currentRowNum++;
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'Office Section',
            'Departure Date',
            'Return Date',
            'Destination',
            'Purpose',
            'Passengers',
            'Driver',
            'Vehicle'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'BFAR BOOKING SYSTEM REPORT');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Exported At: ' . Carbon::now()->format('F d, Y h:i A'));
                $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);

                $sheet->getStyle('A4:H4')->getFont()->setBold(true);
                $sheet->getStyle('A4:H4')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0E0E0');

                foreach ($this->officeHeaderRows as $row) {
                    $sheet->mergeCells("A{$row}:H{$row}");
                    $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->setItalic(true)->setSize(11);
                    $sheet->getStyle("A{$row}:H{$row}")->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF5F5F5');
                }

                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}