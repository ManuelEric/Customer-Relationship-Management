<?php

namespace App\Exports;

use App\Models\Corporate;
use App\Models\CorporatePic;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PartnerExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithEvents, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $partners = Corporate::with([
            'pic' => function ($query) {
                $query->select('corp_id', 'pic_name', 'pic_mail', 'pic_linkedin', 'pic_phone')->where('is_pic', 1);
            },
        ])->select('corp_id', 'corp_name', 'corp_industry', 'corp_subsector_id', 'corp_mail', 'corp_phone', 'corp_site', 'corp_region', 'corp_city', 'corp_address', 'country_type', 'type', 'partnership_type', 'corp_status', 'created_at')->
            where('active_status', 1)->
            get();

        return $partners->flatMap(function ($partner) {
            if (count($partner->pic) > 0) {
                return collect($partner->pic)->map(function ($pic) use ($partner) {
                    return [
                        $partner->corp_id,
                        $partner->corp_name,
                        $partner->industry?->name ?? null,
                        $partner->subSector?->name ?? null,
                        $partner->corp_mail,
                        $partner->corp_phone,
                        $partner->corp_site,
                        $partner->corp_region,
                        $partner->corp_city,
                        $partner->corp_address,
                        $partner->country_type,
                        $partner->type,
                        $partner->partnership_type,
                        $partner->corp_status,
                        $partner->created_at ? Carbon::parse($partner->created_at)->format('Y/m/d') : '',
                        $pic->pic_name,
                        $pic->pic_phone,
                    ];
                });
            } else {
                return [[
                    $partner->corp_id,
                    $partner->corp_name,
                    $partner->industry?->name ?? null,
                    $partner->subSector?->name ?? null,
                    $partner->corp_mail,
                    $partner->corp_phone,
                    $partner->corp_site,
                    $partner->corp_region,
                    $partner->corp_city,
                    $partner->corp_address,
                    $partner->country_type,
                    $partner->type,
                    $partner->partnership_type,
                    $partner->corp_status,
                    $partner->created_at ? Carbon::parse($partner->created_at)->format('Y/m/d') : '',
                    null, // pic_name
                    null, // pic_phone
                ]];
            }
        });

        // return CorporatePic::with('corporate')->where('is_pic', 1)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Partner Name',
            'Industry',
            'Sub Industry',
            'Email',
            'Phone',
            'Website',
            'Region',
            'City',
            'Address',
            'Country',
            'Type',
            'Partnership Type',
            'Partner Status',
            'Registered At',
            'PIC Name',
            'PIC Phone Number',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $lastRow = $sheet->getHighestRow();
                $currentPartnerName = null;
                $startRow = 2; // Row 1 is the heading

                for ($row = 2; $row <= $lastRow; $row++) {
                    $partnerName = $sheet->getCell("B{$row}")->getValue();

                    if ($partnerName !== $currentPartnerName) {
                        if ($startRow < $row - 1) {
                            $sheet->mergeCells("B{$startRow}:B".($row - 1));
                        }
                        $currentPartnerName = $partnerName;
                        $startRow = $row;
                    }
                }

                // Final merge for the last repeated group
                if ($startRow < $lastRow) {
                    $sheet->mergeCells("B{$startRow}:B{$lastRow}");
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER,
            'O' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'Q' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
