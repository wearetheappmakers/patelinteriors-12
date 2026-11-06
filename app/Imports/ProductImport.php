<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\ProductCategory;
use App\Product;
use App\WarehouseInventory;
use App\Warehouse;
use App\Company;
use App\User;
use Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToModel,WithHeadingRow,WithValidation
{

    private $rowNumber = 2;

    public function model(array $row)
    {
        if(Auth::user()->role == 1)
        {
            $branch = User::where('name',$row['branch'])->first();
            if(!isset($branch))
            {
                throw new \Exception("At Row ".$this->rowNumber.": ".$row['branch']." Branch doesn't exists.");
            }
        }
        else
        {
            $branch = User::where('id',Auth::user()->id)->first();
        }
            
        $productcategory = ProductCategory::where('name',$row['productcategory'])->where('branch_id',$branch->id)->first();
        if(!isset($productcategory))
        {
            throw new \Exception("At Row ".$this->rowNumber.": ".$row['productcategory']." Product Category doesn't exists.");
        }

        $company = Company::where('name',$row['company'])->where('pro_category_id',$productcategory->id)->where('branch_id',$branch->id)->first();
        if(!isset($company))
        {
            throw new \Exception("At Row ".$this->rowNumber.": ".$row['company']." Company doesn't exists.");
        }


        $product_chk = Product::where('branch_id',$branch->id)->where('code',$row['sku_code'])->first();
        if(isset($product_chk))
        {
            $product = Product::where('id',$product_chk->id)->first();
        }
        else
        {
            $product = new Product();
            $product->branch_id = $branch->id;
            $product->code = $row['sku_code'];
        }
        $product->product = $row['productname'];
        $product->company_id = $company->id;
        $product->productcategory_id = $productcategory->id;
        $product->description = $row['description'] ?? NULL;
        $product->price = $row['product_price'];
        $product->unit = $row['product_unit'] ?? NULL;
        $product->discount_type = $row['product_discount'] ?? NULL;
        $product->discount_type = $row['discount_type'] ?? NULL;
        $product->save();

        // Create Inventory when creating new product
        if(!isset($product_chk))
        {
            $warehouse = Warehouse::where('branch_id',$branch->id)->get();
            foreach ($warehouse as $key => $value) {

                $param = [
                    'warehouse_id' => $value->id,
                    'branch_id' => $branch->id,
                    'product_id' => $product->id,
                    'stock' => 0,
                    'used' => 0,
                    'actual_stock' => 0,
                ];
                WarehouseInventory::create($param);
            }
        }

        $this->rowNumber++;
    }


    public function rules(): array
    {
        return [

            '*.productcategory' => 'required',
            '*.company' => 'required',
            '*.productname' => 'required',
            '*.sku_code' => 'required',
            '*.product_price' => 'required',

        ];
    }
}
