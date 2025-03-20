<template>
  <div class="order-confirmation-page">
    <div v-if="loading" class="flex justify-center py-12">
      <q-spinner color="primary" size="3em" />
    </div>
    
    <div v-else-if="error" class="max-w-2xl mx-auto bg-red-100 text-red-700 p-6 rounded-xl my-8">
      <h2 class="text-xl font-bold mb-2">An error occurred</h2>
      <p>{{ error }}</p>
      <div class="mt-6">
        <router-link :to="{ name: 'products' }" class="text-primary hover:underline">
          Continue shopping
        </router-link>
      </div>
    </div>
    
    <div v-else class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-6 md:p-8 my-8">
      <!-- Success Header -->
      <div class="text-center mb-8">
        <div class="mx-auto w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
          <i class="material-icons text-3xl">check</i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Order Confirmed!</h1>
        <p class="text-gray-600 mt-2">Thank you for your purchase</p>
      </div>
      
      <!-- Order Details -->
      <div class="border border-gray-200 rounded-lg mb-6">
        <div class="p-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <div>
              <div class="text-sm text-gray-500">Order #</div>
              <div class="font-medium">{{ order.id }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Date</div>
              <div>{{ formatDate(order.created_at) }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Status</div>
              <div>
                <span :class="getStatusClass(order.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ order.status }}
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Order Items -->
        <div class="p-4">
          <div class="mb-4">
            <div class="text-sm font-medium text-gray-500 mb-2">Items</div>
            <div class="space-y-3">
              <div 
                v-for="item in order.items" 
                :key="item.id" 
                class="flex justify-between items-center"
              >
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-gray-100 rounded overflow-hidden mr-3 flex-shrink-0">
                    <img :src="item.image || '/images/products/placeholder.png'" :alt="item.name" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <div class="text-sm font-medium">{{ item.name }}</div>
                    <div class="text-xs text-gray-500">Qty: {{ item.quantity }}</div>
                  </div>
                </div>
                <div class="text-sm font-medium">${{ formatPrice(item.price * item.quantity) }}</div>
              </div>
            </div>
          </div>
          
          <!-- Order Summary -->
          <div class="border-t border-gray-200 pt-4 mt-4">
            <div class="flex justify-between mb-1">
              <span class="text-sm text-gray-500">Subtotal</span>
              <span class="text-sm">${{ formatPrice(order.subtotal) }}</span>
            </div>
            <div v-if="order.discount > 0" class="flex justify-between mb-1">
              <span class="text-sm text-gray-500">Discount</span>
              <span class="text-sm text-green-600">-${{ formatPrice(order.discount) }}</span>
            </div>
            <div class="flex justify-between mb-1">
              <span class="text-sm text-gray-500">Tax</span>
              <span class="text-sm">${{ formatPrice(order.tax) }}</span>
            </div>
            <div class="flex justify-between font-bold pt-2 border-t border-gray-200 mt-2">
              <span>Total</span>
              <span>${{ formatPrice(order.total) }}</span>
            </div>
          </div>
        </div>
        
        <!-- Shipping Info -->
        <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <div class="text-sm font-medium text-gray-500 mb-1">Shipping Address</div>
              <div class="text-sm">{{ order.shipping?.name }}</div>
              <div class="text-sm">{{ order.shipping?.address?.line1 }}</div>
              <div class="text-sm">
                {{ order.shipping?.address?.city }}, {{ order.shipping?.address?.state }} {{ order.shipping?.address?.postal_code }}
              </div>
              <div class="text-sm">{{ order.shipping?.address?.country }}</div>
            </div>
            <div>
              <div class="text-sm font-medium text-gray-500 mb-1">Payment Information</div>
              <div class="text-sm">{{ order.payment_method || 'Credit Card' }}</div>
              <div class="text-sm">{{ maskCardNumber(order.payment_details?.last4) }}</div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Next Steps -->
      <div class="bg-blue-50 p-4 rounded-lg text-sm text-blue-800 mb-6">
        <div class="font-medium mb-1">What's Next?</div>
        <p>We're processing your order. You will receive an email confirmation shortly with your order details.</p>
      </div>
      
      <!-- Actions -->
      <div class="flex flex-wrap gap-4 justify-center">
        <router-link 
          :to="{ name: 'user.orders.index' }" 
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none"
        >
          View All Orders
        </router-link>
        <router-link 
          :to="{ name: 'products' }" 
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
        >
          Continue Shopping
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../../stores/auth';
import { useCartStore } from '../../../stores/cart';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();

// If not authenticated, redirect to login
if (!authStore.authenticated) {
  router.push({ name: 'login' });
}

// State
const loading = ref(true);
const error = ref(null);
const order = ref({
  id: '',
  created_at: '',
  status: 'processing',
  items: [],
  subtotal: 0,
  discount: 0,
  tax: 0,
  total: 0,
  shipping: {},
  payment_method: '',
  payment_details: {}
});

// Get order ID from route params
const orderId = route.params.id;

// Load order details
const loadOrder = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    // If order ID is in the URL, fetch from API
    if (orderId) {
      const response = await axios.get(`/api/orders/${orderId}`);
      order.value = response.data;
    } else {
      // Otherwise check local storage for the last order
      const lastOrderId = localStorage.getItem('last_order_id');
      
      if (lastOrderId) {
        const response = await axios.get(`/api/orders/${lastOrderId}`);
        order.value = response.data;
      } else {
        error.value = 'Order not found';
      }
    }
  } catch (err) {
    console.error('Error loading order:', err);
    error.value = 'Failed to load order details. Please check your order history.';
  } finally {
    loading.value = false;
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

// Mask card number
const maskCardNumber = (last4) => {
  if (!last4) return 'Card information not available';
  return `•••• •••• •••• ${last4}`;
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

// Load order on mount and clear cart
onMounted(async () => {
  await loadOrder();
  
  // Clear cart after successful order loading
  if (!error.value) {
    cartStore.clearCart();
    
    // Store last order ID in localStorage
    if (order.value.id) {
      localStorage.setItem('last_order_id', order.value.id);
    }
  }
});
</script>

<style scoped>
.order-confirmation-page {
  padding: 1.5rem;
}
</style>
