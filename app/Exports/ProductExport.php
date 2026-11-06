<?php

namespace App\Exports;
use App\Product;
use App\Category;
use Auth;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;


class ProductExport implements WithHeadings
{
    public function headings(): array
    {
        if(Auth::user()->role == 1){
            return [
                'branch',
                'productcategory',
                'productname',
                'company',
                'sku_code',
                'product_price',
                'product_unit',
                'description',
                'product_discount',
                'discount_type'

            ];
        }else{
            return [
                'productcategory',
                'productname',
                'company',
                'sku_code',
                'product_price',
                'product_unit',
                'description',
                'product_discount',
                'discount_type'

            ];
        }
    }  
}