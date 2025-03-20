<template>
  <div class="products-page">
    <div class="header mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Product Management</h1>
        <p class="text-gray-500">Manage your store products</p>
      </div>
      <q-btn 
        color="primary" 
        label="Add New Product" 
        icon="add" 
        @click="openProductModal()" 
        class="px-4"
      />
    </div>

    <!-- Filters -->
    <div class="filters bg-white rounded-xl shadow-sm p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
          <select 
            v-model="filters.category" 
            class="w-full p-2 border border-gray-300 rounded-lg"
          >
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select 
            v-model="filters.status" 
            class="w-full p-2 border border-gray-300 rounded-lg"
          >
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="out_of_stock">Out of Stock</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
          <input 
            v-model="filters.search" 
            type="text" 
            placeholder="Product name, SKU..."
            class="w-full p-2 border border-gray-300 rounded-lg"
          >
        </div>
        <div class="flex items-end">
          <button 
            @click="loadProducts" 
            class="w-full p-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors"
          >
            Apply Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-8">
      <q-spinner color="primary" size="3em" />
    </div>

    <!-- Error Message -->
    <div v-else-if="error" class="bg-red-100 text-red-700 p-4 rounded-xl mb-6">
      {{ error }}
      <button @click="loadProducts" class="underline ml-2">Try again</button>
    </div>

    <!-- Products Grid -->
    <div v-else-if="products.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="product in products" 
        :key="product.id" 
        class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow"
      >
        <div class="relative h-48 bg-gray-100">
          <img 
            :src="product.image || '/images/products/placeholder.png'" 
            :alt="product.name" 
            class="w-full h-full object-cover"
          >
          <div 
            :class="getStatusBadgeClass(product.status)"
            class="absolute top-2 right-2 px-2 py-1 text-xs font-semibold rounded-full"
          >
            {{ product.status }}
          </div>
        </div>
        <div class="p-4">
          <h3 class="text-lg font-medium text-gray-900 truncate">{{ product.name }}</h3>
          <div class="flex items-center justify-between mt-1">
            <div class="text-lg font-bold text-primary">${{ formatPrice(product.price) }}</div>
            <div class="text-sm text-gray-500">Stock: {{ product.stock_quantity }}</div>
          </div>
          <p class="text-gray-500 text-sm mt-2 line-clamp-2">{{ product.description }}</p>
          <div class="mt-4 flex justify-between">
            <q-btn 
              flat 
              color="primary" 
              icon="edit" 
              label="Edit" 
              @click="openProductModal(product)" 
              size="sm"
            />
            <q-btn 
              flat 
              :color="product.status === 'active' ? 'negative' : 'positive'" 
              :icon="product.status === 'active' ? 'visibility_off' : 'visibility'" 
              :label="product.status === 'active' ? 'Deactivate' : 'Activate'" 
              @click="toggleProductStatus(product)" 
              size="sm"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="bg-white rounded-xl shadow-sm p-8 text-center">
      <div class="mx-auto w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center mb-4">
        <i class="material-icons text-4xl text-gray-400">inventory_2</i>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-1">No products found</h3>
      <p class="text-gray-500 mb-4">There are no products matching your filters.</p>
      <div class="flex justify-center gap-4">
        <button 
          @click="resetFilters" 
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
        >
          Reset Filters
        </button>
        <button 
          @click="openProductModal()" 
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none"
        >
          Add Product
        </button>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="products.length > 0" class="mt-6 flex items-center justify-between">
      <div class="text-sm text-gray-500">
        Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, totalProducts) }} of {{ totalProducts }} products
      </div>
      <div class="flex gap-2">
        <button 
          @click="prevPage" 
          :disabled="currentPage === 1"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Previous
        </button>
        <button 
          @click="nextPage" 
          :disabled="currentPage * perPage >= totalProducts"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Product Modal -->
    <q-dialog v-model="showProductModal" persistent>
      <q-card class="w-full max-w-3xl">
        <q-card-section class="flex items-center justify-between">
          <div class="text-lg font-medium">{{ editMode ? 'Edit Product' : 'Add New Product' }}</div>
          <q-btn flat round icon="close" v-close-popup @click="resetForm" />
        </q-card-section>

        <q-separator />

        <q-card-section class="q-pt-none">
          <form @submit.prevent="saveProduct" class="space-y-6 py-4">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                <input 
                  v-model="productForm.name" 
                  type="text" 
                  required
                  class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price (USD) *</label>
                <input 
                  v-model="productForm.price" 
                  type="number" 
                  step="0.01" 
                  min="0" 
                  required
                  class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                <select 
                  v-model="productForm.category_id" 
                  required
                  class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                >
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity *</label>
                <input 
                  v-model="productForm.stock_quantity" 
                  type="number" 
                  min="0" 
                  required
                  class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                <input 
                  v-model="productForm.sku" 
                  type="text" 
                  class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select 
                  v-model="productForm.status" 
                  required
                  class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                >
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="out_of_stock">Out of Stock</option>
                </select>
              </div>
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
              <textarea 
                v-model="productForm.description" 
                rows="4" 
                required
                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
              ></textarea>
            </div>

            <!-- Product Images -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
              <div class="flex items-center gap-4">
                <div v-if="productForm.image" class="w-24 h-24 relative">
                  <img :src="productForm.image" alt="Product" class="w-full h-full object-cover rounded">
                  <button 
                    @click="productForm.image = ''" 
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                    type="button"
                  >
                    <i class="material-icons text-sm">close</i>
                  </button>
                </div>
                <div>
                  <input 
                    type="file" 
                    ref="imageInput" 
                    @change="handleImageUpload" 
                    accept="image/*" 
                    class="hidden"
                  >
                  <button 
                    type="button"
                    @click="$refs.imageInput.click()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
                  >
                    {{ productForm.image ? 'Change Image' : 'Upload Image' }}
                  </button>
                </div>
              </div>
            </div>

            <div v-if="formError" class="bg-red-100 text-red-700 p-3 rounded">
              {{ formError }}
            </div>
          </form>
        </q-card-section>

        <q-separator />

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey" v-close-popup @click="resetForm" />
          <q-btn flat label="Save" color="primary" @click="saveProduct" :loading="saving" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useQuasar } from 'quasar';
