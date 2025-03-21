<script>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import BillFormModal from '../../components/bills/BillFormModal.vue';

export default {
  components: {
    BillFormModal
  },
  setup() {
    const showAddModal = ref(false);
    const bills = ref([]);
    const currentPage = ref(1);
    const itemsPerPage = ref(10);
    const selectedBill = ref(null);
    const isLoading = ref(true);
    const viewMode = ref('list'); // 'list' or 'calendar'
    const calendarMonth = ref(new Date().getMonth());
    const calendarYear = ref(new Date().getFullYear());
    const calendarDays = ref([]);
    const monthlyStats = ref({
      total_bills_this_month: 0,
      paid_bills: 0,
      pending_bills: 0,
      average_monthly: 0,
      monthly_data: []
    });
    
    const filters = ref({
      type: '',
      status: '',
      fromDate: '',
      toDate: '',
      search: ''
    });

    // Computed properties
    const filteredBills = computed(() => {
      return bills.value.filter(bill => {
        // Filter by category
        if (filters.value.type && bill.category !== filters.value.type) return false;
        
        // Filter by status
        if (filters.value.status && bill.status !== filters.value.status) return false;
        
        // Filter by date range
        if (filters.value.fromDate) {
          const fromDate = new Date(filters.value.fromDate);
          const billDate = new Date(bill.bill_date);
          if (billDate < fromDate) return false;
        }
        
        if (filters.value.toDate) {
          const toDate = new Date(filters.value.toDate);
          const billDate = new Date(bill.bill_date);
          if (billDate > toDate) return false;
        }
        
        // Filter by search term
        if (filters.value.search) {
          const searchTerm = filters.value.search.toLowerCase();
          return bill.description?.toLowerCase().includes(searchTerm) || 
                 bill.category?.toLowerCase().includes(searchTerm);
        }
        
        return true;
      });
    });

    const paginatedBills = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      const end = start + itemsPerPage.value;
      return filteredBills.value.slice(start, end);
    });

    const totalPages = computed(() => {
      return Math.ceil(filteredBills.value.length / itemsPerPage.value) || 1;
    });

    // Methods
    const fetchBills = async () => {
      isLoading.value = true;
      try {
        const response = await axios.get('/api/bills');
        bills.value = response.data.bills;
        monthlyStats.value = response.data.stats;
        isLoading.value = false;
      } catch (error) {
        console.error('Error fetching bills:', error);
        isLoading.value = false;
      }
    };

    const fetchStats = async () => {
      try {
        const response = await axios.get('/api/bills/stats');
        monthlyStats.value = response.data;
      } catch (error) {
        console.error('Error fetching stats:', error);
      }
    };

    const resetFilters = () => {
      filters.value = {
        type: '',
        status: '',
        fromDate: '',
        toDate: '',
        search: ''
      };
      currentPage.value = 1;
    };

    const applyFilters = () => {
      currentPage.value = 1;
    };

    const isOverdue = (dueDate) => {
      return new Date(dueDate) < new Date();
    };

    const getBillCategoryIcon = (category) => {
      const icons = {
        rent: 'text-purple-500 fas fa-home',
        utilities: 'text-yellow-500 fas fa-bolt',
        water: 'text-blue-500 fas fa-water',
        equipment: 'text-green-500 fas fa-dumbbell',
        maintenance: 'text-orange-500 fas fa-tools',
        salary: 'text-indigo-500 fas fa-user-tie',
        insurance: 'text-red-500 fas fa-shield-alt',
        marketing: 'text-pink-500 fas fa-ad',
        taxes: 'text-gray-500 fas fa-file-invoice-dollar',
        supplies: 'text-teal-500 fas fa-box',
        software: 'text-cyan-500 fas fa-laptop-code',
        other: 'text-gray-500 fas fa-file-invoice-dollar'
      };
      return icons[category] || icons.other;
    };

    const capitalizeFirstLetter = (string) => {
      if (!string) return '';
      return string.charAt(0).toUpperCase() + string.slice(1);
    };

    const formatDate = (dateString) => {
      if (!dateString) return '';
      return new Date(dateString).toLocaleDateString();
    };

    const getStatusBadgeClass = (status) => {
      const classes = {
        paid: 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800',
        pending: 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800',
        overdue: 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800'
      };
      return classes[status] || classes.pending;
    };

    const viewBill = (bill) => {
      selectedBill.value = bill;
      showAddModal.value = true;
    };

    const editBill = (bill) => {
      selectedBill.value = bill;
      showAddModal.value = true;
    };

    const deleteBill = async (bill) => {
      if (confirm('Are you sure you want to delete this bill?')) {
        try {
          await axios.delete(`/api/bills/${bill.id}`);
          await fetchBills();
          await fetchStats();
        } catch (error) {
          console.error('Error deleting bill:', error);
          alert('There was an error deleting the bill. Please try again.');
        }
      }
    };

    const handleBillSaved = async () => {
      await fetchBills();
      await fetchStats();
      selectedBill.value = null;
    };

    const switchView = (mode) => {
      viewMode.value = mode;
      if (mode === 'calendar') {
        generateCalendarDays();
      }
    };

    const generateCalendarDays = () => {
      const firstDay = new Date(calendarYear.value, calendarMonth.value, 1);
      const lastDay = new Date(calendarYear.value, calendarMonth.value + 1, 0);
      const daysInMonth = lastDay.getDate();
      
      // Get the day of the week for the first day (0 = Sunday, 1 = Monday, etc.)
      const firstDayOfWeek = firstDay.getDay();
      
      // Create array for all days in the month
      const days = [];
      
      // Add empty cells for days before the first day of the month
      for (let i = 0; i < firstDayOfWeek; i++) {
        days.push({ day: null, bills: [] });
      }
      
      // Add days of the month
      for (let i = 1; i <= daysInMonth; i++) {
        const date = new Date(calendarYear.value, calendarMonth.value, i);
        const dayBills = bills.value.filter(bill => {
          const billDate = new Date(bill.bill_date);
          return billDate.getDate() === i && 
                 billDate.getMonth() === calendarMonth.value && 
                 billDate.getFullYear() === calendarYear.value;
        });
        
        days.push({
          day: i,
          date: date,
          bills: dayBills
        });
      }
      
      calendarDays.value = days;
    };

    const previousMonth = () => {
      if (calendarMonth.value === 0) {
        calendarMonth.value = 11;
        calendarYear.value--;
      } else {
        calendarMonth.value--;
      }
      generateCalendarDays();
    };

    const nextMonth = () => {
      if (calendarMonth.value === 11) {
        calendarMonth.value = 0;
        calendarYear.value++;
      } else {
        calendarMonth.value++;
      }
      generateCalendarDays();
    };

    const getMonthName = (month) => {
      const months = [
        'January', 'February', 'March', 'April', 'May', 'June', 
        'July', 'August', 'September', 'October', 'November', 'December'
      ];
      return months[month];
    };

    // Lifecycle hooks
    onMounted(async () => {
      await fetchBills();
      await fetchStats();
    });

    return {
      showAddModal,
      bills,
      currentPage,
      itemsPerPage,
      filters,
      selectedBill,
      isLoading,
      viewMode,
      calendarMonth,
      calendarYear,
      calendarDays,
      monthlyStats,
      filteredBills,
      paginatedBills,
      totalPages,
      resetFilters,
      applyFilters,
      isOverdue,
      getBillCategoryIcon,
      capitalizeFirstLetter,
      formatDate,
      getStatusBadgeClass,
      viewBill,
      editBill,
      deleteBill,
      handleBillSaved,
      switchView,
      previousMonth,
      nextMonth,
      getMonthName
    };
  }
}
</script>

