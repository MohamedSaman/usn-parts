<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductDetail;

class ProductsExportController extends Controller
{
    public function export()
    {
        $Productes = ProductDetail::join('Product_suppliers', 'Product_details.supplier_id', '=', 'Product_suppliers.id')
            ->join('Product_prices', 'Product_details.id', '=', 'Product_prices.Product_id')
            ->join('Product_stocks', 'Product_details.id', '=', 'Product_stocks.Product_id')
            ->select(
                'Product_details.id',
                'Product_details.code',
                'Product_details.name as Product_name',
                'Product_details.model',
                'Product_details.brand',
                'Product_details.color',
                'Product_details.made_by',
                'Product_details.category',
                'Product_details.gender',
                'Product_details.type',
                'Product_details.movement',
                'Product_details.dial_color',
                'Product_details.strap_color',
                'Product_details.strap_material',
                'Product_details.case_diameter_mm',
                'Product_details.case_thickness_mm',
                'Product_details.glass_type',
                'Product_details.water_resistance',
                'Product_details.warranty',
                'Product_details.barcode',
                'Product_details.status',
                'Product_prices.supplier_price',
                'Product_prices.selling_price',
                'Product_prices.discount_price',
                'Product_stocks.shop_stock',
                'Product_stocks.store_stock',
                'Product_stocks.damage_stock',
                'Product_stocks.total_stock',
                'Product_stocks.available_stock',
                'Product_suppliers.name as supplier_name'
            )
            ->orderBy('Product_details.created_at', 'desc')
            ->get();

        $filename = 'Productes_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($Productes) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID', 'Code', 'Name', 'Model', 'Brand', 'Color', 'Made By',
                'Category', 'Gender', 'Type', 'Movement', 'Dial Color',
                'Strap Color', 'Strap Material', 'Case Diameter (mm)',
                'Case Thickness (mm)', 'Glass Type', 'Water Resistance',
                'Warranty', 'Barcode', 'Status', 'Supplier Price',
                'Selling Price', 'Discount Price', 'Shop Stock',
                'Store Stock', 'Damage Stock', 'Total Stock',
                'Available Stock', 'Supplier'
            ]);
            foreach ($Productes as $Product) {
                fputcsv($handle, [
                    $Product->id,
                    $Product->code,
                    $Product->Product_name,
                    $Product->model,
                    $Product->brand,
                    $Product->color,
                    $Product->made_by,
                    $Product->category,
                    $Product->gender,
                    $Product->type,
                    $Product->movement,
                    $Product->dial_color,
                    $Product->strap_color,
                    $Product->strap_material,
                    $Product->case_diameter_mm,
                    $Product->case_thickness_mm,
                    $Product->glass_type,
                    $Product->water_resistance,
                    $Product->warranty,
                    $Product->barcode,
                    $Product->status,
                    $Product->supplier_price,
                    $Product->selling_price,
                    $Product->discount_price,
                    $Product->shop_stock,
                    $Product->store_stock,
                    $Product->damage_stock,
                    $Product->total_stock,
                    $Product->available_stock,
                    $Product->supplier_name
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