import { useAuthStore } from '../../../stores/auth';
import { useRouter } from 'vue-router';
import axios from 'axios';

const $q = useQuasar();
const authStore = useAuthStore();
const router = useRouter();

// Check if user is admin
if (!authStore.isAdmin) {
  // Redirect non-admin users
  router.push({ name: 'dashboard.index' });
}

// State
const loading = ref(false);
const error = ref(null);
const products = ref([]);
const categories = ref([]);
const totalProducts = ref(0);
const currentPage = ref(1);
const perPage = ref(12);

// Product modal state
const showProductModal = ref(false);
const editMode = ref(false);
const productForm = reactive({
  id: null,
  name: '',
  description: '',
  price: 0,
  category_id: null,
  status: 'active',
  stock_quantity: 0,
  sku: '',
  image: ''
});
const formError = ref('');
const saving = ref(false);

// Image input ref
const imageInput = ref(null);

// Filters
const filters = reactive({
  category: '',
  status: '',
  search: ''
});

// Load products from API
const loadProducts = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const params = {
      page: currentPage.value,
      per_page: perPage.value,
      category: filters.category,
      status: filters.status,
      search: filters.search
    };
    
    const response = await axios.get('/api/admin/products', { params });
    
    products.value = response.data.data;
    totalProducts.value = response.data.meta.total;
    
  } catch (err) {
    console.error('Error loading products:', err);
    error.value = 'Failed to load products. Please try again.';
  } finally {
    loading.value = false;
  }
};

// Load categories
const loadCategories = async () => {
  try {
    const response = await axios.get('/api/categories');
    categories.value = response.data;
  } catch (err) {
    console.error('Error loading categories:', err);
    $q.notify({
      message: 'Failed to load categories',
      color: 'negative',
      position: 'top',
      timeout: 2000
    });
  }
};

// Pagination methods
const nextPage = () => {
  if (currentPage.value * perPage.value < totalProducts.value) {
    currentPage.value++;
    loadProducts();
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    loadProducts();
  }
};

// Reset filters
const resetFilters = () => {
  filters.category = '';
  filters.status = '';
  filters.search = '';
  currentPage.value = 1;
  loadProducts();
};

// Format price to 2 decimal places
const formatPrice = (price) => {
  return (Math.round(price * 100) / 100).toFixed(2);
};

