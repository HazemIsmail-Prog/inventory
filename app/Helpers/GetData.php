<?php

namespace App\Helpers;

use App\Models\Item;
use App\Models\Warehouse;

class GetData
{
    public static function getAvailableItems(Warehouse $warehouse)
    {
        $items = Warehouse::query()
            ->where('warehouses.id', $warehouse->id)
            ->join('transactions', function ($join) {
                $join->on('warehouses.id', '=', 'transactions.destination_warehouse_id')
                    ->orOn('warehouses.id', '=', 'transactions.source_warehouse_id');
            })
            ->join('item_transaction', 'item_transaction.transaction_id', '=', 'transactions.id')
            ->join('items', 'item_transaction.item_id', '=', 'items.id')
            ->select('items.id', 'items.name', 'items.unit', 'item_transaction.expiration_date')
            ->selectRaw("
                SUM(CASE WHEN transactions.destination_warehouse_id = warehouses.id THEN item_transaction.quantity ELSE 0 END) - 
                SUM(CASE WHEN transactions.source_warehouse_id = warehouses.id THEN item_transaction.quantity ELSE 0 END) as net_quantity
            ")
            ->groupBy('warehouses.id', 'item_transaction.item_id', 'item_transaction.expiration_date')
            ->having('net_quantity', '>', 0) // Filter for net_quantity > 0
            ->orderBy('items.name')
            ->get();

        // Return the items directly
        return [
            'success' => $items->isNotEmpty(),
            'data' => $items->isNotEmpty() ? $items : null,
            'message' => $items->isEmpty() ? 'No items found for this warehouse.' : null,
        ];
    }

    public static function getItemsList()
    {

        $items = Item::query()
            ->orderBy('name')
            ->get();

        // Check if the warehouse has items
        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No items found for this warehouse.',
            ], 404); // Not Found status
        }

        // Return a JSON response with a success status
        return response()->json([
            'success' => true,
            'data' => $items, // Return the items
        ]);
    }
}
