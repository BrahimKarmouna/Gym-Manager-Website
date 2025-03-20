<template>
  <div class="user-orders-page">
    <div class="header mb-6">
      <h1 class="text-2xl font-bold text-gray-800">My Orders</h1>
      <p class="text-gray-500">View and track your order history</p>
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

    <!-- Orders List -->
    <div v-else-if="orders.length > 0" class="space-y-4">
      <div 
        v-for="order in orders" 
        :key="order.id" 
        class="bg-white rounded-xl shadow-sm overflow-hidden"
      >
        <div class="p-4 md:p-6 border-b">
          <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
              <span class="text-gray-500 text-sm">Order #</span>
              <h3 class="font-medium">{{ order.id }}</h3>
            </div>
            <div>
              <span class="text-gray-500 text-sm">Date</span>
              <p>{{ formatDate(order.created_at) }}</p>
            </div>
            <div>
              <span class="text-gray-500 text-sm">Total</span>
              <p class="font-bold">${{ formatPrice(order.total) }}</p>
            </div>
            <div>
              <span class="text-gray-500 text-sm">Status</span>
              <p>
                <span :class="getStatusClass(order.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ order.status }}
                </span>
              </p>
            </div>
            <div class="w-full md:w-auto mt-2 md:mt-0">
              <q-btn 
                flat 
                color="primary" 
                label="View Details" 
                @click="viewOrder(order)"
                size="sm"
              />
            </div>
          </div>
        </div>
        <div class="p-4 bg-gray-50 border-b">
          <div class="text-sm font-medium text-gray-500 mb-2">Items ({{ order.items.length }})</div>
          <div class="flex flex-wrap gap-4">
            <div 
              v-for="item in order.items" 
              :key="item.id" 
              class="flex items-center gap-3"
            >
              <div class="w-10 h-10 bg-gray-200 rounded-md overflow-hidden flex-shrink-0">
                <img :src="item.image || '/images/products/placeholder.png'" :alt="item.name" class="w-full h-full object-cover">
              </div>
              <div>
                <div class="text-sm font-medium">{{ item.name }}</div>
                <div class="text-xs text-gray-500">Qty: {{ item.quantity }} × ${{ formatPrice(item.price) }}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="p-4 flex flex-wrap justify-between items-center gap-4">
          <div>
            <span 
              v-if="order.status === 'completed'" 
              class="text-green-600 text-sm flex items-center gap-1"
            >
              <i class="material-icons text-sm">check_circle</i>
              Completed on {{ formatDate(order.completed_at || order.updated_at) }}
            </span>
            <span 
              v-else-if="order.status === 'processing'" 
              class="text-yellow-600 text-sm flex items-center gap-1"
            >
              <i class="material-icons text-sm">pending</i>
              Processing
            </span>
            <span 
              v-else-if="order.status === 'cancelled'" 
              class="text-red-600 text-sm flex items-center gap-1"
            >
              <i class="material-icons text-sm">cancel</i>
              Cancelled
            </span>
            <span 
              v-else 
              class="text-blue-600 text-sm flex items-center gap-1"
            >
              <i class="material-icons text-sm">schedule</i>
              Pending
            </span>
          </div>
          <div v-if="order.tracking_number" class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Track:</span>
            <span class="text-sm font-medium">{{ order.tracking_number }}</span>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-500">
          Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, totalOrders) }} of {{ totalOrders }} orders
        </div>
        <div class="flex gap-2">
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
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="bg-white rounded-xl shadow-sm p-8 text-center">
      <div class="mx-auto w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center mb-4">
        <i class="material-icons text-4xl text-gray-400">shopping_bag</i>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-1">No orders yet</h3>
      <p class="text-gray-500 mb-4">Looks like you haven't placed any orders yet.</p>
      <router-link 
        :to="{ name: 'products' }" 
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none"
      >
        Start Shopping
      </router-link>
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
          <!-- Order Status -->
          <div class="mb-6">
            <div class="relative">
              <div class="flex items-center justify-between w-full mb-2">
                <div class="text-center">
                  <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center" 
                       :class="getStepClass(1, selectedOrder.status)">
                    <i class="material-icons text-sm">check</i>
                  </div>
                  <div class="text-xs font-medium mt-1">Ordered</div>
                </div>
                <div class="text-center">
                  <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center"
                       :class="getStepClass(2, selectedOrder.status)">
                    <i class="material-icons text-sm">inventory</i>
                  </div>
                  <div class="text-xs font-medium mt-1">Processing</div>
                </div>
                <div class="text-center">
                  <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center"
                       :class="getStepClass(3, selectedOrder.status)">
                    <i class="material-icons text-sm">local_shipping</i>
                  </div>
                  <div class="text-xs font-medium mt-1">Shipped</div>
                </div>
                <div class="text-center">
                  <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center"
                       :class="getStepClass(4, selectedOrder.status)">
                    <i class="material-icons text-sm">done_all</i>
                  </div>
                  <div class="text-xs font-medium mt-1">Delivered</div>
                </div>
              </div>
              <div class="absolute top-4 left-0 right-0 h-1 bg-gray-200">
                <div class="h-full bg-primary" :style="`width: ${getProgressWidth(selectedOrder.status)}%`"></div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

            <!-- Payment Info -->
            <div>
              <h3 class="text-base font-medium mb-2">Payment Information</h3>
              <div class="bg-gray-50 p-3 rounded">
                <div class="text-sm mb-1"><span class="font-medium">Payment ID:</span> {{ selectedOrder.payment_id }}</div>
                <div class="text-sm mb-1"><span class="font-medium">Payment Method:</span> {{ selectedOrder.payment_method || 'Credit Card' }}</div>
                <div class="text-sm"><span class="font-medium">Status:</span> 
                  <span :class="getStatusClass(selectedOrder.status)">{{ selectedOrder.status }}</span>
                </div>
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
                      <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-200 rounded-md overflow-hidden flex-shrink-0 mr-3">
                          <img :src="item.image || '/images/products/placeholder.png'" :alt="item.name" class="w-full h-full object-cover">
                        </div>
                        {{ item.name }}
                      </div>
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

          <!-- Tracking Info -->
          <div v-if="selectedOrder.tracking_number" class="mt-6">
            <h3 class="text-base font-medium mb-2">Tracking Information</h3>
            <div class="bg-gray-50 p-3 rounded">
              <div class="text-sm mb-1"><span class="font-medium">Tracking Number:</span> {{ selectedOrder.tracking_number }}</div>
              <div class="text-sm mb-3"><span class="font-medium">Carrier:</span> {{ selectedOrder.carrier || 'USPS' }}</div>
              <a 
                :href="getTrackingUrl(selectedOrder)" 
                target="_blank"
                class="text-primary hover:text-primary-dark text-sm flex items-center"
              >
                <span>Track Package</span>
                <i class="material-icons text-sm ml-1">open_in_new</i>
              </a>
            </div>
          </div>
        </q-card-section>

        <q-separator />

        <q-card-actions align="right">
          <q-btn flat label="Close" color="primary" v-close-popup />
          <q-btn 
            v-if="isOrderCancellable(selectedOrder)" 
            flat 
            label="Cancel Order" 
            color="negative" 
            @click="cancelOrder(selectedOrder.id)" 
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import { useAuthStore } from '../../../stores/auth';
import { useRouter } from 'vue-router';
import axios from 'axios';

