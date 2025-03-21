<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'all');
        
        $query = Expense::where('user_id', $user->id);
        
        // Filter by type if specified
        if ($type === 'bill') {
            $query->bills();
        } elseif ($type === 'enhancement') {
            $query->enhancements();
        }
        
        // Apply category filter if provided
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        // Apply status filter if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Apply date range filter if provided
        if ($request->has('start_date') && $request->start_date) {
            $query->where('bill_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->where('bill_date', '<=', $request->end_date);
        }
        
        $expenses = $query->orderBy('bill_date', 'desc')->get();
        
        // Calculate statistics for the dashboard
        $stats = $this->calculateStats($user->id, $type);
        
        return response()->json([
            'expenses' => $expenses,
            'stats' => $stats
        ]);
    }

    /**
     * Get expense statistics for the dashboard
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stats(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'all');
        $stats = $this->calculateStats($user->id, $type);
        
        return response()->json($stats);
    }

    /**
     * Calculate statistics for expenses
     * 
     * @param int $userId
     * @param string $type
     * @return array
     */
    private function calculateStats($userId, $type = 'all')
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        // Get all expenses for the current month
        $query = Expense::where('user_id', $userId)
            ->whereBetween('bill_date', [$startOfMonth, $endOfMonth]);
        
        // Filter by type if specified
        if ($type === 'bill') {
            $query->bills();
        } elseif ($type === 'enhancement') {
            $query->enhancements();
        }
        
        $expenses = $query->get();
        
        // Calculate total expenses for this month
        $totalExpensesThisMonth = $expenses->sum('amount');
        
        // Calculate paid expenses
        $paidExpenses = $expenses->where('status', 'paid')->sum('amount');
        
        // Calculate pending expenses
        $pendingExpenses = $expenses->where('status', 'pending')->sum('amount');
        
        // Calculate overdue expenses
        $overdueExpenses = $expenses->where('status', 'overdue')->sum('amount');
        
        // Calculate average monthly (for the last 6 months)
        $sixMonthsAgo = $now->copy()->subMonths(6)->startOfMonth();
        $monthlyData = [];
        
        // Get monthly totals for the last 6 months
        for ($i = 0; $i < 6; $i++) {
            $month = $now->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $monthlyQuery = Expense::where('user_id', $userId)
                ->whereBetween('bill_date', [$monthStart, $monthEnd]);
            
            // Filter by type if specified
            if ($type === 'bill') {
                $monthlyQuery->bills();
            } elseif ($type === 'enhancement') {
                $monthlyQuery->enhancements();
            }
            
            $monthlyTotal = $monthlyQuery->sum('amount');
            
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'total' => $monthlyTotal
            ];
        }
        
        // Calculate average monthly
        $averageMonthly = count($monthlyData) > 0 ? array_sum(array_column($monthlyData, 'total')) / count($monthlyData) : 0;
        
        // Get category breakdown
        $categoryBreakdown = [];
        foreach (Expense::CATEGORIES as $key => $category) {
            $categoryQuery = Expense::where('user_id', $userId)
                ->where('category', $key)
                ->whereBetween('bill_date', [$startOfMonth, $endOfMonth]);
            
            // Filter by type if specified
            if ($type === 'bill') {
                $categoryQuery->bills();
            } elseif ($type === 'enhancement') {
                $categoryQuery->enhancements();
            }
            
            $amount = $categoryQuery->sum('amount');
            
            if ($amount > 0) {
                $categoryBreakdown[] = [
                    'category' => $key,
                    'label' => $category['label'],
                    'icon' => $category['icon'],
                    'amount' => $amount,
                    'percentage' => $totalExpensesThisMonth > 0 ? round(($amount / $totalExpensesThisMonth) * 100, 2) : 0
                ];
            }
        }
        
        // Sort categories by amount (descending)
        usort($categoryBreakdown, function($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });
        
        // Get calendar data (expenses grouped by date)
        $calendarData = [];
        $currentMonth = $now->format('Y-m');
        
        $calendarQuery = Expense::where('user_id', $userId)
            ->whereYear('bill_date', $now->year)
            ->whereMonth('bill_date', $now->month);
        
        // Filter by type if specified
        if ($type === 'bill') {
            $calendarQuery->bills();
        } elseif ($type === 'enhancement') {
            $calendarQuery->enhancements();
        }
        
        $calendarExpenses = $calendarQuery->get();
        
        foreach ($calendarExpenses as $expense) {
            $date = $expense->bill_date->format('Y-m-d');
            
            if (!isset($calendarData[$date])) {
                $calendarData[$date] = [
                    'date' => $date,
                    'expenses' => [],
                    'total' => 0
                ];
            }
            
            $calendarData[$date]['expenses'][] = $expense;
            $calendarData[$date]['total'] += $expense->amount;
        }
        
        // Convert to indexed array
        $calendarData = array_values($calendarData);
        
        return [
            'total_expenses_this_month' => $totalExpensesThisMonth,
            'paid_expenses' => $paidExpenses,
            'pending_expenses' => $pendingExpenses,
            'overdue_expenses' => $overdueExpenses,
            'average_monthly' => $averageMonthly,
            'monthly_data' => $monthlyData,
            'category_breakdown' => $categoryBreakdown,
            'calendar_data' => $calendarData
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
            'due_date' => 'nullable|date',
            'status' => 'nullable|string|in:paid,pending,overdue',
            'type' => 'required|string|in:bill,enhancement',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $user = Auth::user();
        $data['user_id'] = $user->id;
        
        // Get the first gym the user has access to if they don't have a specific gym_id
        if (!$user->gym_id) {
            $gymIds = $user->getAuthorizedGymIds();
            if (!empty($gymIds)) {
                $data['gym_id'] = $gymIds[0];
            } else {
                return response()->json(['message' => 'User does not have access to any gym'], 403);
            }
        } else {
            $data['gym_id'] = $user->gym_id;
        }

        // Set default status if not provided
        if (!isset($data['status']) || $data['status'] === null || $data['status'] === '') {
            $data['status'] = 'pending';
        }

        // For enhancements, due_date is not required
        if ($data['type'] === 'enhancement' && (!isset($data['due_date']) || $data['due_date'] === null)) {
            $data['due_date'] = null;
        }

        // Check if the bill is overdue (only for bills, not enhancements)
        if ($data['type'] === 'bill' && $data['status'] === 'pending' && isset($data['due_date']) && $data['due_date']) {
            $dueDate = Carbon::parse($data['due_date']);
            $today = Carbon::today();
            
            if ($dueDate < $today) {
                $data['status'] = 'overdue';
            }
        }

        $expense = Expense::create($data);

        return response()->json([
            'message' => 'Expense created successfully',
            'expense' => $expense
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
        $expense = Expense::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        return response()->json(['expense' => $expense]);
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
        $expense = Expense::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'bill_date' => 'required|date',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string|in:paid,pending,overdue',
            'type' => 'required|string|in:bill,enhancement',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $user = Auth::user();
        
        // Ensure gym_id is preserved from the existing expense
        if ($expense->gym_id) {
            $data['gym_id'] = $expense->gym_id;
        } else {
            // If somehow the expense doesn't have a gym_id, try to set it
            $gymIds = $user->getAuthorizedGymIds();
            if (!empty($gymIds)) {
                $data['gym_id'] = $gymIds[0];
            } else {
                return response()->json(['message' => 'User does not have access to any gym'], 403);
            }
        }
        
        // Check if the bill is overdue (only for bills, not enhancements)
        if ($expense->type === 'bill' && $expense->status === 'pending') {
            $dueDate = $request->has('due_date') ? Carbon::parse($request->due_date) : Carbon::parse($expense->due_date);
            $today = Carbon::today();
            
            if ($dueDate && $dueDate < $today) {
                $data['status'] = 'overdue';
            }
        }

        $expense->update($data);

        return response()->json([
            'message' => 'Expense updated successfully',
            'expense' => $expense
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
        $expense = Expense::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully']);
    }
}
