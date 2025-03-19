<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Insurance;
use Carbon\Carbon;

class RevenueController extends Controller
{
    public function monthlyRevenue()
    {
        // Data for the current and previous periods
        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create(Carbon::now()->year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $monthlyData[] = [
                'month' => $startDate->format('F'),
                'revenue' => $this->calculateRevenue($startDate, $endDate),
            ];
        }

        return response()->json([
            'total_revenue_current_month' => $this->calculateRevenue(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()),
            'total_revenue_last_month' => $this->calculateRevenue(Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()),
            'total_revenue_current_year' => $this->calculateRevenue(Carbon::now()->startOfYear(), Carbon::now()->endOfYear()),
            'total_revenue_last_year' => $this->calculateRevenue(Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()),
            'monthly_revenue' => $monthlyData,  // Add all months' data here
        ]);
    }

    private function calculateRevenue($startDate, $endDate)
    {
        $payments = Payment::with('plan')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $totalPayments = $payments->sum(fn($payment) => $payment->plan ? $payment->plan->price : 0);

        $insurances = Insurance::with('insurance_plan')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $totalInsurances = $insurances->sum(fn($insurance) => $insurance->insurance_plan ? $insurance->insurance_plan->price : 0);

        return $totalPayments + $totalInsurances;
    }
}
