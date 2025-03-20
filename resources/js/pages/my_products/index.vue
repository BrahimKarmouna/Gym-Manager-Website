<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div class="mb-8 bg-gray-50 p-6 rounded-lg">
      <h1 class="text-2xl font-bold text-gray-800">Product Management</h1>
      <p class="text-gray-600">Manage your fitness products inventory</p>
    </div>

    <!-- Actions Bar -->
    <div class="flex justify-between items-center mb-6">
      <div class="relative w-64">
        <input 
          type="text" 
          placeholder="Search products..." 
          v-model="searchQuery"
          class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </span>
      </div>
      <button 
        @click="openProductForm()" 
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add New Product
      </button>
    </div>

    <!-- Filter Bar -->
    <div class="flex gap-4 mb-6">
      <select 
        v-model="categoryFilter" 
        class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="">All Categories</option>
        <option value="Equipment">Equipment</option>
        <option value="Apparel">Apparel</option>
        <option value="Supplements">Supplements</option>
      </select>
      <select 
        v-model="sortOption" 
        class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="default">Featured</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
        <option value="name-asc">Name: A to Z</option>
      </select>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
      <table class="w-full table-auto">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Product</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Category</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Price</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="product in filteredProducts" :key="product.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <img class="h-10 w-10 rounded-md object-cover" :src="product.imageUrl" alt="">
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                  <div class="text-sm text-gray-500" v-if="product.badge">{{ product.badge }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ product.category }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">${{ product.price.toFixed(2) }}</div>
              <div class="text-sm text-gray-500" v-if="product.originalPrice">${{ product.originalPrice.toFixed(2) }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                Active
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button @click="editProduct(product)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
              <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center">
      <div class="text-sm text-gray-700">
        Showing <span class="font-medium">{{ startIndex + 1 }}</span> to 
        <span class="font-medium">{{ Math.min(startIndex + itemsPerPage, filteredProductsTotal) }}</span> of 
        <span class="font-medium">{{ filteredProductsTotal }}</span> products
      </div>
      <div class="flex gap-2">
        <button 
          @click="currentPage--" 
          :disabled="currentPage === 1" 
          class="px-3 py-1 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Previous
        </button>
        <button 
          v-for="page in totalPages" 
          :key="page" 
          @click="currentPage = page" 
          class="px-3 py-1 border border-gray-300 rounded-md"
          :class="{'bg-blue-600 text-white': page === currentPage}"
        >
          {{ page }}
        </button>
        <button 
          @click="currentPage++" 
          :disabled="currentPage === totalPages" 
          class="px-3 py-1 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Product Form Modal -->
    <div v-if="showProductForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">{{ editingProduct ? 'Edit Product' : 'Add New Product' }}</h2>
          <button @click="closeProductForm" class="text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveProduct">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
              <input v-model="formProduct.name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <select v-model="formProduct.category" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                <option value="Equipment">Equipment</option>
                <option value="Apparel">Apparel</option>
                <option value="Supplements">Supplements</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
              <input v-model.number="formProduct.price" type="number" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Original Price ($) (Optional)</label>
              <input v-model.number="formProduct.originalPrice" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Badge (Optional)</label>
              <input v-model="formProduct.badge" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
              <input v-model="formProduct.imageUrl" type="url" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Product Colors (comma separated hex codes)</label>
            <input v-model="colorsInput" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g. #000000, #ff0000, #0000ff">
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" @click="closeProductForm" class="px-4 py-2 border border-gray-300 rounded-md">
              Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              {{ editingProduct ? 'Update Product' : 'Create Product' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      searchQuery: '',
      categoryFilter: '',
      sortOption: 'default',
      currentPage: 1,
      itemsPerPage: 10,
      showProductForm: false,
      editingProduct: false,
      formProduct: {
        id: null,
        name: '',
        category: 'Equipment',
        price: 0,
        originalPrice: null,
        badge: '',
        colors: [],
        imageUrl: '',
      },
      colorsInput: '',
      products: [
        {
          id: 1,
          name: 'Pro Performance Training Shoes',
          category: 'Apparel',
          price: 129.99,
          originalPrice: 159.99,
          badge: 'New',
          colors: ['#000', '#e63946', '#457b9d'],
          imageUrl: 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/58359469-0302-4e59-b404-c5987cc17763/metcon-8-workout-shoes-Cczj2B.png',
        },
        // Keeping the existing product data from your original component...
        {
          id: 2,
          name: 'Premium Weight Lifting Belt',
          category: 'Equipment',
          price: 59.99,
          colors: ['#000', '#6d597a'],
          imageUrl: 'https://contents.mediadecathlon.com/p2154743/k$6c1a68bf1fc06a394e2aca56599594d2/weight-training-leather-belt.jpg',
        },
        // ... rest of the products data
      ]
    };
  },
  computed: {
    allFilteredProducts() {
      let result = [...this.products];
      
      // Apply search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        result = result.filter(product => 
          product.name.toLowerCase().includes(query) || 
          product.category.toLowerCase().includes(query)
        );
      }
      
      // Apply category filter
      if (this.categoryFilter) {
        result = result.filter(product => product.category === this.categoryFilter);
      }
      
      // Apply sorting
      switch (this.sortOption) {
        case 'price-asc':
          result.sort((a, b) => a.price - b.price);
          break;
        case 'price-desc':
          result.sort((a, b) => b.price - a.price);
          break;
        case 'name-asc':
          result.sort((a, b) => a.name.localeCompare(b.name));
          break;
      }
      
      return result;
    },
    filteredProducts() {
      const startIndex = (this.currentPage - 1) * this.itemsPerPage;
      return this.allFilteredProducts.slice(startIndex, startIndex + this.itemsPerPage);
    },
    filteredProductsTotal() {
      return this.allFilteredProducts.length;
    },
    totalPages() {
      return Math.ceil(this.filteredProductsTotal / this.itemsPerPage);
    },
    startIndex() {
      return (this.currentPage - 1) * this.itemsPerPage;
    }
  },
  methods: {
    openProductForm() {
      this.editingProduct = false;
      this.formProduct = {
        id: Date.now(), // Simple way to generate unique ID
        name: '',
        category: 'Equipment',
        price: 0,
        originalPrice: null,
        badge: '',
        colors: [],
        imageUrl: '',
      };
      this.colorsInput = '';
      this.showProductForm = true;
    },
    closeProductForm() {
      this.showProductForm = false;
    },
    editProduct(product) {
      this.editingProduct = true;
      this.formProduct = { ...product };
      this.colorsInput = product.colors.join(', ');
      this.showProductForm = true;
    },
    saveProduct() {
      // Parse colors from input
      this.formProduct.colors = this.colorsInput
        .split(',')
        .map(color => color.trim())
        .filter(color => color);
      
      if (this.editingProduct) {
        // Update existing product
        const index = this.products.findIndex(p => p.id === this.formProduct.id);
        if (index !== -1) {
          this.products.splice(index, 1, { ...this.formProduct });
        }
      } else {
        // Add new product
        this.products.push({ ...this.formProduct });
      }
      
      this.closeProductForm();
    },
    deleteProduct(id) {
      if (confirm('Are you sure you want to delete this product?')) {
        this.products = this.products.filter(p => p.id !== id);
      }
    }
  }
};
</script>
