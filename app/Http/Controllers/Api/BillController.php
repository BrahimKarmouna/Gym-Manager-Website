<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Gym;
use App\Models\GymUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BillController extends Controller
{
    /**
     * Display a listing of bills.
     */
    public function index(Request $request)
    {
        // Get authorized gym IDs for the current user
        $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
        
        // Build query for bills
        $query = Bill::with('gym')
            ->where('user_id', Auth::id())
            ->whereIn('gym_id', $authorizedGymIds);
            
        // Apply filters if provided
        if ($request->has('month') && $request->has('year')) {
            // Filter by specific month and year
            $year = $request->query('year');
            $month = $request->query('month');
            $query->whereYear('bill_date', $year)
                ->whereMonth('bill_date', $month);
        } elseif ($request->has('from_date') && $request->has('to_date')) {
            // Filter by date range
            $query->whereBetween('bill_date', [$request->query('from_date'), $request->query('to_date')]);
        } elseif ($request->has('status')) {
            // Filter by payment status
            $query->where('status', $request->query('status'));
        }
        
        // Get bills with sorting
        $bills = $query->orderBy('bill_date', 'desc')->get();
            
        return response()->json([
            'bills' => $bills,
            'stats' => $this->getStatistics(),
        ]);
    }

    /**
     * Get bill statistics for dashboard
     */
    public function getStats()
    {
        $userId = Auth::id();
        $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
        $now = Carbon::now();
        
        // Calculate total bills this month
        $totalBillsThisMonth = Bill::where('user_id', $userId)
            ->whereIn('gym_id', $authorizedGymIds)
            ->whereYear('bill_date', $now->year)
            ->whereMonth('bill_date', $now->month)
            ->sum('amount');
            
        // Calculate paid bills
        $paidBills = Bill::where('user_id', $userId)
            ->whereIn('gym_id', $authorizedGymIds)
            ->where('status', 'paid')
            ->sum('amount');
            
        // Calculate pending bills
        $pendingBills = Bill::where('user_id', $userId)
            ->whereIn('gym_id', $authorizedGymIds)
            ->where('status', 'pending')
            ->sum('amount');
            
        // Calculate average monthly (last 6 months)
        $sixMonthsAgo = $now->copy()->subMonths(6);
        $totalAmount = Bill::where('user_id', $userId)
            ->whereIn('gym_id', $authorizedGymIds)
            ->where('bill_date', '>=', $sixMonthsAgo)
            ->sum('amount');
            
        $averageMonthly = $totalAmount / 6;
        
        // Get monthly totals for the past 6 months (for chart)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthlyTotal = Bill::where('user_id', $userId)
                ->whereIn('gym_id', $authorizedGymIds)
                ->whereYear('bill_date', $month->year)
                ->whereMonth('bill_date', $month->month)
                ->sum('amount');
                
            $monthlyData[] = [
                'month' => $month->format('M'),
                'year' => $month->format('Y'),
                'total' => $monthlyTotal,
            ];
        }
        
        return response()->json([
            'total_bills_this_month' => round($totalBillsThisMonth, 2),
            'paid_bills' => round($paidBills, 2),
            'pending_bills' => round($pendingBills, 2),
            'average_monthly' => round($averageMonthly, 2),
            'monthly_data' => $monthlyData,
        ]);
    }
    
    /**
     * Get statistics for the bills
     */
    private function getStatistics()
    {
        $userId = Auth::id();
        $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
        $now = Carbon::now();
        
        // This month's total
        $thisMonthTotal = Bill::where('user_id', $userId)
            ->whereIn('gym_id', $authorizedGymIds)
            ->whereYear('bill_date', $now->year)
            ->whereMonth('bill_date', $now->month)
            ->sum('amount');
            
        // Last month's total
        $lastMonth = $now->copy()->subMonth();
        $lastMonthTotal = Bill::where('user_id', $userId)
            ->whereIn('gym_id', $authorizedGymIds)
            ->whereYear('bill_date', $lastMonth->year)
            ->whereMonth('bill_date', $lastMonth->month)
            ->sum('amount');
            
        // Percentage change
        $percentChange = $lastMonthTotal > 0 
            ? (($thisMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100 
            : 0;
            
        return [
            'this_month_total' => round($thisMonthTotal, 2),
            'last_month_total' => round($lastMonthTotal, 2),
            'percent_change' => round($percentChange, 1),
        ];
    }

    /**
     * Store a newly created bill.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'bill_date' => 'required|date',
            'status' => 'nullable|string|in:paid,pending,overdue',
            'due_date' => 'nullable|date',
            'category' => 'nullable|string',
        ]);

        // Get authenticated user's ID
        $userId = Auth::id();
        
        // Find the gym associated with this user using the GymUser model
        $gymUser = GymUser::where('user_id', $userId)->first();
        
        if (!$gymUser) {
            return response()->json(['message' => 'No gym associated with this user'], 422);
        }
        
        // Create the bill with the user ID and gym ID
        $data = $request->all();
        $data['gym_id'] = $gymUser->gym_id;
        $data['user_id'] = $userId;
        
        // Ensure status is a valid string
        if (!isset($data['status']) || $data['status'] === null || $data['status'] === '') {
            $data['status'] = 'pending';
        } else {
            // Force status to be a string and validate it's one of the allowed values
            $data['status'] = (string) $data['status'];
            if (!in_array($data['status'], ['paid', 'pending', 'overdue'])) {
                $data['status'] = 'pending';
            }
        }
        
        $bill = Bill::create($data);

        return response()->json([
            'message' => 'Bill created successfully',
            'bill' => $bill
        ], 201);
    }

    /**
     * Display the specified bill.
     */
    public function show($id)
    {
        // Find bill by ID and ensure it belongs to the authenticated user
        $bill = Bill::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('gym')
            ->first();
            
        if (!$bill) {
            return response()->json(['message' => 'Bill not found or you do not have permission to view this bill'], 404);
        }
        
        return response()->json($bill);
    }

    /**
     * Update the specified bill.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'bill_date' => 'required|date',
            'status' => 'nullable|string|in:paid,pending,overdue',
            'due_date' => 'nullable|date',
            'category' => 'nullable|string',
        ]);

        // Get authenticated user's ID
        $userId = Auth::id();
        
        // Find the bill that belongs to the user
        $bill = Bill::where('id', $id)
            ->where('user_id', $userId)
            ->first();
            
        if (!$bill) {
            return response()->json(['message' => 'Bill not found or unauthorized'], 404);
        }
        
        // Update bill details (without changing the gym_id)
        $bill->description = $request->input('description');
        $bill->amount = $request->input('amount');
        $bill->bill_date = $request->input('bill_date');
        
        // Ensure status is a valid string
        $status = $request->input('status');
        if (!$status || $status === '') {
            $bill->status = 'pending';
        } else {
            // Force status to be a string and validate it's one of the allowed values
            $status = (string) $status;
            if (in_array($status, ['paid', 'pending', 'overdue'])) {
                $bill->status = $status;
            } else {
                $bill->status = 'pending';
            }
        }
        
        $bill->due_date = $request->input('due_date');
        $bill->category = $request->input('category');
        $bill->save();
        
        return response()->json([
            'message' => 'Bill updated successfully',
            'bill' => $bill
        ]);
    }

    /**
     * Remove the specified bill.
     */
    public function destroy($id)
    {
        // Find the bill and verify ownership
        $bill = Bill::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$bill) {
            return response()->json(['message' => 'Bill not found or you do not have permission to delete this bill'], 404);
        }
        
        $bill->delete();
        
        return response()->json([
            'message' => 'Bill deleted successfully'
        ]);
    }
}
