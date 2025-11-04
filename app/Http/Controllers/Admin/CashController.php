<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashInHand;

class CashController extends Controller
{
    public function updateCashInHand(Request $request)
    {
        $request->validate([
            'newCashInHand' => 'required|numeric|min:0',
        ]);

        $cashRecord = CashInHand::firstOrCreate(
            ['key' => 'cash in hand'],
            ['value' => 0]
        );

        $cashRecord->update(['value' => $request->newCashInHand]);

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cash-in-Hand updated successfully',
                'newValue' => $request->newCashInHand
            ]);
        }

        return redirect()->back();
    }
}
