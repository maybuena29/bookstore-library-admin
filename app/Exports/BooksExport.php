<?php

namespace App\Exports;

use App\Models\Books;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BooksExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle

{
    public function collection()
    {
        return Books::all();
    }

    public function headings(): array
    {

        $defaultHeadings = [
            'Book Name',
            'Author',
            'Date Created',
        ];

        return $defaultHeadings;
    }

    // FOR SETTING THE VALUE PER COLUMN
    public function map($data): array
    {
        $defaultData = [
            isset($data->book_name)? $data->book_name : ' --- ',
            isset($data->author)? $data->author : ' --- ',
            isset($data->created_at)? Carbon::parse($data->created_at)->timezone('Asia/Manila')->format('Y-m-d g:i A') : ' --- ',
        ];

        return $defaultData;
    }

    // FOR EXCEL STYLE ONLY
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return [
            1    => ['font' => ['bold' => true, 'size' => 13]],
        ];
    }

    // TITLE FOR SHEET
    public function title(): string
    {
        return 'Book List';
    }
}