<template>
  <div class="bg-gray-50 min-h-screen p-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Bills Management</h1>
      <div class="flex space-x-3">
        <div class="bg-white rounded-lg shadow-sm p-1 flex">
          <button @click="switchView('list')" 
            :class="['px-3 py-1 rounded-md transition', viewMode === 'list' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100']">
            <i class="fas fa-list-ul mr-1"></i> List
          </button>
          <button @click="switchView('calendar')" 
            :class="['px-3 py-1 rounded-md transition', viewMode === 'calendar' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100']">
            <i class="fas fa-calendar-alt mr-1"></i> Calendar
          </button>
        </div>
        <button @click="showAddModal = true; selectedBill = null" 
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transform transition hover:scale-105 shadow-lg">
          <i class="fas fa-plus mr-2"></i>
          <span>Add New Bill</span>
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-xl p-6 text-white transform transition duration-500 hover:scale-105 relative">
        <h5 class="font-medium text-blue-100">Total Bills This Month</h5>
        <h2 class="text-3xl font-bold mt-2">${{ monthlyStats.total_bills_this_month?.toFixed(2) || '0.00' }}</h2>
        <div class="absolute bottom-4 right-4 opacity-30">
          <i class="fas fa-file-invoice-dollar text-5xl"></i>
        </div>
      </div>
      
      <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow-xl p-6 text-white transform transition duration-500 hover:scale-105 relative">
        <h5 class="font-medium text-emerald-100">Paid Bills</h5>
        <h2 class="text-3xl font-bold mt-2">${{ monthlyStats.paid_bills?.toFixed(2) || '0.00' }}</h2>
        <div class="absolute bottom-4 right-4 opacity-30">
          <i class="fas fa-check-circle text-5xl"></i>
        </div>
      </div>
      
      <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-xl p-6 text-white transform transition duration-500 hover:scale-105 relative">
        <h5 class="font-medium text-red-100">Pending Bills</h5>
        <h2 class="text-3xl font-bold mt-2">${{ monthlyStats.pending_bills?.toFixed(2) || '0.00' }}</h2>
        <div class="absolute bottom-4 right-4 opacity-30">
          <i class="fas fa-clock text-5xl"></i>
        </div>
      </div>
      
      <div class="bg-gradient-to-r from-violet-500 to-violet-600 rounded-xl shadow-xl p-6 text-white transform transition duration-500 hover:scale-105 relative">
        <h5 class="font-medium text-violet-100">Average Monthly</h5>
        <h2 class="text-3xl font-bold mt-2">${{ monthlyStats.average_monthly?.toFixed(2) || '0.00' }}</h2>
        <div class="absolute bottom-4 right-4 opacity-30">
          <i class="fas fa-chart-line text-5xl"></i>
        </div>
      </div>
    </div>

    <!-- Loading Indicator -->
    <div v-if="isLoading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
    </div>

    <template v-else>
      <!-- List View -->
      <div v-if="viewMode === 'list'">
        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-lg mb-8 p-6 border border-gray-100">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
            <div>
              <label class="text-sm font-medium text-gray-600 block mb-2">Bill Category</label>
              <select v-model="filters.type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">All Categories</option>
                <option value="rent">Rent</option>
                <option value="utilities">Utilities</option>
                <option value="water">Water</option>
                <option value="equipment">Equipment</option>
                <option value="maintenance">Maintenance</option>
                <option value="salary">Salary</option>
                <option value="insurance">Insurance</option>
                <option value="marketing">Marketing</option>
                <option value="taxes">Taxes</option>
                <option value="supplies">Supplies</option>
                <option value="software">Software</option>
                <option value="other">Other</option>
              </select>
            </div>
            
            <div>
              <label class="text-sm font-medium text-gray-600 block mb-2">Status</label>
              <select v-model="filters.status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">All Status</option>
                <option value="paid">Paid</option>
                <option value="pending">Pending</option>
                <option value="overdue">Overdue</option>
              </select>
            </div>
            
            <div>
              <label class="text-sm font-medium text-gray-600 block mb-2">From Date</label>
              <input type="date" v-model="filters.fromDate" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            
            <div>
              <label class="text-sm font-medium text-gray-600 block mb-2">To Date</label>
              <input type="date" v-model="filters.toDate" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
          </div>
          
          <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 mt-4">
            <input type="text" placeholder="Search bills..." v-model="filters.search" 
              class="flex-grow rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div class="flex space-x-3">
              <button @click="resetFilters" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                <i class="fas fa-undo mr-1"></i> Reset
              </button>
              <button @click="applyFilters" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                <i class="fas fa-filter mr-1"></i> Apply Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Bills Table -->
        <div v-if="filteredBills.length > 0" class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill Date</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="bill in paginatedBills" :key="bill.id" 
                    :class="{'bg-red-50': bill.status === 'pending' && isOverdue(bill.due_date), 'hover:bg-gray-50': true}">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <i :class="getBillCategoryIcon(bill.category)"></i>
                      <span class="text-sm font-medium text-gray-900">{{ capitalizeFirstLetter(bill.category) }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ bill.description }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ parseFloat(bill.amount).toFixed(2) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(bill.bill_date) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(bill.due_date) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(bill.status)">{{ capitalizeFirstLetter(bill.status) }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <button @click="viewBill(bill)" class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 p-2 rounded-full transition">
                      <i class="fas fa-eye text-xs"></i>
                    </button>
                    <button @click="editBill(bill)" class="text-amber-600 hover:text-amber-900 bg-amber-100 hover:bg-amber-200 p-2 rounded-full transition">
                      <i class="fas fa-edit text-xs"></i>
                    </button>
                    <button @click="deleteBill(bill)" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 p-2 rounded-full transition">
                      <i class="fas fa-trash text-xs"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing <span class="font-medium">{{ paginatedBills.length }}</span> of <span class="font-medium">{{ filteredBills.length }}</span> bills
            </div>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm">
              <button @click.prevent="currentPage--" :disabled="currentPage === 1" 
                :class="['relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0', {'opacity-50 cursor-not-allowed': currentPage === 1}]">
                <i class="fas fa-chevron-left text-xs"></i>
              </button>
              <button v-for="page in totalPages" :key="page" @click.prevent="currentPage = page"
                :class="['relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0', 
                        page === currentPage ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'text-gray-900']">
                {{ page }}
              </button>
              <button @click.prevent="currentPage++" :disabled="currentPage === totalPages"
                :class="['relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0', {'opacity-50 cursor-not-allowed': currentPage === totalPages}]">
                <i class="fas fa-chevron-right text-xs"></i>
              </button>
            </nav>
          </div>
        </div>

        <!-- No Bills Message -->
        <div v-else class="bg-white rounded-xl shadow-lg p-8 text-center">
          <div class="text-gray-400 mb-4">
            <i class="fas fa-file-invoice-dollar text-6xl"></i>
          </div>
          <h3 class="text-xl font-medium text-gray-700 mb-2">No Bills Found</h3>
          <p class="text-gray-500 mb-4">There are no bills matching your filters. Try adjusting your search criteria or add a new bill.</p>
          <button @click="showAddModal = true; selectedBill = null" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-2"></i> Add New Bill
          </button>
        </div>
      </div>

      <!-- Calendar View -->
      <div v-else-if="viewMode === 'calendar'" class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
          <button @click="previousMonth" class="p-2 rounded-full hover:bg-gray-200 transition">
            <i class="fas fa-chevron-left"></i>
          </button>
          <h2 class="text-xl font-semibold text-gray-800">{{ getMonthName(calendarMonth) }} {{ calendarYear }}</h2>
          <button @click="nextMonth" class="p-2 rounded-full hover:bg-gray-200 transition">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
        
        <div class="grid grid-cols-7 gap-px bg-gray-200">
          <!-- Day headers -->
          <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day" 
               class="bg-gray-100 text-center py-2 font-medium text-gray-700">
            {{ day }}
          </div>
          
          <!-- Calendar days -->
          <div v-for="(dayInfo, index) in calendarDays" :key="index" 
               class="bg-white min-h-[120px] p-2 relative">
            <div v-if="dayInfo.day" class="text-sm font-medium mb-2 sticky top-0 bg-white">
              {{ dayInfo.day }}
            </div>
            
            <div v-for="bill in dayInfo.bills" :key="bill.id" 
                 :class="['text-xs p-1 mb-1 rounded truncate cursor-pointer', 
                         bill.status === 'paid' ? 'bg-green-100 text-green-800' : 
                         bill.status === 'overdue' ? 'bg-red-100 text-red-800' : 
                         'bg-yellow-100 text-yellow-800']"
                 @click="viewBill(bill)">
              {{ bill.description }} - ${{ parseFloat(bill.amount).toFixed(2) }}
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Bill Form Modal -->
    <BillFormModal 
      :show="showAddModal" 
      :bill="selectedBill" 
      @close="showAddModal = false" 
      @saved="handleBillSaved" 
    />
  </div>
</template>
