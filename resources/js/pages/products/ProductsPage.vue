<template>
  <div class="bg-black min-h-screen">
    <div class="container mx-auto px-4 py-8">
      <!-- Page Header -->
      <div class="text-center mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Fitness Products</h1>
        <p class="text-gray-300">High-quality equipment and supplements to support your fitness journey</p>
      </div>
      
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center py-20">
        <svg class="animate-spin h-12 w-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      
      <!-- Error State -->
      <div v-else-if="error" class="bg-red-500 bg-opacity-20 border border-red-500 text-white p-4 rounded-lg text-center">
        {{ error }}
        <button @click="fetchProducts" class="ml-4 px-4 py-1 bg-white text-red-600 rounded-lg text-sm font-medium">
          Try Again
        </button>
      </div>
      
      <!-- Product Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="product in products" :key="product.id" class="bg-gray-800 rounded-lg overflow-hidden shadow-lg transition-all hover:-translate-y-1 hover:shadow-xl">
          <router-link :to="`/products/${product.id}`" class="block relative">
            <img :src="product.image" :alt="product.name" class="w-full h-56 object-cover" />
            <span v-if="product.discount" class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
              {{ product.discount }}% OFF
            </span>
          </router-link>
          
          <div class="p-4">
            <h3 class="text-lg font-semibold text-white">{{ product.name }}</h3>
            <div class="mt-2 flex items-center">
              <span v-if="product.originalPrice" class="text-sm text-gray-400 line-through mr-2">
                ${{ product.originalPrice.toFixed(2) }}
              </span>
              <span class="text-xl font-bold text-white">${{ product.price.toFixed(2) }}</span>
            </div>
            
            <p class="mt-2 text-sm text-gray-300 h-12 overflow-hidden">
              {{ product.description }}
            </p>
            
            <div class="mt-4 flex justify-between items-center">
              <router-link 
                :to="`/products/${product.id}`" 
                class="text-blue-400 hover:underline"
              >
                View Details
              </router-link>
              
              <button 
                @click="addToCart(product)"
                class="bg-white text-black py-1 px-4 rounded-lg hover:bg-gray-200 flex items-center"
                :disabled="!product.inStock"
                :class="{ 'opacity-50 cursor-not-allowed': !product.inStock }"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Add
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useCartStore } from '../../stores/cart';
import { useToast } from '../../composables/useToast';

const cartStore = useCartStore();
const toast = useToast();

const products = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchProducts = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    // Simulate API request delay
    await new Promise(resolve => setTimeout(resolve, 800));
    
    // Mock product data - replace with actual API call
    products.value = [
      {
        id: '1',
        name: 'Premium Fitness Set',
        price: 99.99,
        originalPrice: 129.99,
        discount: 23,
        description: 'Complete fitness set for home workouts. This premium kit includes everything you need to stay fit.',
        image: '/images/products/fitness-set.jpg',
        inStock: true
      },
      {
        id: '2',
        name: 'Yoga Mat',
        price: 24.99,
        description: 'Anti-slip yoga mat for comfortable workouts',
        image: '/images/products/yoga-mat.jpg',
        inStock: true
      },
      {
        id: '3',
        name: 'Resistance Bands',
        price: 19.99,
        originalPrice: 29.99,
        discount: 33,
        description: 'Set of 5 resistance bands with different strength levels',
        image: '/images/products/resistance-bands.jpg',
        inStock: true
      },
      {
        id: '4',
        name: 'Adjustable Dumbbells',
        price: 149.99,
        description: 'Pair of adjustable dumbbells for strength training',
        image: '/images/products/dumbbells.jpg',
        inStock: false
      },
      {
        id: '5',
        name: 'Whey Protein Powder',
        price: 39.99,
        description: 'High-quality protein powder for muscle recovery',
        image: '/images/products/protein.jpg',
        inStock: true
      },
      {
        id: '6',
        name: 'Pre-Workout Formula',
        price: 29.99,
        originalPrice: 34.99,
        discount: 14,
        description: 'Boost your energy for intense workouts',
        image: '/images/products/pre-workout.jpg',
        inStock: true
      },
      {
        id: '7',
        name: 'BCAA Supplement',
        price: 24.99,
        description: 'Branched-Chain Amino Acids for muscle recovery',
        image: '/images/products/bcaa.jpg',
        inStock: true
      },
      {
        id: '8',
        name: 'Multivitamin Pack',
        price: 19.99,
        description: 'Complete daily vitamin and mineral support',
        image: '/images/products/vitamins.jpg',
        inStock: true
      }
    ];
    
  } catch (err) {
    console.error('Failed to fetch products:', err);
    error.value = 'Failed to load products. Please try again.';
  } finally {
    loading.value = false;
  }
};

const addToCart = (product) => {
  cartStore.addItem(product);
  toast.success(`${product.name} added to cart!`);
};

onMounted(() => {
  fetchProducts();
});
</script>

<style scoped>
.product-card {
  transition: all 0.3s ease;
}
.product-card:hover {
  transform: translateY(-5px);
}
</style>
