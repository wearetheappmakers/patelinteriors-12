<?php

namespace App\Exports;

use App\WarehouseInventory;
use App\OpeningStock;
use App\OpeningStockChild;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class InventoryExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading
{
    protected $branch_id;
    protected $warehouse_id;
    protected $product_id;
    protected $productcategory_id;
    protected $company_id;

    protected $openingStocks = [];

    public function __construct(
        $branch_id,
        $warehouse_id = null,
        $product_id = null,
        $productcategory_id = null,
        $company_id = null
    ) {
        $this->branch_id = $branch_id;
        $this->warehouse_id = $warehouse_id;
        $this->product_id = $product_id;
        $this->productcategory_id = $productcategory_id;
        $this->company_id = $company_id;

        /*
         * Current financial year
         */
        if (date('m') >= 4) {
            $this->financial_year = date('Y') . '-' . (date('y') + 1);
        } else {
            $this->financial_year = (date('Y') - 1) . '-' . date('y');
        }

        /*
         * Load Opening Stock Main records only once.
         */
        $mainQuery = OpeningStock::where(
            'fy_year',
            $this->financial_year
        );

        /*
         * If a branch is selected, only load that branch.
         * This keeps the query small.
         */
        if (!empty($this->branch_id)) {
            $mainQuery->where('branch_id', $this->branch_id);
        }

        $mainRecords = $mainQuery->get();

        /*
         * Load Opening Stock Child records for those Opening Stock IDs.
         */
        if ($mainRecords->count() > 0) {

            $mainIds = $mainRecords->pluck('id')->toArray();

            $children = OpeningStockChild::whereIn('os_id', $mainIds)
                ->get();

            foreach ($children as $child) {

                /*
                 * Unique combination:
                 *
                 * branch + product + warehouse
                 *
                 * This is the same combination used
                 * in your existing export logic.
                 */
                $key =
                    $child->branch_id . '_' .
                    $child->pro_id . '_' .
                    $child->warehouse_id;

                $this->openingStocks[$key] = $child->qty ?? 0;
            }
        }
    }

    /**
     * Main inventory query.
     */
    public function query()
    {
        $query = WarehouseInventory::query()

            ->leftJoin(
                'warehouses',
                'warehouse_inventories.warehouse_id',
                '=',
                'warehouses.id'
            )

            ->leftJoin(
                'product',
                'warehouse_inventories.product_id',
                '=',
                'product.id'
            )

            ->leftJoin(
                'companies',
                'product.company_id',
                '=',
                'companies.id'
            )

            ->leftJoin(
                'product_category',
                'product.productcategory_id',
                '=',
                'product_category.id'
            )

            ->leftJoin('users', function ($join) {

                $join->on(
                    'warehouse_inventories.branch_id',
                    '=',
                    'users.id'
                )

                ->where('users.role', 2);
            })

            ->select(
                'warehouse_inventories.*',
                'warehouses.name as warehouse_name',
                'product.product as product_name',
                'product.code as product_code',
                'companies.name as company_name',
                'product_category.name as category_name',
                'users.name as branch_name'
            );

        /*
         * Branch filter
         */
        if (!empty($this->branch_id)) {

            $query->where(
                'warehouse_inventories.branch_id',
                $this->branch_id
            );
        }

        /*
         * Warehouse filter
         */
        if (!empty($this->warehouse_id)) {

            $query->where(
                'warehouse_inventories.warehouse_id',
                $this->warehouse_id
            );
        }

        /*
         * Product filter
         */
        if (
            !empty($this->product_id) &&
            $this->product_id != 'undefined'
        ) {

            $query->where(
                'warehouse_inventories.product_id',
                $this->product_id
            );
        }

        /*
         * Product Category filter
         */
        if (!empty($this->productcategory_id)) {

            $query->where(
                'product.productcategory_id',
                $this->productcategory_id
            );
        }

        /*
         * Company filter
         */
        if (!empty($this->company_id)) {

            $query->where(
                'product.company_id',
                $this->company_id
            );
        }

        /*
         * IMPORTANT:
         * Keep ordering by primary key.
         *
         * This makes chunk processing safer.
         */
        return $query->orderBy(
            'warehouse_inventories.id',
            'asc'
        );
    }

    /**
     * Excel headings.
     */
    public function headings(): array
    {
        return [
            'Branch',
            'Warehouse Name',
            'Category',
            'Company',
            'Product Name',
            'Product Code',
            'Opening Stock',
            'Purchase',
            'Sale',
            'SI Blocking Qty',
            'Closing Stock',
        ];
    }

    /**
     * Map inventory row to Excel row.
     */
    public function map($row): array
    {
        /*
         * Opening Stock
         *
         * Same logic as before:
         * branch + product + warehouse
         */
        $key =
            $row->branch_id . '_' .
            $row->product_id . '_' .
            $row->warehouse_id;

        $opening_stock = isset($this->openingStocks[$key])
            ? $this->openingStocks[$key]
            : 0;

        /*
         * Purchase
         */
        $stock = $row->stock ?? 0;

        $purchase = max(
            0,
            $stock - $opening_stock
        );

        /*
         * Sale
         */
        $sale = $row->used ?? 0;

        /*
         * SI Blocking Qty
         *
         * This now comes directly from
         * warehouse_inventories.si_blocking_qty.
         */
        $si_blocking = $row->si_blocking_qty ?? 0;

        /*
         * Closing Stock
         */
        $closing =
            $stock
            - $sale
            - $si_blocking;

        return [
            $row->branch_name,
            $row->warehouse_name,
            $row->category_name,
            $row->company_name,
            $row->product_name,
            $row->product_code,
            $opening_stock,
            $purchase,
            $sale,
            $si_blocking,
            $closing,
        ];
    }

    /**
     * Process the export in chunks.
     *
     * 5000 is a safe starting point for your live server.
     */
    public function chunkSize(): int
    {
        return 5000;
    }
}