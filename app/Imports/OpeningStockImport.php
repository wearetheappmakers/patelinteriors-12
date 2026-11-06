<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\ProductCategory;
use App\Product;
use App\WarehouseInventory;
use App\Warehouse;
use App\OpeningStock;
use App\OpeningStockChild;
use Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class OpeningStockImport implements ToModel,WithHeadingRow,WithValidation
{
    private $rowNumber = 2;
    public function model(array $row)
    {
        if(Auth::user()->role == 2)
        {
            $branch_id = Auth::user()->id;
            $product = Product::where('branch_id',$branch_id)->where('code',$row['product'])->first();
            if(!isset($product))
            {
                throw new \Exception("At Row ".$this->rowNumber.": ".$row['product']." doesn't exists in Product Master.");
            }
            $warehouse = Warehouse::where('branch_id',$branch_id)->where('name',$row['warehouse'])->first();
            if(!isset($warehouse))
            {
                throw new \Exception("At Row ".$this->rowNumber.": ".$row['warehouse']." doesn't exists in Warehouse Master.");
            }
            if(isset($product) && isset($warehouse))
            {
                $check = WarehouseInventory::where('product_id',$product->id)->where('branch_id',$branch_id)->where('warehouse_id',$warehouse->id)->first();
                if(isset($check))
                {
                    $date=date('Y-m-d');

                    if (date('m') >= 4) {
                        $financial_year = (date('Y')) . '-' . (date('y')+1);
                    } else {
                        $financial_year = (date('Y')-1) . '-' . date('y');
                    }
                    $stock_check = OpeningStock::where('fy_year',$financial_year)->where('branch_id',$branch_id)->exists();
                    if(!$stock_check)
                    {
                        $os = new OpeningStock();
                        $os->date = date('Y-m-d');
                        $os->fy_year = $financial_year;
                        $os->branch_id = $branch_id;
                        $os->save();
                    }
                    else
                    {
                        $os = OpeningStock::where('fy_year',$financial_year)->first();
                    }

                    $stock_child = new OpeningStockChild();
                    $stock_child->os_id = $os->id;
                    $stock_child->branch_id = $branch_id;
                    $stock_child->pro_id = $product->id;
                    $stock_child->warehouse_id = $warehouse->id;
                    $stock_child->qty = $row['quantity'];
                    $stock_child->save();

                    $check->stock = $check->stock + $row['quantity'];
                    $check->save();
                }else{
                    throw new \Exception("At Row ".$this->rowNumber.": ".$row['warehouse']." - Warehouse Inventory not found.");
                }
            }
        }
        $this->rowNumber++;
    }


    public function rules(): array
    {
        return [

            '*.product' => 'required',
            '*.warehouse' => 'required',
            '*.quantity' => 'required',

        ];
    }
}
