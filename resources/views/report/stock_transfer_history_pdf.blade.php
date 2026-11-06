<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        body{
            font-family: "verdana", sans-serif;
            font-size: 12px !important;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        table, th, td{
            border:1px solid #ccc;
        }

        th{
            background:#f2f2f2;
            color:#333;
            font-weight:bold;
            text-align:center;
            padding:10px;
        }

        td{
            padding:8px;
            text-align:left;
        }

        tfoot{
            background:#e8f0fe;
            font-weight:bold;
        }

        tfoot td,
        tfoot th{
            text-align:center;
        }
    </style>

    <title>Stock Transfer History</title>
</head>

<body>

<h3>
    Stock Transfer History (
    Branch:
    <span style="color:#ff9800;">{{ $product->branch->name }}</span>
    &nbsp; | &nbsp;

    Category:
    <span style="color:#009688;">{{ $product->productcategory->name }}</span>
    &nbsp; | &nbsp;

    Company:
    <span style="color:#673ab7;">{{ $product->company->name }}</span>
    &nbsp; | &nbsp;

    Product:
    <span style="color:#e91e63;">{{ $product->product }}</span>
    &nbsp; | &nbsp;

    SKU Code:
    <span style="color:#9C27B0;">{{ $product->code }}</span>
    )
</h3>

@php
    $i = 1;
      $showEmployeeColumn = $transferHistory->contains(function ($row) {
        return !empty($row->employee_id);
    });
@endphp

<table>

    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Date</th>
            <th>Branch</th>
            <th>Category</th>
            <th>Company</th>
            <th>Product</th>
            <th>From Warehouse</th>
            <th>To Warehouse</th>
           <th>Inward</th>
            <th>Outward</th>
             @if($showEmployeeColumn)
                <th>Employee Name</th>
            @endif
            <th>Remark</th>
        </tr>
    </thead>

    <tbody>

    @forelse($transferHistory as $row)

    <tr>

        <td style="text-align:center;">
            {{ $i++ }}
        </td>

        <td>
            {{ date('d-m-Y', strtotime($row->transfer_date)) }}
        </td>

        <td>
            {{ $row->branch->name ?? '-' }}
        </td>

        <td>
            {{ $row->product->productcategory->name ?? '-' }}
        </td>

        <td>
            {{ $row->product->company->name ?? '-' }}
        </td>

        <td>
            {{ $row->product->product ?? '-' }}
        </td>

        <td>
            {{ $row->fromWarehouse->name ?? '-' }}
        </td>

        <td>
            {{ $row->toWarehouse->name ?? '-' }}
        </td>

        <td style="text-align:right;">
            {{ number_format($row->inward, 2) }}
        </td>

        <td style="text-align:right;">
            {{ number_format($row->outward, 2) }}
        </td>
        @if($showEmployeeColumn)
            <td>
                @if($row->employee_id)
                    {{ $row->employee->name ?? '-' }}
                @else
                    -
                @endif
            </td>
        @endif

        <td>
            {{ $row->remark }}
        </td>

    </tr>

    @empty

    <tr>
       <td colspan="{{ $showEmployeeColumn ? 12 : 11 }}"
            style="text-align:center;">
            No Stock Transfer History Found.
        </td>
    </tr>

    @endforelse

    </tbody>

    <tfoot>

    <tr>

       <th colspan="8" style="text-align:right;">
            Total
        </th>

        <th style="text-align:right;">
            {{ number_format($transferHistory->sum('inward'), 2) }}
        </th>

        <th style="text-align:right;">
            {{ number_format($transferHistory->sum('outward'), 2) }}
        </th>

        @if($showEmployeeColumn)
            <th></th>
        @endif

        <th></th>
    </tr>

    </tfoot>

</table>

</body>
</html>