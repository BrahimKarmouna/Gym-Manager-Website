php artisan migrate<template>
  <div class="orders-page">
    <div class="header mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Order Management</h1>
      <p class="text-gray-500">Manage and process customer orders</p>
    </div>

    <!-- Filters -->
    <div class="filters bg-white rounded-xl shadow-sm p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
          <select
            v-model="filters.status"
            class="w-full p-2 border border-gray-300 rounded-lg"
          >
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
          <select
            v-model="filters.dateRange"
            class="w-full p-2 border border-gray-300 rounded-lg"
          >
            <option value="all">All Time</option>
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="custom">Custom Range</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Order ID, customer name..."
            class="w-full p-2 border border-gray-300 rounded-lg"
          >
        </div>
        <div class="flex items-end">
          <button
            @click="loadOrders"
            class="w-full p-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors"
          >
            Apply Filters
          </button>
        </div>
      </div>
      <div v-if="filters.dateRange === 'custom'" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
          <input
            v-model="filters.startDate"
            type="date"
            class="w-full p-2 border border-gray-300 rounded-lg"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
          <input
            v-model="filters.endDate"
            type="date"
            class="w-full p-2 border border-gray-300 rounded-lg"
          >
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-8">
      <q-spinner color="primary" size="3em" />
    </div>

    <!-- Error Message -->
    <div v-else-if="error" class="bg-red-100 text-red-700 p-4 rounded-xl mb-6">
      {{ error }}
      <button @click="loadOrders" class="underline ml-2">Try again</button>
    </div>

    <!-- Orders Table -->
    <div v-else-if="orders.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Order ID
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Customer
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Date
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Total
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">#{{ order.id }}</div>
              <div class="text-sm text-gray-500">{{ order.payment_id }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ order.user.name }}</div>
              <div class="text-sm text-gray-500">{{ order.user.email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ formatDate(order.created_at) }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClass(order.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ order.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              ${{ formatPrice(order.total) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="viewOrder(order)"
                class="text-primary hover:text-primary-dark mr-3"
              >
                View
              </button>
              <button
                v-if="order.status === 'pending'"
                @click="updateOrderStatus(order.id, 'processing')"
                class="text-yellow-600 hover:text-yellow-800 mr-3"
              >
                Process
              </button>
              <button
                v-if="order.status === 'processing'"
                @click="updateOrderStatus(order.id, 'completed')"
                class="text-green-600 hover:text-green-800 mr-3"
              >
                Complete
              </button>
              <button
                v-if="order.status !== 'cancelled' && order.status !== 'completed'"
                @click="updateOrderStatus(order.id, 'cancelled')"
                class="text-red-600 hover:text-red-800"
              >
                Cancel
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="px-6 py-4 flex items-center justify-between border-t border-gray-200">
        <div class="text-sm text-gray-500">
          Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, totalOrders) }} of {{ totalOrders }} orders
        </div>
        <div class="flex-1 flex justify-end">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          <button
            @click="nextPage"
            :disabled="currentPage * perPage >= totalOrders"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="bg-white rounded-xl shadow-sm p-8 text-center">
      <div class="mx-auto w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center mb-4">
        <i class="material-icons text-4xl text-gray-400">receipt_long</i>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-1">No orders found</h3>
      <p class="text-gray-500 mb-4">There are no orders matching your filters.</p>
      <button
        @click="resetFilters"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
      >
        Reset Filters
      </button>
    </div>

    <!-- Order Details Modal -->
    <q-dialog v-model="showOrderModal">
      <q-card class="w-full max-w-3xl">
        <q-card-section class="flex items-center justify-between">
          <div>
            <div class="text-lg font-medium">Order #{{ selectedOrder?.id }}</div>
            <div class="text-sm text-gray-500">{{ formatDate(selectedOrder?.created_at) }}</div>
          </div>
          <q-btn flat round icon="close" v-close-popup />
        </q-card-section>

        <q-separator />

        <q-card-section v-if="selectedOrder">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Customer Info -->
            <div>
              <h3 class="text-base font-medium mb-2">Customer Information</h3>
              <div class="bg-gray-50 p-3 rounded">
                <div class="text-sm mb-1"><span class="font-medium">Name:</span> {{ selectedOrder.user.name }}</div>
                <div class="text-sm mb-1"><span class="font-medium">Email:</span> {{ selectedOrder.user.email }}</div>
                <div class="text-sm"><span class="font-medium">Customer since:</span> {{ formatDate(selectedOrder.user.created_at) }}</div>
              </div>
            </div>

            <!-- Shipping Info -->
            <div>
              <h3 class="text-base font-medium mb-2">Shipping Address</h3>
              <div class="bg-gray-50 p-3 rounded">
                <div class="text-sm mb-1">{{ selectedOrder.shipping?.name }}</div>
                <div class="text-sm mb-1">{{ selectedOrder.shipping?.address?.line1 }}</div>
                <div class="text-sm mb-1">
                  {{ selectedOrder.shipping?.address?.city }}, {{ selectedOrder.shipping?.address?.state }} {{ selectedOrder.shipping?.address?.postal_code }}
                </div>
                <div class="text-sm">{{ selectedOrder.shipping?.address?.country }}</div>
              </div>
            </div>
          </div>

          <!-- Order Items -->
          <div class="mt-6">
            <h3 class="text-base font-medium mb-2">Order Items</h3>
            <div class="bg-gray-50 rounded overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                  <tr>
                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Product
                    </th>
                    <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Price
                    </th>
                    <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Quantity
                    </th>
                    <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Total
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="item in selectedOrder.items" :key="item.id">
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                      {{ item.name }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                      ${{ formatPrice(item.price) }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                      {{ item.quantity }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                      ${{ formatPrice(item.price * item.quantity) }}
                    </td>
                  </tr>
                </tbody>
                <tfoot class="bg-gray-50">
                  <tr>
                    <td colspan="3" class="px-4 py-2 text-sm text-gray-900 text-right font-medium">
                      Subtotal:
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-900 text-right">
                      ${{ formatPrice(selectedOrder.subtotal) }}
                    </td>
                  </tr>
                  <tr v-if="selectedOrder.discount > 0">
                    <td colspan="3" class="px-4 py-2 text-sm text-green-600 text-right font-medium">
                      Discount:
                    </td>
                    <td class="px-4 py-2 text-sm text-green-600 text-right">
                      -${{ formatPrice(selectedOrder.discount) }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" class="px-4 py-2 text-sm text-gray-900 text-right font-medium">
                      Tax:
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-900 text-right">
                      ${{ formatPrice(selectedOrder.tax) }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" class="px-4 py-2 text-base text-gray-900 text-right font-bold">
                      Total:
                    </td>
                    <td class="px-4 py-2 text-base text-gray-900 text-right font-bold">
                      ${{ formatPrice(selectedOrder.total) }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <!-- Payment Info -->
          <div class="mt-6">
            <h3 class="text-base font-medium mb-2">Payment Information</h3>
            <div class="bg-gray-50 p-3 rounded">
              <div class="text-sm mb-1"><span class="font-medium">Payment ID:</span> {{ selectedOrder.payment_id }}</div>
              <div class="text-sm mb-1"><span class="font-medium">Payment Method:</span> {{ selectedOrder.payment_method || 'Credit Card' }}</div>
              <div class="text-sm"><span class="font-medium">Status:</span>
                <span :class="getStatusClass(selectedOrder.status)">{{ selectedOrder.status }}</span>
              </div>
            </div>
          </div>
        </q-card-section>

        <q-separator />

        <q-card-actions align="right">
          <q-btn flat label="Close" color="primary" v-close-popup />
          <q-btn
            v-if="selectedOrder?.status === 'pending'"
            flat
            label="Mark as Processing"
            color="warning"
            @click="updateOrderStatus(selectedOrder.id, 'processing'); showOrderModal = false"
          />
          <q-btn
            v-if="selectedOrder?.status === 'processing'"
            flat
            label="Mark as Completed"
            color="positive"
            @click="updateOrderStatus(selectedOrder.id, 'completed'); showOrderModal = false"
          />
          <q-btn
            v-if="selectedOrder?.status !== 'cancelled' && selectedOrder?.status !== 'completed'"
            flat
            label="Cancel Order"
            color="negative"
            @click="updateOrderStatus(selectedOrder.id, 'cancelled'); showOrderModal = false"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useQuasar } from 'quasar';
import { useAuthStore } from '../../../stores/auth';
import axios from 'axios';

const $q = useQuasar();
const authStore = useAuthStore();

// Check if user is admin
if (!authStore.isAdmin) {
  // Redirect non-admin users
  router.push({ name: 'dashboard.index' });
}

// State
const loading = ref(false);
const error = ref(null);
const orders = ref([]);
const totalOrders = ref(0);
const currentPage = ref(1);
const perPage = ref(10);
const selectedOrder = ref(null);
const showOrderModal = ref(false);

// Filters
const filters = reactive({
  status: '',
  dateRange: 'all',
  startDate: '',
  endDate: '',
  search: ''
});

// Load orders from API
const loadOrders = async () => {
  loading.value = true;
  error.value = null;

  try {
    const params = {
      page: currentPage.value,
      per_page: perPage.value,
      status: filters.status,
      search: filters.search
    };

    // Add date filters
    if (filters.dateRange === 'custom' && filters.startDate && filters.endDate) {
      params.start_date = filters.startDate;
      params.end_date = filters.endDate;
    } else if (filters.dateRange !== 'all') {
      params.date_range = filters.dateRange;
    }

    const response = await axios.get('/api/admin/orders', { params });

    orders.value = response.data.data;
    totalOrders.value = response.data.meta.total;

  } catch (err) {
    console.error('Error loading orders:', err);
    error.value = 'Failed to load orders. Please try again.';
  } finally {
    loading.value = false;
  }
};

// Pagination methods
const nextPage = () => {
  if (currentPage.value * perPage.value < totalOrders.value) {
    currentPage.value++;
    loadOrders();
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    loadOrders();
  }
};

// Reset filters
const resetFilters = () => {
  filters.status = '';
  filters.dateRange = 'all';
  filters.startDate = '';
  filters.endDate = '';
  filters.search = '';
  currentPage.value = 1;
  loadOrders();
};

// View order details
const viewOrder = (order) => {
  selectedOrder.value = order;
  showOrderModal.value = true;
};

// Update order status
const updateOrderStatus = async (orderId, status) => {
  try {
    await axios.patch(`/api/admin/orders/${orderId}`, { status });

    // Update order in the list
    const order = orders.value.find(o => o.id === orderId);
    if (order) {
      order.status = status;
    }

    // Update selected order if open
    if (selectedOrder.value && selectedOrder.value.id === orderId) {
      selectedOrder.value.status = status;
    }

    $q.notify({
      message: `Order status updated to ${status}`,
      color: 'positive',
      position: 'top',
      timeout: 2000
    });

  } catch (error) {
    console.error('Error updating order status:', error);
    $q.notify({
      message: 'Failed to update order status',
      color: 'negative',
      position: 'top',
      timeout: 2000
    });
  }
};

// Format price to 2 decimal places
const formatPrice = (price) => {
  return (Math.round(price * 100) / 100).toFixed(2);
};

// Format date
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

// Get appropriate CSS class for status
const getStatusClass = (status) => {
  const classes = {
    'pending': 'bg-blue-100 text-blue-800',
    'processing': 'bg-yellow-100 text-yellow-800',
    'completed': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  };

  return classes[status] || 'bg-gray-100 text-gray-800';
};

// Load orders on mount
onMounted(() => {
  loadOrders();
});
</script>

<style scoped>
.orders-page {
  padding: 1.5rem;
}
</style>
