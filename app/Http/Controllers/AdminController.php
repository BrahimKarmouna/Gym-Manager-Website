<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        // Ensure user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        $totalUsers = User::count();
        $totalOrders = Order::count();
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();
        $revenueTotal = Order::where('payment_status', 'paid')->sum('total_amount');

        return view('admin.dashboard', compact('totalUsers', 'totalOrders', 'recentOrders', 'revenueTotal'));
    }

    /**
     * Display orders management page.
     */
    public function orders()
    {
        // Ensure user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        $orders = Order::with(['user', 'items'])->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.orders', compact('orders'));
    }

    /**
     * Display users management page.
     */
    public function users()
    {
        // Ensure user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }
        
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.users', compact('users'));
    }
}
