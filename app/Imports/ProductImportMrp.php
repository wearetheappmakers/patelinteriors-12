<?php
namespace App\Imports;
use Illuminate\Support\Collection;
use App\ProductCategory;
use App\Product;
use App\WarehouseInventory;
use App\Warehouse;
use App\Company;
use App\User;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Auth;

class ProductImportMrp implements ToModel,WithHeadingRow,WithValidation
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

        $chk_product = Product::where('code',$row['sku_code'])->where('branch_id',$branch->id)->first();
        if(!isset($chk_product))
        {
            throw new \Exception("At Row ".$this->rowNumber.": ".$row['sku_code']." Wrong SKU Code.");
        }

        $chk_product->update([
            $chk_product->price = $row['product_price']
        ]);

        $this->rowNumber++;
    }
    public function rules(): array
    {
        if(Auth::user()->role == 1)
        {
            return [
                '*.branch' => 'required',
                '*.sku_code' => 'required',
                '*.product_price' => 'required',
            ];
        }
        else
        {
            return [
                '*.sku_code' => 'required',
                '*.product_price' => 'required',
            ];
        }
    }
}
