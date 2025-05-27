<template>
  <div class="bg-black min-h-screen">
    <div class="container mx-auto px-4 py-8">
      <div v-if="loading" class="flex justify-center py-20">
        <svg class="animate-spin h-12 w-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      
      <div v-else-if="error" class="text-red-500 text-center py-20">
        {{ error }}
      </div>
      
      <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div class="bg-gray-800 rounded-lg overflow-hidden">
          <div class="relative h-96">
            <img :src="product.image" :alt="product.name" class="w-full h-full object-cover" />
            <span v-if="product.discount" class="absolute top-4 right-4 bg-red-600 text-white px-3 py-1 rounded-full">
              {{ product.discount }}% OFF
            </span>
          </div>
          
          <!-- Thumbnail images if available -->
          <div v-if="product.gallery?.length" class="flex justify-center p-4 space-x-2">
            <div 
              v-for="(image, index) in product.gallery" 
              :key="index"
              class="w-16 h-16 cursor-pointer rounded border-2"
              :class="selectedImage === image ? 'border-blue-500' : 'border-transparent'"
              @click="selectedImage = image"
            >
              <img :src="image" :alt="`${product.name} - view ${index+1}`" class="w-full h-full object-cover rounded" />
            </div>
          </div>
        </div>
        
        <!-- Product Info -->
        <div class="flex flex-col">
          <h1 class="text-3xl font-bold text-white">{{ product.name }}</h1>
          
          <div class="mt-2">
            <span v-if="product.originalPrice" class="text-lg text-gray-400 line-through mr-3">
              ${{ product.originalPrice.toFixed(2) }}
            </span>
            <span class="text-3xl font-bold text-white">${{ product.price.toFixed(2) }}</span>
          </div>
          
          <div class="mt-6 space-y-4">
            <p class="text-gray-300">{{ product.description }}</p>
            
            <div class="border-t border-gray-700 pt-4">
              <h3 class="text-lg font-medium text-white mb-2">Features</h3>
              <ul class="list-disc pl-5 text-gray-300 space-y-1">
                <li v-for="(feature, index) in product.features" :key="index">
                  {{ feature }}
                </li>
              </ul>
            </div>
            
            <div v-if="product.inStock" class="text-green-500 flex items-center">
              <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
              </svg>
              In Stock
            </div>
            <div v-else class="text-red-500">Out of Stock</div>
          </div>
          
          <!-- Add to Cart Section -->
          <div class="mt-8 flex items-center">
            <div class="flex items-center mr-4 bg-gray-700 rounded-lg">
              <button 
                @click="decrementQuantity" 
                class="px-3 py-2 text-white hover:bg-gray-600 rounded-l-lg"
                :disabled="quantity <= 1"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
              </button>
              <input 
                type="number" 
                v-model="quantity" 
                min="1" 
                class="w-12 text-center bg-transparent text-white border-0 focus:ring-0"
              />
              <button 
                @click="incrementQuantity" 
                class="px-3 py-2 text-white hover:bg-gray-600 rounded-r-lg"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
              </button>
            </div>
            
            <button 
              @click="addToCart" 
              class="flex-1 bg-white text-black py-3 rounded-lg font-semibold hover:bg-gray-200 transition-colors flex items-center justify-center"
              :disabled="isAddingToCart || !product.inStock"
              :class="{ 'opacity-50 cursor-not-allowed': isAddingToCart || !product.inStock }"
            >
              <span v-if="isAddingToCart" class="mr-2">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              {{ product.inStock ? 'Add to Cart' : 'Out of Stock' }}
            </button>
          </div>
        </div>
      </div>
      
      <!-- Related Products -->
      <div class="mt-12">
        <h2 class="text-2xl font-bold text-white mb-6">Related Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <ProductCard 
            v-for="relatedProduct in relatedProducts"
            :key="relatedProduct.id"
            :product="relatedProduct"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useCartStore } from '../../stores/cart';
import { useToast } from '../../composables/useToast';
import ProductCard from '../../components/products/ProductCard.vue';
import { api } from '@/boot/axios';

const route = useRoute();
const cartStore = useCartStore();
const toast = useToast();

const product = ref(null);
const relatedProducts = ref([]);
const loading = ref(true);
const error = ref(null);
const quantity = ref(1);
const isAddingToCart = ref(false);
const selectedImage = ref('');

const fetchProduct = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    // Replace this with your actual API call
    // const response = await api.get(`/api/products/${route.params.id}`);
    
    // Simulating API response
    await new Promise(resolve => setTimeout(resolve, 800));
    
    // Mocked product data
    product.value = {
      id: route.params.id,
      name: 'Premium Fitness Set',
      price: 99.99,
      originalPrice: 129.99,
      discount: 23,
      description: 'Complete fitness set for home workouts. This premium kit includes everything you need to stay fit and healthy from the comfort of your home.',
      image: '/images/products/fitness-set.jpg',
      gallery: [
        '/images/products/fitness-set-1.jpg',
        '/images/products/fitness-set-2.jpg',
        '/images/products/fitness-set-3.jpg',
      ],
      features: [
        'High-quality materials',
        'Suitable for all fitness levels',
        'Easy to store',
        'Includes workout guide',
        'Lifetime warranty'
      ],
      inStock: true
    };
    
    selectedImage.value = product.value.image;
    
    // Fetch related products
    // const relatedResponse = await api.get(`/api/products/related/${route.params.id}`);
    
    // Mocked related products
    relatedProducts.value = [
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
      }
    ];
    
  } catch (err) {
    console.error('Failed to fetch product:', err);
    error.value = 'Failed to load product. Please try again.';
  } finally {
    loading.value = false;
  }
};

const incrementQuantity = () => {
  quantity.value++;
};

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

const addToCart = () => {
  if (!product.value.inStock) return;
  
  isAddingToCart.value = true;
  
  setTimeout(() => {
    try {
      cartStore.addItem(product.value, quantity.value);
      toast.success(`${quantity.value} × ${product.value.name} added to cart!`);
    } catch (err) {
      toast.error("Couldn't add product to cart");
      console.error(err);
    } finally {
      isAddingToCart.value = false;
    }
  }, 600);
};

onMounted(() => {
  fetchProduct();
});
</script>