const $q = useQuasar();
const authStore = useAuthStore();
const router = useRouter();

// Redirect to login if not authenticated
if (!authStore.authenticated) {
  router.push({ name: 'login' });
}

// State
const loading = ref(false);
const error = ref(null);
const orders = ref([]);
const totalOrders = ref(0);
const currentPage = ref(1);
const perPage = ref(5);
const selectedOrder = ref(null);
const showOrderModal = ref(false);

// Load user orders
const loadOrders = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const params = {
      page: currentPage.value,
      per_page: perPage.value
    };
    
    const response = await axios.get('/api/orders', { params });
    
    orders.value = response.data.data;
    totalOrders.value = response.data.meta.total;
    
  } catch (err) {
    console.error('Error loading orders:', err);
    error.value = 'Failed to load your orders. Please try again.';
  } finally {
    loading.value = false;
  }
};

// View order details
const viewOrder = (order) => {
  selectedOrder.value = order;
  showOrderModal.value = true;
};

// Cancel an order
const cancelOrder = async (orderId) => {
  try {
    await axios.post(`/api/orders/${orderId}/cancel`);
    
    // Update order in the list
    const order = orders.value.find(o => o.id === orderId);
    if (order) {
      order.status = 'cancelled';
    }
    
    // Update selected order if open
    if (selectedOrder.value && selectedOrder.value.id === orderId) {
      selectedOrder.value.status = 'cancelled';
    }
    
    $q.notify({
      message: 'Order cancelled successfully',
      color: 'positive',
      position: 'top',
      timeout: 2000
    });
    
    showOrderModal.value = false;
    
  } catch (error) {
    console.error('Error cancelling order:', error);
    $q.notify({
      message: 'Failed to cancel order. ' + (error.response?.data?.message || 'Please try again.'),
      color: 'negative',
      position: 'top',
      timeout: 2000
    });
  }
};

