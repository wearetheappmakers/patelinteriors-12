<?php

namespace App\Exports;
use App\Product;
use App\Category;
use App\Gallery;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


class GalleryExport implements WithHeadings,WithDrawings
{
    public function headings(): array
    {
        return [            
            'gallery'                  
        ];
    }   

    public function drawings()
    {
        $gallery = Gallery::select('gallery')->get();
        $drawings = [];
        foreach($gallery as $key=>$value)
        {
            $drawing = new Drawing();
            $drawing->setPath(public_path($value));
            $drawing->setHeight(50);
            $drawing->setWidth(120);
            $drawing->setCoordinates('B3');
            $drawings [] = ($drawing);
        }
        return $drawings;
    }
}