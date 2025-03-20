<template>
  <StripeForm />
  <div class="h-screen bg-black">
    <div class="bg-black">
      <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <!-- Search and Filters -->
        <div class="flex justify-between items-center mb-6">
          <div class="flex items-center space-x-4">
            <!-- Search Bar -->
            <input
              type="text"
              placeholder="Search products..."
              class="px-4 py-2 bg-gray-800 text-white rounded-md w-64"
              v-model="searchQuery"
            />
          </div>
          <div class="flex items-center space-x-4">
            <!-- Filters -->
            <select
              v-model="selectedCategory"
              class="bg-gray-800 text-white px-4 py-2 rounded-md"
            >
              <option value="">All Categories</option>
              <option value="clothing">Clothing</option>
              <option value="electronics">Electronics</option>
              <option value="accessories">Accessories</option>
            </select>
            <select
              v-model="selectedPrice"
              class="bg-gray-800 text-white px-4 py-2 rounded-md"
            >
              <option value="">Price Range</option>
              <option value="under-50">Under $50</option>
              <option value="50-100">$50 - $100</option>
              <option value="100-200">$100 - $200</option>
            </select>
          </div>
        </div>

        <!-- Product List -->
        <h2 class="text-2xl font-bold tracking-tight text-white">Customers also purchased</h2>

        <!-- Using router-link to wrap the whole product section -->
        <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
          <router-link
            v-for="product in filteredProducts"
            :key="product.id"
            :to="`/product_show`"
            class="group"
          >
            <div class="flex flex-col items-center">
              <img
                :src="product.image"
                :alt="product.name"
                class="aspect-square w-full rounded-md bg-gray-200 object-cover group-hover:opacity-75 lg:aspect-auto lg:h-80"
              />
              <div class="mt-4 flex justify-between w-full">
                <div>
                  <h3 class="text-sm text-white">
                    <span>{{ product.name }}</span>
                  </h3>
                  <p class="mt-1 text-sm text-white">{{ product.color }}</p>
                </div>
                <p class="text-sm font-medium text-white">${{ product.price }}</p>
                <button @click="addToCart(product)" class="add-to-cart-button">
                  Add to Cart
                </button>
              </div>
            </div>
          </router-link>
        </div>
        <div class="cart-icon" @click="cartStore.showCartPanel()">
          <span class="material-icons">shopping_cart</span>
          <span class="cart-count" v-if="cartStore.totalItems > 0">{{ cartStore.totalItems }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useCartStore } from '../../../stores/cart';
import StripeForm from '@/pages/stripe/stripeForm.vue';

// Initialize cart store
const cartStore = useCartStore();

// Product state
const products = ref([]);
const loading = ref(true);
const error = ref(null);
const searchQuery = ref('');
const selectedCategory = ref('');
const sortBy = ref('name-asc');
const categories = ref([]);
const showToast = ref(false);
const toastMessage = ref('');

// Computed filtered products 
const filteredProducts = computed(() => {
  let result = [...products.value];
  
  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(product => 
      product.name.toLowerCase().includes(query) || 
      product.description.toLowerCase().includes(query)
    );
  }
  
  // Apply category filter
  if (selectedCategory.value !== '') {
    result = result.filter(product => product.category === selectedCategory.value);
  }
  
  // Apply sorting
  if (sortBy.value === 'name-asc') {
    result.sort((a, b) => a.name.localeCompare(b.name));
  } else if (sortBy.value === 'name-desc') {
    result.sort((a, b) => b.name.localeCompare(a.name));
  } else if (sortBy.value === 'price-asc') {
    result.sort((a, b) => a.price - b.price);
  } else if (sortBy.value === 'price-desc') {
    result.sort((a, b) => b.price - a.price);
  }
  
  return result;
});

// Fetch products
const fetchProducts = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/api/products');
    products.value = response.data;
    
    // Extract unique categories
    const uniqueCategories = new Set(products.value.map(product => product.category));
    categories.value = Array.from(uniqueCategories);
  } catch (err) {
    error.value = 'Failed to load products. Please try again later.';
    console.error('Error fetching products:', err);
  } finally {
    loading.value = false;
  }
};

// Add to cart function
const addToCart = async (product) => {
  await cartStore.addToCart(product, 1);
  toastMessage.value = `${product.name} added to cart!`;
  showToast.value = true;
  setTimeout(() => {
    showToast.value = false;
  }, 3000);
};

// Format price
const formatPrice = (price) => {
  return cartStore.formatPrice(price);
};

// Lifecycle hook
onMounted(async () => {
  await cartStore.initCart();
  await fetchProducts();
});
</script>

<style scoped>
.products-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  position: relative;
}

.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

h1 {
  font-size: 2rem;
  color: #333;
}

.cart-icon {
  position: relative;
  cursor: pointer;
}

.cart-icon .material-icons {
  font-size: 2rem;
  color: #333;
}

.cart-count {
  position: absolute;
  top: -8px;
  right: -8px;
  background-color: #ff5722;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
}

.filters {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}

.search-container {
  flex: 1;
  min-width: 250px;
}

.search-input {
  width: 100%;
  padding: 10px 15px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 1rem;
}

.filter-options {
  display: flex;
  gap: 10px;
}

.filter-select {
  padding: 10px 15px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 1rem;
  cursor: pointer;
}

.loading-state,
.error-state,
.empty-state {
  text-align: center;
  padding: 40px;
  color: #666;
}

.loading-spinner {
  display: inline-block;
  width: 40px;
  height: 40px;
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-left-color: #ff5722;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.retry-button {
  margin-top: 10px;
  padding: 8px 16px;
  background-color: #ff5722;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1rem;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.product-card {
  border: 1px solid #eee;
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.product-image {
  height: 200px;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-details {
  padding: 15px;
}

.product-title {
  font-size: 1.2rem;
  margin-bottom: 8px;
  color: #333;
}

.product-price {
  font-size: 1.1rem;
  font-weight: bold;
  color: #ff5722;
  margin-bottom: 8px;
}

.product-description {
  font-size: 0.9rem;
  color: #666;
  margin-bottom: 15px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
}

.product-actions {
  display: flex;
  justify-content: space-between;
}

.view-details-button,
.add-to-cart-button {
  padding: 8px 12px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 0.9rem;
  text-align: center;
  text-decoration: none;
}

.view-details-button {
  background-color: #f5f5f5;
  color: #333;
  border: 1px solid #ddd;
  flex: 1;
  margin-right: 8px;
}

.add-to-cart-button {
  background-color: #ff5722;
  color: white;
  border: none;
  flex: 1;
}

.view-details-button:hover {
  background-color: #eee;
}

.add-to-cart-button:hover {
  background-color: #e64a19;
}

.toast-notification {
  position: fixed;
  bottom: -100px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #4CAF50;
  color: white;
  padding: 16px 24px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  transition: bottom 0.3s;
}

.toast-notification.show {
  bottom: 20px;
}

.toast-content {
  display: flex;
  align-items: center;
  gap: 10px;
}

.success-icon {
  font-size: 1.5rem;
}

@media (max-width: 768px) {
  .product-grid {
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  }
  
  .filters {
    flex-direction: column;
  }
  
  .filter-options {
    width: 100%;
  }
  
  .filter-select {
    flex: 1;
  }
}
</style>
