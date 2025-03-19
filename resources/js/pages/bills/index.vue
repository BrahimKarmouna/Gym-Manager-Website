<script>
export default {
  data() {
    return {
      showAddModal: false,
      bills: [],
      currentPage: 1,
      itemsPerPage: 10,
      filters: {
        type: '',
        status: '',
        fromDate: '',
        toDate: '',
        search: ''
      }
    };
  },
  computed: {
    totalMonthly() {
      const currentDate = new Date();
      const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
      const monthlyBills = this.bills.filter(bill => new Date(bill.billingPeriodStart) >= firstDayOfMonth);
      return monthlyBills.reduce((sum, bill) => sum + bill.amount, 0);
    },
    totalPaid() {
      return this.bills.filter(bill => bill.status === 'paid')
        .reduce((sum, bill) => sum + bill.amount, 0);
    },
    totalPending() {
      return this.bills.filter(bill => bill.status === 'pending')
        .reduce((sum, bill) => sum + bill.amount, 0);
    },
    averageMonthly() {
      // Get unique months from all bills
      const months = [...new Set(this.bills.map(bill => {
        const date = new Date(bill.billingPeriodStart);
        return `${date.getFullYear()}-${date.getMonth()}`;
      }))];
      
      return months.length ? this.bills.reduce((sum, bill) => sum + bill.amount, 0) / months.length : 0;
    },
    filteredBills() {
      return this.bills.filter(bill => {
        // Filter by type
        if (this.filters.type && bill.type !== this.filters.type) return false;
        
        // Filter by status
        if (this.filters.status && bill.status !== this.filters.status) return false;
        
        // Filter by date range
        if (this.filters.fromDate) {
          const fromDate = new Date(this.filters.fromDate);
          const billDate = new Date(bill.billingPeriodStart);
          if (billDate < fromDate) return false;
        }
        
        if (this.filters.toDate) {
          const toDate = new Date(this.filters.toDate);
          const billDate = new Date(bill.billingPeriodEnd);
          if (billDate > toDate) return false;
        }
        
        // Filter by search term
        if (this.filters.search) {
          const searchTerm = this.filters.search.toLowerCase();
          return bill.description.toLowerCase().includes(searchTerm) || 
                 bill.type.toLowerCase().includes(searchTerm);
        }
        
        return true;
      });
    },
    paginatedBills() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredBills.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredBills.length / this.itemsPerPage) || 1;
    }
  },
  methods: {
    resetFilters() {
      this.filters = {
        type: '',
        status: '',
        fromDate: '',
        toDate: '',
        search: ''
      };
      this.currentPage = 1;
    },
    applyFilters() {
      this.currentPage = 1;
      // Any additional logic for applying filters
    },
    isOverdue(dueDate) {
      return new Date(dueDate) < new Date();
    },
    getBillTypeIcon(type) {
      const icons = {
        electricity: 'text-yellow-500 fas fa-bolt',
        water: 'text-blue-500 fas fa-water',
        internet: 'text-green-500 fas fa-wifi',
        maintenance: 'text-orange-500 fas fa-tools',
        other: 'text-gray-500 fas fa-file-invoice-dollar'
      };
      return icons[type] || icons.other;
    },
    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString();
    },
    getStatusBadgeClass(status) {
      return status === 'paid' 
        ? 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800' 
        : 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800';
    },
    viewBill(bill) {
      // Implement view bill functionality
      console.log('Viewing bill:', bill);
    },
    editBill(bill) {
      // Implement edit bill functionality
      console.log('Editing bill:', bill);
    },
    deleteBill(bill) {
      // Implement delete bill functionality
      console.log('Deleting bill:', bill);
    }
  },
  created() {
    // Sample data - replace with actual API call
    this.bills = [
      {
        id: 1,
        type: 'electricity',
        description: 'Electricity Bill - January',
        amount: 150.75,
        billingPeriodStart: '2023-01-01',
        billingPeriodEnd: '2023-01-31',
        dueDate: '2023-02-15',
        status: 'paid'
      },
      {
        id: 2,
        type: 'water',
        description: 'Water Bill - January',
        amount: 75.50,
        billingPeriodStart: '2023-01-01',
        billingPeriodEnd: '2023-01-31',
        dueDate: '2023-02-20',
        status: 'pending'
      },
      // Add more sample bills as needed
    ];
  }
}
</script>

