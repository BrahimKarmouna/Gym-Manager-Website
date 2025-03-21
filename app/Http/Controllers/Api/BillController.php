<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $bills = Bill::where('user_id', $user->id)->get();
        
        // Calculate statistics for the dashboard
        $stats = $this->calculateStats($user->id);
        
        return response()->json([
            'bills' => $bills,
            'stats' => $stats
        ]);
    }

    /**
     * Get bill statistics for the dashboard
     * 
     * @return \Illuminate\Http\Response
     */
    public function stats()
    {
        $user = Auth::user();
        $stats = $this->calculateStats($user->id);
        
        return response()->json($stats);
    }

    /**
     * Calculate statistics for bills
     * 
     * @param int $userId
     * @return array
     */
    private function calculateStats($userId)
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        // Get all bills for the current month
        $bills = Bill::where('user_id', $userId)
            ->whereBetween('bill_date', [$startOfMonth, $endOfMonth])
            ->get();
        
        // Calculate total bills for this month
        $totalBillsThisMonth = $bills->sum('amount');
        
        // Calculate paid bills
        $paidBills = $bills->where('status', 'paid')->sum('amount');
        
        // Calculate pending bills
        $pendingBills = $bills->where('status', 'pending')->sum('amount');
        
        // Calculate average monthly (for the last 6 months)
        $sixMonthsAgo = $now->copy()->subMonths(6)->startOfMonth();
        $monthlyData = [];
        
        // Get monthly totals for the last 6 months
        for ($i = 0; $i < 6; $i++) {
            $month = $now->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $monthlyTotal = Bill::where('user_id', $userId)
                ->whereBetween('bill_date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'total' => $monthlyTotal
            ];
        }
        
        // Calculate average monthly
        $averageMonthly = count($monthlyData) > 0 ? array_sum(array_column($monthlyData, 'total')) / count($monthlyData) : 0;
        
        return [
            'total_bills_this_month' => $totalBillsThisMonth,
            'paid_bills' => $paidBills,
            'pending_bills' => $pendingBills,
            'average_monthly' => $averageMonthly,
            'monthly_data' => $monthlyData
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'bill_date' => 'required|date',
            'due_date' => 'required|date',
            'status' => 'nullable|string|in:paid,pending,overdue',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();

        // Set default status if not provided
        if (!isset($data['status']) || $data['status'] === null || $data['status'] === '') {
            $data['status'] = 'pending';
        }

        // Check if the bill is overdue
        $dueDate = Carbon::parse($data['due_date']);
        $today = Carbon::today();
        
        if ($data['status'] === 'pending' && $dueDate < $today) {
            $data['status'] = 'overdue';
        }

        $bill = Bill::create($data);

        return response()->json([
            'message' => 'Bill created successfully',
            'bill' => $bill
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bill = Bill::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        return response()->json(['bill' => $bill]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bill = Bill::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'category' => 'sometimes|required|string|max:50',
            'bill_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date',
            'status' => 'nullable|string|in:paid,pending,overdue',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Handle status explicitly to ensure it's properly updated
        $status = $request->input('status');
        if (!$status || $status === '') {
            $bill->status = 'pending';
        } else {
            $bill->status = $status;
        }

        // Check if the bill is overdue (only if status is pending)
        if ($bill->status === 'pending') {
            $dueDate = $request->has('due_date') ? Carbon::parse($request->due_date) : Carbon::parse($bill->due_date);
            $today = Carbon::today();
            
            if ($dueDate < $today) {
                $bill->status = 'overdue';
            }
        }

        // Update other fields
        if (isset($data['description'])) $bill->description = $data['description'];
        if (isset($data['amount'])) $bill->amount = $data['amount'];
        if (isset($data['category'])) $bill->category = $data['category'];
        if (isset($data['bill_date'])) $bill->bill_date = $data['bill_date'];
        if (isset($data['due_date'])) $bill->due_date = $data['due_date'];

        $bill->save();

        return response()->json([
            'message' => 'Bill updated successfully',
            'bill' => $bill
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bill = Bill::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        $bill->delete();

        return response()->json(['message' => 'Bill deleted successfully']);
    }
}