// Get status badge class
const getStatusBadgeClass = (status) => {
  const classes = {
    'active': 'bg-green-100 text-green-800',
    'inactive': 'bg-gray-100 text-gray-800',
    'out_of_stock': 'bg-red-100 text-red-800'
  };
  
  return classes[status] || 'bg-gray-100 text-gray-800';
};

// Open product modal
const openProductModal = (product = null) => {
  resetForm();
  
  if (product) {
    // Edit mode
    editMode.value = true;
    productForm.id = product.id;
    productForm.name = product.name;
    productForm.description = product.description;
    productForm.price = product.price;
    productForm.category_id = product.category_id;
    productForm.status = product.status;
    productForm.stock_quantity = product.stock_quantity;
    productForm.sku = product.sku || '';
    productForm.image = product.image || '';
  } else {
    // Add mode
    editMode.value = false;
  }
  
  showProductModal.value = true;
};

// Reset form
const resetForm = () => {
  productForm.id = null;
  productForm.name = '';
  productForm.description = '';
  productForm.price = 0;
  productForm.category_id = categories.value.length > 0 ? categories.value[0].id : null;
  productForm.status = 'active';
  productForm.stock_quantity = 0;
  productForm.sku = '';
  productForm.image = '';
  formError.value = '';
};

// Handle image upload
const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (!file) return;
  
  // Simple validation
  if (!file.type.match('image.*')) {
    formError.value = 'Please select an image file';
    return;
  }
  
  // For demo purposes, we'll just create a local object URL
  // In a real app, you'd upload this to your server or cloud storage
  productForm.image = URL.createObjectURL(file);
  
  // In a real application, you would upload the file to your server:
  /*
  const formData = new FormData();
  formData.append('image', file);
  
  try {
    const response = await axios.post('/api/admin/products/upload-image', formData);
    productForm.image = response.data.url;
  } catch (error) {
    console.error('Error uploading image:', error);
    formError.value = 'Failed to upload image. Please try again.';
  }
  */
};

// Save product
const saveProduct = async () => {
  formError.value = '';
  
  // Validate form
  if (!productForm.name || !productForm.description || productForm.price <= 0 || !productForm.category_id) {
    formError.value = 'Please fill in all required fields';
    return;
  }
  
  saving.value = true;
  
  try {
    let response;
    
    if (editMode.value) {
      // Update existing product
      response = await axios.put(`/api/admin/products/${productForm.id}`, productForm);
      
      // Update product in the list
      const index = products.value.findIndex(p => p.id === productForm.id);
      if (index !== -1) {
        products.value[index] = response.data;
      }
      
      $q.notify({
        message: 'Product updated successfully',
        color: 'positive',
        position: 'top',
        timeout: 2000
      });
    } else {
      // Create new product
      response = await axios.post('/api/admin/products', productForm);
      
      // Add to product list if on first page
      if (currentPage.value === 1 && products.value.length < perPage.value) {
        products.value.unshift(response.data);
        if (products.value.length > perPage.value) {
          products.value.pop();
        }
      }
      
      totalProducts.value++;
      
      $q.notify({
        message: 'Product created successfully',
        color: 'positive',
        position: 'top',
        timeout: 2000
      });
    }
    
    // Close modal and reset form
    showProductModal.value = false;
    resetForm();
    
  } catch (error) {
    console.error('Error saving product:', error);
    formError.value = error.response?.data?.message || 'Failed to save product. Please try again.';
  } finally {
    saving.value = false;
  }
};

// Toggle product status
const toggleProductStatus = async (product) => {
  const newStatus = product.status === 'active' ? 'inactive' : 'active';
  
  try {
    await axios.patch(`/api/admin/products/${product.id}/status`, { status: newStatus });
    
    // Update product in the list
    product.status = newStatus;
    
    $q.notify({
      message: `Product ${newStatus === 'active' ? 'activated' : 'deactivated'} successfully`,
      color: 'positive',
      position: 'top',
      timeout: 2000
    });
    
  } catch (error) {
    console.error('Error updating product status:', error);
    $q.notify({
      message: 'Failed to update product status',
      color: 'negative',
      position: 'top',
      timeout: 2000
    });
  }
};

// Load data on mount
onMounted(() => {
  loadProducts();
  loadCategories();
});
</script>

<style scoped>
.products-page {
  padding: 1.5rem;
}
</style>