<template>
  <div class="bg-gray-50 min-h-screen p-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Bills Management</h1>
      <button @click="showAddModal = true" 
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transform transition hover:scale-105 shadow-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        <span>Add New Bill</span>
      </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-xl p-6 text-white transform transition duration-500 hover:scale-105">
        <h5 class="font-medium text-blue-100">Total Bills This Month</h5>
        <h2 class="text-3xl font-bold mt-2">${{ totalMonthly.toFixed(2) }}</h2>
        <div class="absolute bottom-4 right-4 opacity-30">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
        </div>
      </div>
      
      <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow-xl p-6 text-white transform transition duration-500 hover:scale-105">
        <h5 class="font-medium text-emerald-100">Paid Bills</h5>
        <h2 class="text-3xl font-bold mt-2">${{ totalPaid.toFixed(2) }}</h2>
        <div class="absolute bottom-4 right-4 opacity-30">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
      
      <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-xl p-6 text-white transform transition duration-500 hover:scale-105">
        <h5 class="font-medium text-red-100">Pending Bills</h5>
        <h2 class="text-3xl font-bold mt-2">${{ totalPending.toFixed(2) }}</h2>
        <div class="absolute bottom-4 right-4 opacity-30">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
      
      <div class="bg-gradient-to-r from-violet-500 to-violet-600 rounded-xl shadow-xl p-6 text-white transform transition duration-500 hover:scale-105">
        <h5 class="font-medium text-violet-100">Average Monthly</h5>
        <h2 class="text-3xl font-bold mt-2">${{ averageMonthly.toFixed(2) }}</h2>
        <div class="absolute bottom-4 right-4 opacity-30">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg mb-8 p-6 border border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
        <div>
          <label class="text-sm font-medium text-gray-600 block mb-2">Bill Type</label>
          <select v-model="filters.type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="">All Types</option>
            <option value="electricity">Electricity</option>
            <option value="water">Water</option>
            <option value="internet">Internet</option>
            <option value="maintenance">Maintenance</option>
            <option value="other">Other</option>
          </select>
        </div>
        
        <div>
          <label class="text-sm font-medium text-gray-600 block mb-2">Status</label>
          <select v-model="filters.status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="">All Status</option>
            <option value="paid">Paid</option>
            <option value="pending">Pending</option>
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
          <button @click="resetFilters" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">Reset</button>
          <button @click="applyFilters" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Apply Filters</button>
        </div>
      </div>
    </div>

    <!-- Bills Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill Type</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Billing Period</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="bill in paginatedBills" :key="bill.id" 
                :class="{'bg-red-50': bill.status === 'pending' && isOverdue(bill.dueDate), 'hover:bg-gray-50': true}">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                  <span :class="getBillTypeIcon(bill.type)"></span>
                  <span class="text-sm font-medium text-gray-900">{{ capitalizeFirstLetter(bill.type) }}</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ bill.description }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ bill.amount.toFixed(2) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(bill.billingPeriodStart) }} to {{ formatDate(bill.billingPeriodEnd) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(bill.dueDate) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusBadgeClass(bill.status)">{{ capitalizeFirstLetter(bill.status) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                <button @click="viewBill(bill)" class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 p-2 rounded-full transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
                <button @click="editBill(bill)" class="text-amber-600 hover:text-amber-900 bg-amber-100 hover:bg-amber-200 p-2 rounded-full transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                </button>
                <button @click="deleteBill(bill)" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 p-2 rounded-full transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
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
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
            </svg>
          </button>
          <button v-for="page in totalPages" :key="page" @click.prevent="currentPage = page"
            :class="['relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0', 
                    page === currentPage ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'text-gray-900']">
            {{ page }}
          </button>
          <button @click.prevent="currentPage++" :disabled="currentPage === totalPages"
            :class="['relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0', {'opacity-50 cursor-not-allowed': currentPage === totalPages}]">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
            </svg>
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>
