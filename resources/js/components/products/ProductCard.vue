<template>
  <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg transition-transform hover:scale-105">
    <div class="relative">
      <img :src="product.image" :alt="product.name" class="w-full h-48 object-cover" />
      <span v-if="product.discount" class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
        {{ product.discount }}% OFF
      </span>
    </div>
    
    <div class="p-4">
      <h3 class="text-lg font-medium text-white">{{ product.name }}</h3>
      <div class="mt-2 flex items-center">
        <span v-if="product.originalPrice" class="text-sm text-gray-400 line-through mr-2">
          ${{ product.originalPrice.toFixed(2) }}
        </span>
        <span class="text-xl font-semibold text-white">${{ product.price.toFixed(2) }}</span>
      </div>
      
      <p class="mt-2 text-sm text-gray-300 h-12 overflow-hidden">
        {{ product.description }}
      </p>
      
      <div class="mt-4 flex justify-between items-center">
        <router-link 
          :to="{ name: 'product.show', params: { id: product.id } }" 
          class="text-blue-400 text-sm hover:underline"
        >
          View Details
        </router-link>
        
        <button 
          @click="addToCart" 
          class="bg-white text-black font-medium py-1 px-3 rounded hover:bg-gray-200 flex items-center"
          :class="{ 'opacity-50 cursor-not-allowed': isLoading }"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="mr-1">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          Add
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useCartStore } from '../../stores/cart';
import { useToast } from '../../composables/useToast';

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
});

const cartStore = useCartStore();
const toast = useToast();
const isLoading = ref(false);

const addToCart = async () => {
  isLoading.value = true;
  
  try {
    setTimeout(() => {
      // Add the product to the cart
      cartStore.addItem(props.product);
      
      // Display a success toast
      toast.success(`${props.product.name} added to cart!`);
      
      isLoading.value = false;
    }, 500); // Simulated delay for UI feedback
  } catch (error) {
    toast.error("Couldn't add product to cart");
    isLoading.value = false;
  }
};
</script>
