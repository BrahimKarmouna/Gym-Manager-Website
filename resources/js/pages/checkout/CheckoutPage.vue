<template>
  <div class="bg-black min-h-screen py-12">
    <div class="container mx-auto px-4">
      <h1 class="text-3xl font-bold text-white mb-8">Checkout</h1>
      
      <div v-if="cartStore.isEmpty" class="bg-gray-800 rounded-lg p-8 text-center">
        <svg class="w-16 h-16 text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <p class="text-xl text-white mt-4">Your cart is empty</p>
        <p class="text-gray-400 mt-2">Add some products to your cart to check out</p>
        <router-link 
          :to="{ name: 'products' }" 
          class="mt-6 inline-block px-6 py-3 bg-white text-black font-medium rounded-lg hover:bg-gray-200"
        >
          Browse Products
        </router-link>
      </div>
      
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Summary -->
        <div class="lg:col-span-2">
          <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Order Summary</h2>
            
            <div class="overflow-x-auto">
              <table class="w-full text-sm text-left text-white">
                <thead class="text-xs text-gray-400 uppercase border-b border-gray-700">
                  <tr>
                    <th scope="col" class="py-3 px-4">Product</th>
                    <th scope="col" class="py-3 px-4">Quantity</th>
                    <th scope="col" class="py-3 px-4">Price</th>
                    <th scope="col" class="py-3 px-4">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in cartStore.items" :key="item.id" class="border-b border-gray-700">
                    <td class="py-4 px-4">
                      <div class="flex items-center">
                        <img :src="item.image" :alt="item.name" class="h-10 w-10 rounded object-cover mr-3" />
                        <span>{{ item.name }}</span>
                      </div>
                    </td>
                    <td class="py-4 px-4">{{ item.quantity }}</td>
                    <td class="py-4 px-4">${{ item.price.toFixed(2) }}</td>
                    <td class="py-4 px-4">${{ (item.price * item.quantity).toFixed(2) }}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr class="border-b border-gray-700">
                    <td colspan="3" class="py-4 px-4 font-medium">Subtotal</td>
                    <td class="py-4 px-4 font-medium">${{ cartStore.total.toFixed(2) }}</td>
                  </tr>
                  <tr class="border-b border-gray-700">
                    <td colspan="3" class="py-4 px-4 font-medium">Shipping</td>
                    <td class="py-4 px-4 font-medium">${{ shipping.toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <td colspan="3" class="py-4 px-4 font-bold text-lg">Total</td>
                    <td class="py-4 px-4 font-bold text-lg">${{ (cartStore.total + shipping).toFixed(2) }}</td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
        
        <!-- Checkout Form -->
        <div class="lg:col-span-1">
          <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Billing Information</h2>
            
            <form @submit.prevent="placeOrder" class="space-y-4">
              <div>
                <label for="name" class="block text-sm font-medium text-white mb-1">Full Name</label>
                <input 
                  type="text" 
                  id="name" 
                  v-model="form.name" 
                  class="w-full rounded-md bg-gray-700 border-gray-600 text-white py-2 px-3 focus:ring-blue-500 focus:border-blue-500" 
                  required
                />
              </div>
              
              <div>
                <label for="email" class="block text-sm font-medium text-white mb-1">Email Address</label>
                <input 
                  type="email" 
                  id="email" 
                  v-model="form.email" 
                  class="w-full rounded-md bg-gray-700 border-gray-600 text-white py-2 px-3 focus:ring-blue-500 focus:border-blue-500" 
                  required
                />
              </div>
              
              <div>
                <label for="address" class="block text-sm font-medium text-white mb-1">Address</label>
                <input 
                  type="text" 
                  id="address" 
                  v-model="form.address" 
                  class="w-full rounded-md bg-gray-700 border-gray-600 text-white py-2 px-3 focus:ring-blue-500 focus:border-blue-500" 
                  required
                />
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="city" class="block text-sm font-medium text-white mb-1">City</label>
                  <input 
                    type="text" 
                    id="city" 
                    v-model="form.city" 
                    class="w-full rounded-md bg-gray-700 border-gray-600 text-white py-2 px-3 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  />
                </div>
                <div>
                  <label for="postalCode" class="block text-sm font-medium text-white mb-1">Postal Code</label>
                  <input 
                    type="text" 
                    id="postalCode" 
                    v-model="form.postalCode" 
                    class="w-full rounded-md bg-gray-700 border-gray-600 text-white py-2 px-3 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  />
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-white mb-3">Payment Method</label>
                <div class="space-y-2">
                  <label class="flex items-center">
                    <input 
                      type="radio" 
                      name="paymentMethod" 
                      value="card" 
                      v-model="form.paymentMethod"
                      class="w-4 h-4 text-blue-500 bg-gray-700 border-gray-600"
                    />
                    <span class="ml-2 text-sm text-white">Credit Card</span>
                  </label>
                  <label class="flex items-center">
                    <input 
                      type="radio" 
                      name="paymentMethod" 
                      value="paypal" 
                      v-model="form.paymentMethod"
                      class="w-4 h-4 text-blue-500 bg-gray-700 border-gray-600"
                    />
                    <span class="ml-2 text-sm text-white">PayPal</span>
                  </label>
                </div>
              </div>
              
              <!-- Card details (show only if card payment selected) -->
              <div v-if="form.paymentMethod === 'card'" class="space-y-4">
                <div>
                  <label for="cardNumber" class="block text-sm font-medium text-white mb-1">Card Number</label>
                  <input 
                    type="text" 
                    id="cardNumber" 
                    v-model="form.cardNumber" 
                    placeholder="1234 5678 9012 3456"
                    class="w-full rounded-md bg-gray-700 border-gray-600 text-white py-2 px-3 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  />
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label for="expiryDate" class="block text-sm font-medium text-white mb-1">Expiry Date</label>
                    <input 
                      type="text" 
                      id="expiryDate" 
                      v-model="form.expiryDate" 
                      placeholder="MM/YY"
                      class="w-full rounded-md bg-gray-700 border-gray-600 text-white py-2 px-3 focus:ring-blue-500 focus:border-blue-500" 
                      required
                    />
                  </div>
                  <div>
                    <label for="cvv" class="block text-sm font-medium text-white mb-1">CVV</label>
                    <input 
                      type="text" 
                      id="cvv" 
                      v-model="form.cvv" 
                      placeholder="123"
                      class="w-full rounded-md bg-gray-700 border-gray-600 text-white py-2 px-3 focus:ring-blue-500 focus:border-blue-500" 
                      required
                    />
                  </div>
                </div>
              </div>
              
              <button 
                type="submit"
                class="w-full bg-white text-black py-3 px-4 rounded-lg font-medium hover:bg-gray-200 transition-colors flex items-center justify-center"
                :disabled="isProcessing"
              >
                <span v-if="isProcessing" class="mr-2">
                  <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </span>
                {{ isProcessing ? 'Processing...' : `Pay $${(cartStore.total + shipping).toFixed(2)}` }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '../../stores/cart';
import { useToast } from '../../composables/useToast';
import { api } from '@/boot/axios';

const router = useRouter();
const cartStore = useCartStore();
const toast = useToast();

const shipping = 9.99;
const isProcessing = ref(false);

const form = ref({
  name: '',
  email: '',
  address: '',
  city: '',
  postalCode: '',
  paymentMethod: 'card',
  cardNumber: '',
  expiryDate: '',
  cvv: '',
});

const placeOrder = async () => {
  if (cartStore.isEmpty) {
    toast.error('Your cart is empty');
    return;
  }
  
  isProcessing.value = true;
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Place order API call
    // const response = await api.post('/api/orders', {
    //   items: cartStore.items,
    //   customerInfo: form.value,
    //   total: cartStore.total + shipping,
    // });
    
    // Clear cart
    cartStore.clearCart();
    
    // Show success message
    toast.success('Order placed successfully!');
    
    // Redirect to order confirmation
    router.push({ name: 'order.confirmation', params: { orderId: 'ORD-' + Math.floor(100000 + Math.random() * 900000) } });
    
  } catch (error) {
    console.error('Failed to place order:', error);
    toast.error('Failed to place order. Please try again.');
  } finally {
    isProcessing.value = false;
  }
};
</script>
