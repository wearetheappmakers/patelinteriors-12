<?php
namespace App\Exports;
use App\Product;
use App\ProductCategory;
use App\Company;
use App\User;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExportMrp implements FromCollection,WithHeadings
{
    public function headings(): array
    {
        if(Auth::user()->role == 1){
            return [
                'branch',
                'productcategory',
                'company',
                'productname',
                'sku_code',
                'product_price'
            ];
        }
        else
        {
            return [
                'productcategory',
                'company',
                'productname',
                'sku_code',
                'product_price'
            ];
        }
    }

    public function collection()
    {
        $query = Product::query();
        if (Auth::user()->id == 1) {
            $query->select('branch_id', 'productcategory_id', 'company_id', 'product', 'code', 'price');
        } else {
            $query->where('branch_id', Auth::user()->id)
            ->select('productcategory_id', 'company_id', 'product', 'code', 'price');
        }

        $productCategories = ProductCategory::pluck('name', 'id');
        $companies = Company::pluck('name', 'id');
        $branches = User::where('status', 1)->where('role', 2)->pluck('name', 'id');
        $dataCollection = collect();
        
        foreach ($query->cursor() as $product) {
            
            $data = [];
            if(Auth::user()->role == 1)
            {
                $data['branch'] = $branches[$product->branch_id] ?? null;
            }
            
            $data['productcategory'] = $productCategories[$product->productcategory_id] ?? null;
            $data['company']         = $companies[$product->company_id] ?? null;
            $data['productname']     = $product->product;
            $data['sku_code']        = $product->code;
            $data['product_price']   = $product->price;
            $dataCollection->push($data);
        }
        return $dataCollection;
    }
}