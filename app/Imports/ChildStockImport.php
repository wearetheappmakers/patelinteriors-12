<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Product;
use App\WarehouseInventory;
use App\Warehouse;
use App\OpeningStockChild;
use Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ChildStockImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading
{
    private $param;
    private $branch_id;

    public function __construct($param)
    {
        $this->param = $param;
        $this->branch_id = Auth::user()->role == 2 ? Auth::user()->id : null;
    }

    public function collection(Collection $rows)
    {
        $productCodes = $rows->pluck('product')->map(fn($v) => trim(strtolower($v)))->unique();
        $warehouseNames = $rows->pluck('warehouse')->map(fn($v) => trim(strtolower($v)))->unique();

        $products = Product::where('branch_id', $this->branch_id)
            ->whereIn(\DB::raw('LOWER(TRIM(code))'), $productCodes)
            ->get()
            ->keyBy(fn($p) => trim(strtolower($p->code)));

        $warehouses = Warehouse::where('branch_id', $this->branch_id)
            ->whereIn(\DB::raw('LOWER(TRIM(name))'), $warehouseNames)
            ->get()
            ->keyBy(fn($w) => trim(strtolower($w->name)));

        $productIds = $products->pluck('id')->toArray();
        $warehouseIds = $warehouses->pluck('id')->toArray();

        $warehouseInventories = WarehouseInventory::where('branch_id', $this->branch_id)
            ->whereIn('product_id', $productIds)
            ->whereIn('warehouse_id', $warehouseIds)
            ->get()
            ->keyBy(fn($i) => $i->product_id . '_' . $i->warehouse_id);

        $openingStockChildren = OpeningStockChild::where('os_id', $this->param['id'])
            ->where('branch_id', $this->branch_id)
            ->whereIn('pro_id', $productIds)
            ->whereIn('warehouse_id', $warehouseIds)
            ->get()
            ->keyBy(fn($o) => $o->pro_id . '_' . $o->warehouse_id);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // considering header row

            $productCode = trim(strtolower($row['product']));
            $warehouseName = trim(strtolower($row['warehouse']));
            $quantity = (float) $row['quantity'];

            $product = $products[$productCode] ?? null;
            if (!$product) throw new \Exception("At Row {$rowNumber}: Product '{$row['product']}' not found.");

            $warehouse = $warehouses[$warehouseName] ?? null;
            if (!$warehouse) throw new \Exception("At Row {$rowNumber}: Warehouse '{$row['warehouse']}' not found.");

            $key = $product->id . '_' . $warehouse->id;

            $inventory = $warehouseInventories[$key] ?? null;
            if (!$inventory) throw new \Exception("At Row {$rowNumber}: Warehouse Inventory not found.");

            $openingStockChild = $openingStockChildren[$key] ?? null;

            if ($openingStockChild) {
                $inventory->stock = ($inventory->stock - $openingStockChild->qty) + $quantity;
                $openingStockChild->qty = $quantity;

                $openingStockChild->save();
                $inventory->save();
            } else {
                $stockChild = new OpeningStockChild();
                $stockChild->os_id = $this->param['id'];
                $stockChild->branch_id = $this->branch_id;
                $stockChild->pro_id = $product->id;
                $stockChild->warehouse_id = $warehouse->id;
                $stockChild->qty = $quantity;
                $stockChild->save();

                $inventory->stock = $inventory->stock + $quantity;
                $inventory->save();
            }
        }
    }

    public function rules(): array
    {
        return [
            '*.product' => 'required',
            '*.warehouse' => 'required',
            '*.quantity' => 'required|numeric',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }
}