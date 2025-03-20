<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Cart Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Your Cart</h1>
      <p v-if="cartStore.isEmpty" class="text-gray-600 dark:text-gray-400 mt-2">
        Your cart is empty. <router-link to="/products" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Continue shopping</router-link>
      </p>
    </div>

    <!-- Cart Items -->
    <div v-if="!cartStore.isEmpty" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 mb-8">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
              <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
              <th class="px-6 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
              <th class="px-6 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="item in cartStore.items" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <img :src="item.image || '/img/placeholder.png'" alt="Product image" class="h-16 w-16 object-cover rounded-md mr-4">
                  <div>
                    <div class="text-base font-medium text-gray-800 dark:text-white">{{ item.name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ item.description }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${{ cartStore.formatPrice(item.price) }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center">
                  <button 
                    @click="updateQuantity(item.id, item.quantity - 1)" 
                    class="p-2 rounded-l-md bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300"
                    :disabled="item.quantity <= 1"
                    :class="{ 'opacity-50 cursor-not-allowed': item.quantity <= 1 }"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </button>
                  <input 
                    type="number" 
                    min="1" 
                    :value="item.quantity" 
                    @change="e => updateQuantity(item.id, parseInt(e.target.value))"
                    class="w-12 text-center p-1 border-t border-b dark:bg-gray-800 dark:text-white border-gray-200 dark:border-gray-700"
                  >
                  <button 
                    @click="updateQuantity(item.id, item.quantity + 1)" 
                    class="p-2 rounded-r-md bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </button>
                </div>
              </td>
              <td class="px-6 py-4 text-right text-sm font-medium text-gray-700 dark:text-gray-300">${{ cartStore.formatPrice(item.price * item.quantity) }}</td>
              <td class="px-6 py-4 text-right">
                <button @click="removeItem(item.id)" class="text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Order Summary -->
    <div v-if="!cartStore.isEmpty" class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="md:col-span-2">
        <!-- Coupon Code -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 p-6 mb-6">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Have a coupon?</h2>
          <div class="flex">
            <input type="text" v-model="couponCode" placeholder="Enter coupon code" class="flex-grow p-3 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button @click="applyCoupon" :disabled="cartStore.loading" class="bg-blue-600 text-white px-4 py-3 rounded-r-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50">
              {{ cartStore.loading ? 'Applying...' : 'Apply' }}
            </button>
          </div>
          <p v-if="cartStore.error" class="mt-2 text-sm text-red-600 dark:text-red-400">
            {{ cartStore.error }}
          </p>
          <p v-else-if="cartStore.couponCode" class="mt-2 text-sm text-green-600 dark:text-green-400">
            Coupon "{{ cartStore.couponCode }}" applied successfully for {{ cartStore.couponDiscount }}% off!
          </p>
        </div>
      </div>

      <div class="md:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 p-6 sticky top-8">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Order Summary</h2>
          
          <div class="space-y-3">
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
              <span>Subtotal</span>
              <span>${{ cartStore.formatPrice(cartStore.subtotal) }}</span>
            </div>
            <div v-if="cartStore.discount > 0" class="flex justify-between text-green-600 dark:text-green-400">
              <span>Discount</span>
              <span>-${{ cartStore.formatPrice(cartStore.discount) }}</span>
            </div>
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
              <span>Tax (7.5%)</span>
              <span>${{ cartStore.formatPrice(cartStore.tax) }}</span>
            </div>
            <div class="pt-3 mt-3 border-t border-gray-200 dark:border-gray-700">
              <div class="flex justify-between font-semibold text-gray-800 dark:text-white">
                <span>Total</span>
                <span>${{ cartStore.formatPrice(cartStore.total) }}</span>
              </div>
            </div>
          </div>
          
          <div class="mt-6 space-y-3">
            <button 
              @click="checkout" 
              :disabled="cartStore.loading"
              class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
            >
              {{ cartStore.loading ? 'Processing...' : 'Proceed to Checkout' }}
            </button>
            <router-link 
              to="/products" 
              class="block w-full text-center py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              Continue Shopping
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../../stores/auth';
import { useCartStore } from '../../../stores/cart';

const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const couponCode = ref('');

// Initialize cart on page load
onMounted(async () => {
  await cartStore.initCart();
});

// Update item quantity
const updateQuantity = async (itemId, quantity) => {
  await cartStore.updateItemQuantity(itemId, quantity);
};

// Remove item from cart
const removeItem = async (itemId) => {
  await cartStore.removeItem(itemId);
};

// Apply coupon code
const applyCoupon = async () => {
  if (!couponCode.value) return;
  
  const success = await cartStore.applyCoupon(couponCode.value);
  if (success) {
    couponCode.value = ''; // Clear the input field after successful application
  }
};

// Proceed to checkout
const checkout = async () => {
  // Show loading state while processing
  if (cartStore.loading) return;
  
  // Attempt to proceed to checkout
  const result = await cartStore.proceedToCheckout();
  
  // If user needs to login, redirect to login page
  if (result?.redirectToLogin) {
    router.push({ 
      path: '/login', 
      query: { redirect: '/checkout' } 
    });
  } 
  // If checkout session created successfully, redirect to Stripe checkout
  else if (result?.success) {
    window.location.href = result.session.url;
  }
};
</script>