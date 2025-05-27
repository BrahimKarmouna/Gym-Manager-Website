<template>
  <div v-if="cartStore.isCartOpen" class="fixed inset-0 z-50 overflow-hidden">
    <!-- Backdrop -->
    <div 
      class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" 
      @click="cartStore.toggleCart"
    ></div>
    
    <!-- Sidebar -->
    <div class="absolute inset-y-0 right-0 max-w-full flex">
      <div class="relative w-screen max-w-md">
        <div class="h-full flex flex-col bg-gray-900 shadow-xl overflow-hidden">
          <!-- Header -->
          <div class="px-4 pt-5 pb-4 sm:px-6">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-medium text-white">Shopping Cart ({{ cartStore.count }})</h2>
              <button 
                @click="cartStore.toggleCart" 
                class="text-gray-400 hover:text-white focus:outline-none"
                aria-label="Close cart"
              >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
          
          <!-- Empty state -->
          <div v-if="cartStore.isEmpty" class="flex-1 flex flex-col items-center justify-center p-6">
            <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="mt-4 text-gray-300">Your cart is empty</p>
            <button 
              @click="cartStore.toggleCart" 
              class="mt-6 px-4 py-2 bg-white text-black font-medium rounded-lg hover:bg-gray-200"
            >
              Continue Shopping
            </button>
          </div>
          
          <!-- Cart items -->
          <div v-else class="flex-1 flex flex-col overflow-y-auto">
            <ul class="divide-y divide-gray-700">
              <li v-for="item in cartStore.items" :key="item.id" class="px-4 py-4">
                <div class="flex items-center">
                  <!-- Product image -->
                  <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-md">
                    <img :src="item.image" :alt="item.name" class="h-full w-full object-cover" />
                  </div>
                  
                  <!-- Product details -->
                  <div class="ml-4 flex-1">
                    <div class="flex justify-between">
                      <h3 class="text-base font-medium text-white">{{ item.name }}</h3>
                      <p class="text-sm font-medium text-white">${{ (item.price * item.quantity).toFixed(2) }}</p>
                    </div>
                    <p class="mt-1 text-sm text-gray-400">${{ item.price.toFixed(2) }} each</p>
                    
                    <!-- Quantity -->
                    <div class="flex items-center mt-2">
                      <button 
                        @click="updateQuantity(item.id, item.quantity - 1)" 
                        class="text-gray-400 hover:text-white"
                        :disabled="item.quantity <= 1"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                      </button>
                      <span class="mx-2 text-sm text-white">{{ item.quantity }}</span>
                      <button 
                        @click="updateQuantity(item.id, item.quantity + 1)" 
                        class="text-gray-400 hover:text-white"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                      </button>
                      
                      <!-- Remove item -->
                      <button 
                        @click="removeItem(item.id)" 
                        class="ml-auto text-gray-400 hover:text-red-500"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          
          <!-- Footer -->
          <div v-if="!cartStore.isEmpty" class="p-4 border-t border-gray-700">
            <div class="flex justify-between text-base font-medium text-white mb-4">
              <p>Subtotal</p>
              <p>${{ cartStore.total.toFixed(2) }}</p>
            </div>
            <div class="mt-6">
              <router-link 
                :to="{ name: 'checkout' }"
                class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-black bg-white hover:bg-gray-200"
                @click="cartStore.toggleCart"
              >
                Checkout
              </router-link>
              <div class="mt-4 text-center">
                <button 
                  @click="cartStore.toggleCart" 
                  class="text-sm font-medium text-gray-400 hover:text-white"
                >
                  Continue Shopping
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '../../stores/cart';
import { useToast } from '../../composables/useToast';

const cartStore = useCartStore();
const toast = useToast();

const updateQuantity = (productId, quantity) => {
  if (quantity < 1) return;
  
  cartStore.updateQuantity(productId, quantity);
  toast.success('Cart updated');
};

const removeItem = (productId) => {
  cartStore.removeItem(productId);
  toast.success('Item removed from cart');
};
</script>
