<template>
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
          <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
            <div v-for="product in filteredProducts" :key="product.id" class="group relative">
              <img :src="product.image" :alt="product.name" class="aspect-square w-full rounded-md bg-gray-200 object-cover group-hover:opacity-75 lg:aspect-auto lg:h-80">
              <div class="mt-4 flex justify-between">
                <div>
                  <h3 class="text-sm text-white">
                    <a href="#">
                      <span aria-hidden="true" class="absolute inset-0"></span>
                      {{ product.name }}
                    </a>
                  </h3>
                  <p class="mt-1 text-sm text-white">{{ product.color }}</p>
                </div>
                <p class="text-sm font-medium text-white">${{ product.price }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        searchQuery: '',
        selectedCategory: '',
        selectedPrice: '',
        products: [
          { id: 1, name: 'Basic Tee', color: 'Black', price: 35, image: 'https://tailwindui.com/plus-assets/img/ecommerce-images/product-page-01-related-product-01.jpg', category: 'clothing' },
          { id: 2, name: 'Headphones', color: 'White', price: 75, image: 'https://tailwindui.com/plus-assets/img/ecommerce-images/product-page-01-related-product-02.jpg', category: 'electronics' },
          { id: 3, name: 'Watch', color: 'Gold', price: 150, image: 'https://tailwindui.com/plus-assets/img/ecommerce-images/product-page-01-related-product-03.jpg', category: 'accessories' },
          // More products...
        ]
      };
    },
    computed: {
      filteredProducts() {
        return this.products.filter(product => {
          const matchesSearchQuery = product.name.toLowerCase().includes(this.searchQuery.toLowerCase());
          const matchesCategory = this.selectedCategory ? product.category === this.selectedCategory : true;
          const matchesPrice = this.selectedPrice === 'under-50' ? product.price < 50 : 
                               this.selectedPrice === '50-100' ? (product.price >= 50 && product.price <= 100) : 
                               this.selectedPrice === '100-200' ? (product.price >= 100 && product.price <= 200) : 
                               true;
          return matchesSearchQuery && matchesCategory && matchesPrice;
        });
      }
    }
  };
  </script>
  