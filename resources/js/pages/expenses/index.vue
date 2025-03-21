<template>
  <div class="bg-gray-50 min-h-screen p-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Expenses Management</h1>
      <div class="flex space-x-3">
        <button 
          @click="viewMode = 'list'" 
          class="px-4 py-2 rounded-md" 
          :class="viewMode === 'list' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
        >
          <span class="material-icons-outlined mr-1">list</span>
          List
        </button>
        <button 
          @click="viewMode = 'calendar'; setupCalendarDays()" 
          class="px-4 py-2 rounded-md" 
          :class="viewMode === 'calendar' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
        >
          <span class="material-icons-outlined mr-1">calendar_today</span>
          Calendar
        </button>
        <button 
          @click="showAddModal = true; selectedExpense = null" 
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <span class="material-icons-outlined mr-1">add</span>
          Add Expense
        </button>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="changeTab('bill')"
            class="py-4 px-1 border-b-2 font-medium text-sm"
            :class="activeTab === 'bill' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          >
            <span class="material-icons-outlined mr-1">receipt</span>
            Bills
          </button>
          <button
            @click="changeTab('enhancement')"
            class="py-4 px-1 border-b-2 font-medium text-sm"
            :class="activeTab === 'enhancement' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          >
            <span class="material-icons-outlined mr-1">construction</span>
            Gym Enhancements
          </button>
        </nav>
      </div>
    </div>

    <!-- Loading Indicator -->
    <div v-if="isLoading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
    </div>

    <template v-else>
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-500 mr-4">
              <span class="material-icons-outlined">payments</span>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-500">Total This Month</p>
              <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.total_expenses_this_month) }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
              <span class="material-icons-outlined">check_circle</span>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-500">Paid</p>
              <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.paid_expenses) }}</p>
            </div>
          </div>
        </div>
        
        <div v-if="activeTab === 'bill'" class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
              <span class="material-icons-outlined">pending</span>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-500">Pending</p>
              <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.pending_expenses) }}</p>
            </div>
          </div>
        </div>
        
        <div v-if="activeTab === 'bill'" class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-500 mr-4">
              <span class="material-icons-outlined">warning</span>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-500">Overdue</p>
              <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.overdue_expenses) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 md:col-span-2">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Trend</h3>
          <div class="h-64">
            <canvas ref="monthlyChart"></canvas>
          </div>
        </div>
        
        <div v-if="stats.category_breakdown.length > 0" class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Category Breakdown</h3>
          <div class="h-64">
            <canvas ref="categoryChart"></canvas>
          </div>
        </div>
      </div>

      <!-- List View -->
      <div v-if="viewMode === 'list'">
        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-lg mb-8 p-6 border border-gray-100">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <select 
                v-model="filters.category" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="">All Categories</option>
                <option v-for="(category, key) in categoryOptions" :key="key" :value="key">
                  {{ category.label }}
                </option>
              </select>
            </div>
            
            <div v-if="activeTab === 'bill'">
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select 
                v-model="filters.status" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="">All Statuses</option>
                <option value="paid">Paid</option>
                <option value="pending">Pending</option>
                <option value="overdue">Overdue</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
              <input 
                type="date" 
                v-model="filters.fromDate" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
              <input 
                type="date" 
                v-model="filters.toDate" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
            </div>
          </div>
          
          <div class="flex justify-between">
            <div class="relative flex-1 max-w-xs">
              <input 
                type="text" 
                v-model="filters.search" 
                placeholder="Search expenses..." 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pl-10"
              >
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-icons-outlined text-gray-400">search</span>
              </div>
            </div>
            
            <button 
              @click="resetFilters" 
              class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Reset Filters
            </button>
          </div>
        </div>

        <!-- Expenses Table -->
        <div v-if="filteredExpenses.length > 0" class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th v-if="activeTab === 'bill'" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                  <th v-if="activeTab === 'bill'" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="expense in paginatedExpenses" :key="expense.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ expense.description }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex items-center">
                      <span class="material-icons-outlined mr-1 text-gray-400">{{ getCategoryIcon(expense.category) }}</span>
                      {{ getCategoryLabel(expense.category) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(expense.amount) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(expense.bill_date) }}</td>
                  <td v-if="activeTab === 'bill'" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ expense.due_date ? formatDate(expense.due_date) : '-' }}</td>
                  <td v-if="activeTab === 'bill'" class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusColor(expense.status)">
                      {{ expense.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button @click="editExpense(expense)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                    <button @click="deleteExpense(expense.id)" class="text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing <span class="font-medium">{{ paginatedExpenses.length }}</span> of <span class="font-medium">{{ filteredExpenses.length }}</span> expenses
            </div>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm">
              <button
                @click="currentPage = Math.max(1, currentPage - 1)"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
              >
                <span class="material-icons-outlined">chevron_left</span>
              </button>
              <button
                @click="currentPage = Math.min(totalPages, currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
              >
                <span class="material-icons-outlined">chevron_right</span>
              </button>
            </nav>
          </div>
        </div>

        <!-- No Expenses Message -->
        <div v-else class="bg-white rounded-xl shadow-lg p-8 text-center">
          <div class="text-gray-400 mb-4">
            <span class="material-icons-outlined text-5xl">receipt_long</span>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-1">No expenses found</h3>
          <p class="text-gray-500">Try adjusting your filters or add a new expense.</p>
          <button 
            @click="showAddModal = true" 
            class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Add New Expense
          </button>
        </div>
      </div>

      <!-- Calendar View -->
      <div v-else-if="viewMode === 'calendar'" class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
          <button @click="changeMonth(-1)" class="p-2 rounded-full hover:bg-gray-200">
            <span class="material-icons-outlined">chevron_left</span>
          </button>
          <h3 class="text-lg font-medium text-gray-900">
            {{ new Date(calendarYear, calendarMonth).toLocaleString('default', { month: 'long', year: 'numeric' }) }}
          </h3>
          <button @click="changeMonth(1)" class="p-2 rounded-full hover:bg-gray-200">
            <span class="material-icons-outlined">chevron_right</span>
          </button>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-7 gap-2 mb-2">
            <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day" class="text-center text-sm font-medium text-gray-500">
              {{ day }}
            </div>
          </div>
          
          <div class="grid grid-cols-7 gap-2">
            <div 
              v-for="(day, index) in calendarDays" 
              :key="index" 
              class="border rounded-md p-2 min-h-[100px]"
              :class="day.day ? (day.expenses.length > 0 ? 'border-indigo-200 bg-indigo-50' : 'border-gray-200') : 'border-transparent'"
            >
              <div v-if="day.day" class="flex justify-between items-center mb-2">
                <span class="font-medium" :class="day.expenses.length > 0 ? 'text-indigo-700' : 'text-gray-700'">{{ day.day }}</span>
                <span v-if="day.expenses.length > 0" class="text-xs font-medium bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full">
                  {{ formatCurrency(day.total) }}
                </span>
              </div>
              <div v-if="day.day && day.expenses.length > 0" class="space-y-1">
                <div 
                  v-for="expense in day.expenses.slice(0, 3)" 
                  :key="expense.id" 
                  class="text-xs p-1 rounded truncate cursor-pointer hover:bg-indigo-100"
                  @click="editExpense(expense)"
                >
                  {{ expense.description }} - {{ formatCurrency(expense.amount) }}
                </div>
                <div v-if="day.expenses.length > 3" class="text-xs text-indigo-600 font-medium">
                  + {{ day.expenses.length - 3 }} more
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Expense Form Modal -->
    <ExpenseFormModal 
      :show="showAddModal" 
      :expense="selectedExpense" 
      @close="showAddModal = false" 
      @saved="handleExpenseSaved"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import axios from 'axios';
import Chart from 'chart.js/auto';
import ExpenseFormModal from '../../components/expenses/ExpenseFormModal.vue';

export default {
  components: {
    ExpenseFormModal
  },
  setup() {
    const showAddModal = ref(false);
    const expenses = ref([]);
    const currentPage = ref(1);
    const itemsPerPage = ref(10);
    const selectedExpense = ref(null);
    const isLoading = ref(true);
    const viewMode = ref('list'); // 'list' or 'calendar'
    const activeTab = ref('bill'); // 'bill' or 'enhancement'
    const calendarMonth = ref(new Date().getMonth());
    const calendarYear = ref(new Date().getFullYear());
    const calendarDays = ref([]);
    const monthlyChart = ref(null);
    const categoryChart = ref(null);
    const monthlyChartInstance = ref(null);
    const categoryChartInstance = ref(null);
    
    const stats = ref({
      total_expenses_this_month: 0,
      paid_expenses: 0,
      pending_expenses: 0,
      overdue_expenses: 0,
      average_monthly: 0,
      monthly_data: [],
      category_breakdown: [],
      calendar_data: []
    });
    
    const filters = ref({
      category: '',
      status: '',
      fromDate: '',
      toDate: '',
      search: ''
    });

    // Category options
    const categoryOptions = {
      rent: { label: 'Rent', icon: 'home' },
      utilities: { label: 'Utilities', icon: 'bolt' },
      water: { label: 'Water Bill', icon: 'water_drop' },
      equipment: { label: 'Equipment', icon: 'fitness_center' },
      maintenance: { label: 'Maintenance', icon: 'build' },
      salary: { label: 'Salary', icon: 'payments' },
      insurance: { label: 'Insurance', icon: 'shield' },
      marketing: { label: 'Marketing', icon: 'campaign' },
      taxes: { label: 'Taxes', icon: 'receipt_long' },
      supplies: { label: 'Supplies', icon: 'inventory_2' },
      software: { label: 'Software', icon: 'computer' },
      renovation: { label: 'Renovation', icon: 'construction' },
      decoration: { label: 'Decoration', icon: 'palette' },
      furniture: { label: 'Furniture', icon: 'chair' },
      other: { label: 'Other', icon: 'more_horiz' }
    };

    // Load expenses
    const loadExpenses = async () => {
      isLoading.value = true;
      try {
        const response = await axios.get(`/api/expenses?type=${activeTab.value}`);
        expenses.value = response.data.expenses;
        stats.value = response.data.stats;
        
        // Setup calendar days if in calendar view
        if (viewMode.value === 'calendar') {
          setupCalendarDays();
        }
        
        // Update charts
        nextTick(() => {
          updateCharts();
        });
      } catch (error) {
        console.error('Error loading expenses:', error);
      } finally {
        isLoading.value = false;
      }
    };

    // Filter expenses
    const filteredExpenses = computed(() => {
      return expenses.value.filter(expense => {
        // Filter by search term
        if (filters.value.search && !expense.description.toLowerCase().includes(filters.value.search.toLowerCase())) {
          return false;
        }
        
        // Filter by category
        if (filters.value.category && expense.category !== filters.value.category) {
          return false;
        }
        
        // Filter by status (only for bills)
        if (activeTab.value === 'bill' && filters.value.status && expense.status !== filters.value.status) {
          return false;
        }
        
        // Filter by date range
        if (filters.value.fromDate) {
          const fromDate = new Date(filters.value.fromDate);
          const expenseDate = new Date(expense.bill_date);
          if (expenseDate < fromDate) return false;
        }
        
        if (filters.value.toDate) {
          const toDate = new Date(filters.value.toDate);
          const expenseDate = new Date(expense.bill_date);
          if (expenseDate > toDate) return false;
        }
        
        return true;
      });
    });

    // Paginated expenses
    const paginatedExpenses = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      const end = start + itemsPerPage.value;
      return filteredExpenses.value.slice(start, end);
    });

    // Total pages
    const totalPages = computed(() => {
      return Math.ceil(filteredExpenses.value.length / itemsPerPage.value);
    });

    // Setup calendar days
    const setupCalendarDays = () => {
      const daysInMonth = new Date(calendarYear.value, calendarMonth.value + 1, 0).getDate();
      const firstDayOfMonth = new Date(calendarYear.value, calendarMonth.value, 1).getDay();
      
      // Create array of days
      const days = [];
      
      // Add empty days for the start of the month
      for (let i = 0; i < firstDayOfMonth; i++) {
        days.push({ day: null, expenses: [] });
      }
      
      // Add days of the month
      for (let day = 1; day <= daysInMonth; day++) {
        const date = `${calendarYear.value}-${(calendarMonth.value + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        const dayExpenses = stats.value.calendar_data.find(item => item.date === date);
        
        days.push({
          day,
          date,
          expenses: dayExpenses ? dayExpenses.expenses : [],
          total: dayExpenses ? dayExpenses.total : 0
        });
      }
      
      calendarDays.value = days;
    };

    // Update charts
    const updateCharts = () => {
      // Destroy previous chart instances
      if (monthlyChartInstance.value) {
        monthlyChartInstance.value.destroy();
      }
      
      if (categoryChartInstance.value) {
        categoryChartInstance.value.destroy();
      }
      
      // Create monthly chart
      if (monthlyChart.value) {
        const ctx = monthlyChart.value.getContext('2d');
        monthlyChartInstance.value = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: stats.value.monthly_data.map(item => item.month),
            datasets: [{
              label: 'Monthly Expenses',
              data: stats.value.monthly_data.map(item => item.total),
              backgroundColor: 'rgba(79, 70, 229, 0.6)',
              borderColor: 'rgba(79, 70, 229, 1)',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  callback: function(value) {
                    return '$' + value;
                  }
                }
              }
            }
          }
        });
      }
      
      // Create category chart
      if (categoryChart.value && stats.value.category_breakdown.length > 0) {
        const ctx = categoryChart.value.getContext('2d');
        categoryChartInstance.value = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: stats.value.category_breakdown.map(item => item.label),
            datasets: [{
              data: stats.value.category_breakdown.map(item => item.amount),
              backgroundColor: [
                'rgba(79, 70, 229, 0.6)',
                'rgba(59, 130, 246, 0.6)',
                'rgba(16, 185, 129, 0.6)',
                'rgba(245, 158, 11, 0.6)',
                'rgba(239, 68, 68, 0.6)',
                'rgba(139, 92, 246, 0.6)',
                'rgba(236, 72, 153, 0.6)',
                'rgba(75, 85, 99, 0.6)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'right'
              }
            }
          }
        });
      }
    };

    // Change month
    const changeMonth = (increment) => {
      let newMonth = calendarMonth.value + increment;
      let newYear = calendarYear.value;
      
      if (newMonth < 0) {
        newMonth = 11;
        newYear--;
      } else if (newMonth > 11) {
        newMonth = 0;
        newYear++;
      }
      
      calendarMonth.value = newMonth;
      calendarYear.value = newYear;
      
      // Reload expenses for the new month
      loadExpenses();
    };

    // Format currency
    const formatCurrency = (value) => {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
      }).format(value);
    };

    // Format date
    const formatDate = (dateString) => {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      }).format(date);
    };

    // Get status color
    const getStatusColor = (status) => {
      switch (status) {
        case 'paid':
          return 'bg-green-100 text-green-800';
        case 'pending':
          return 'bg-yellow-100 text-yellow-800';
        case 'overdue':
          return 'bg-red-100 text-red-800';
        default:
          return 'bg-gray-100 text-gray-800';
      }
    };

    // Get category icon
    const getCategoryIcon = (category) => {
      return categoryOptions[category]?.icon || 'more_horiz';
    };
    
    // Get category label
    const getCategoryLabel = (category) => {
      return categoryOptions[category]?.label || category;
    };

    // Edit expense
    const editExpense = (expense) => {
      selectedExpense.value = expense;
      showAddModal.value = true;
    };

    // Delete expense
    const deleteExpense = async (id) => {
      if (confirm('Are you sure you want to delete this expense?')) {
        try {
          await axios.delete(`/api/expenses/${id}`);
          loadExpenses();
        } catch (error) {
          console.error('Error deleting expense:', error);
        }
      }
    };

    // Handle expense saved
    const handleExpenseSaved = () => {
      loadExpenses();
    };

    // Change tab
    const changeTab = (tab) => {
      activeTab.value = tab;
      loadExpenses();
    };

    // Reset filters
    const resetFilters = () => {
      filters.value = {
        category: '',
        status: '',
        fromDate: '',
        toDate: '',
        search: ''
      };
    };

    // Watch for view mode changes
    watch(viewMode, (newMode) => {
      if (newMode === 'calendar') {
        setupCalendarDays();
      }
    });

    // Initialize
    onMounted(() => {
      loadExpenses();
    });

    return {
      showAddModal,
      expenses,
      currentPage,
      itemsPerPage,
      selectedExpense,
      isLoading,
      viewMode,
      activeTab,
      calendarMonth,
      calendarYear,
      calendarDays,
      monthlyChart,
      categoryChart,
      stats,
      filters,
      categoryOptions,
      filteredExpenses,
      paginatedExpenses,
      totalPages,
      loadExpenses,
      setupCalendarDays,
      changeMonth,
      formatCurrency,
      formatDate,
      getStatusColor,
      getCategoryIcon,
      getCategoryLabel,
      editExpense,
      deleteExpense,
      handleExpenseSaved,
      changeTab,
      resetFilters
    };
  }
};
</script>
