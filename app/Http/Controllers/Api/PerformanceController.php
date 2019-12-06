<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Invoice;

use stdClass;

class PerformanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('app.version');
        $this->middleware('token');
    }

    /**
     * Get all verticals.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotalAchievements()
    {
        $invoices = Invoice::where('user_id', request()->user->_id)
                            ->select('total_amount')
                            ->get();

        $totalAchievements = 0;

        foreach($invoices as $invoice) {
            $totalAchievements += floatval($invoice->total_amount);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_achievements' => $totalAchievements
            ],
            'errors' => new stdClass
        ]);
    }
}