// Check if order can be cancelled
const isOrderCancellable = (order) => {
  if (!order) return false;
  return ['pending', 'processing'].includes(order.status);
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

// Get status badge class
const getStatusClass = (status) => {
  const classes = {
    'pending': 'bg-blue-100 text-blue-800',
    'processing': 'bg-yellow-100 text-yellow-800',
    'completed': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800',
    'shipped': 'bg-purple-100 text-purple-800'
  };
  
  return classes[status] || 'bg-gray-100 text-gray-800';
};

// Get progress step class
const getStepClass = (step, status) => {
  // Map statuses to step numbers
  const statusToStep = {
    'pending': 1,
    'processing': 2,
    'shipped': 3,
    'completed': 4,
    'cancelled': -1
  };
  
  const currentStep = statusToStep[status] || 0;
  
  // If cancelled, all steps are red
  if (status === 'cancelled') {
    return 'bg-red-100 text-red-600';
  }
  
  // If the current step is at or past this step, it's active
  if (step <= currentStep) {
    return 'bg-primary text-white';
  }
  
  // Otherwise it's inactive
  return 'bg-gray-200 text-gray-400';
};

// Get progress bar width based on status
const getProgressWidth = (status) => {
  const progressMap = {
    'pending': 25,
    'processing': 50,
    'shipped': 75,
    'completed': 100,
    'cancelled': 0
  };
  
  return progressMap[status] || 0;
};

// Get tracking URL
const getTrackingUrl = (order) => {
  if (!order || !order.tracking_number) return '#';
  
  const carrier = order.carrier || 'usps';
  
  // Map carriers to tracking URLs
  const carrierUrls = {
    'usps': `https://tools.usps.com/go/TrackConfirmAction?tLabels=${order.tracking_number}`,
    'ups': `https://www.ups.com/track?tracknum=${order.tracking_number}`,
    'fedex': `https://www.fedex.com/fedextrack/?trknbr=${order.tracking_number}`,
    'dhl': `https://www.dhl.com/us-en/home/tracking.html?tracking-id=${order.tracking_number}`
  };
  
  return carrierUrls[carrier.toLowerCase()] || '#';
};

// Load orders on mount
onMounted(() => {
  loadOrders();
});
</script>

<style scoped>
.user-orders-page {
  padding: 1.5rem;
}
</style>
