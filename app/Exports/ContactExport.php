<?php

namespace App\Exports;
use App\Product;
use App\Contact;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;


class ContactExport implements WithHeadings
{
     public function headings(): array
    {
        return [
            
            'contactcategory',
            'name',
            'company',
            'mobileno',
            'email',
            'address'
           
           
                   
        ];
    }



   
}
