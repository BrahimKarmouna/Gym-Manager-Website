<template>
  <q-page class="bg-gray-50">
    <!-- Enhanced Hero Banner Section -->
    <div class="relative bg-gradient-to-r from-blue-700 to-indigo-800 h-72">
      <div class="absolute inset-0 bg-pattern opacity-10"></div>
      <div class="max-w-7xl mx-auto px-4 py-16 relative">
        <div class="text-white">
          <h1 class="text-4xl font-bold mb-2">Products Management</h1>
          <p class="text-blue-100">Manage your gym's inventory with ease</p>
          <div class="mt-6 flex gap-4">
            <q-btn
              @click="openAddProductModal"
              color="white"
              text-color="blue-600"
              icon="add"
              label="Add New Product"
              class="font-medium"
            />
            <q-btn
              outline
              color="white"
              icon="download"
              label="Export Inventory"
              class="font-medium"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 -mt-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <q-card class="bg-white shadow-lg">
          <q-card-section>
            <div class="flex items-center">
              <q-icon name="inventory" size="2.5rem" color="primary" />
              <div class="ml-4">
                <div class="text-sm text-gray-500">Total Products</div>
                <div class="text-2xl font-bold">{{ products.length }}</div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <q-card class="bg-white shadow-lg">
          <q-card-section>
            <div class="flex items-center">
              <q-icon name="warning" size="2.5rem" color="orange" />
              <div class="ml-4">
                <div class="text-sm text-gray-500">Low Stock Items</div>
                <div class="text-2xl font-bold">
                  {{ products.filter(p => p.stock < 10).length }}
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <q-card class="bg-white shadow-lg">
          <q-card-section>
            <div class="flex items-center">
              <q-icon name="trending_up" size="2.5rem" color="positive" />
              <div class="ml-4">
                <div class="text-sm text-gray-500">Total Value</div>
                <div class="text-2xl font-bold">
                  ${{ calculateTotalValue() }}
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <q-card class="bg-white shadow-lg">
          <q-card-section>
            <div class="flex items-center">
              <q-icon name="category" size="2.5rem" color="purple" />
              <div class="ml-4">
                <div class="text-sm text-gray-500">Categories</div>
                <div class="text-2xl font-bold">
                  {{ categoryOptions.length - 1 }}
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Enhanced Filters Section -->
    <div class="max-w-7xl mx-auto px-4 py-8">
      <q-card class="bg-white shadow-lg">
        <q-card-section>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <q-input
              v-model="searchQuery"
              dense
              filled
              placeholder="Search products..."
              class="col-span-1"
            >
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
            
            <q-select
              v-model="categoryFilter"
              :options="categoryOptions"
              filled
              dense
              label="Category"
            />

            <q-select
              v-model="sortBy"
              :options="sortOptions"
              filled
              dense
              label="Sort By"
            />

            <q-select
              v-model="stockFilter"
              :options="[
                { label: 'All Stock Levels', value: null },
                { label: 'Low Stock (< 10)', value: 'low' },
                { label: 'In Stock', value: 'in' },
                { label: 'Out of Stock', value: 'out' }
              ]"
              filled
              dense
              label="Stock Level"
            />
          </div>
        </q-card-section>
      </q-card>

      <!-- Enhanced Products Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-8">
        <q-card
          v-for="product in filteredProducts"
          :key="product.id"
          class="transform hover:scale-105 transition-transform duration-300"
        >
          <q-img
            :src="product.image"
            :ratio="1"
            class="h-48 object-cover"
          >
            <div class="absolute-top-right q-pa-sm">
              <q-badge
                :color="product.stock > 10 ? 'green' : 'red'"
                :label="product.stock > 0 ? 'In Stock' : 'Out of Stock'"
                class="text-bold"
              />
            </div>
          </q-img>

          <q-card-section>
            <div class="text-lg font-semibold line-clamp-2">{{ product.name }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ product.category }}</div>
            <div class="flex justify-between items-center mt-3">
              <div class="text-xl font-bold text-blue-600">${{ product.price }}</div>
              <div class="text-sm text-gray-500">Stock: {{ product.stock }}</div>
            </div>
          </q-card-section>

          <q-separator />

          <q-card-actions align="around">
            <q-btn flat color="primary" icon="edit" label="Edit" @click="editProduct(product)" />
            <q-btn flat color="negative" icon="delete" label="Delete" @click="confirmDelete(product)" />
          </q-card-actions>
        </q-card>
      </div>

      <!-- Enhanced Empty State -->
      <div
        v-if="filteredProducts.length === 0"
        class="text-center py-16 bg-white rounded-lg shadow-lg mt-8"
      >
        <q-icon name="inventory_2" size="6rem" color="grey-4" />
        <h3 class="text-xl font-semibold text-gray-700 mt-4">No Products Found</h3>
        <p class="text-gray-500 mt-2">Try adjusting your filters or add new products</p>
        <q-btn
          color="primary"
          label="Add Your First Product"
          class="mt-4"
          @click="openAddProductModal"
        />
      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      searchQuery: '',
      categoryFilter: null,
      sortBy: null,
      deleteDialog: false,
      selectedProduct: null,
      categoryOptions: [
        { label: 'All Categories', value: null },
        { label: 'Supplements', value: 'supplements' },
        { label: 'Equipment', value: 'equipment' },
        { label: 'Clothing', value: 'clothing' }
      ],
      sortOptions: [
        { label: 'Name: A-Z', value: 'name-asc' },
        { label: 'Name: Z-A', value: 'name-desc' },
        { label: 'Price: Low to High', value: 'price-asc' },
        { label: 'Price: High to Low', value: 'price-desc' },
        { label: 'Stock: Low to High', value: 'stock-asc' },
        { label: 'Stock: High to Low', value: 'stock-desc' }
      ],
      products: [
        {
          id: 1,
          name: 'Premium Whey Protein',
          price: 29.99,
          stock: 50,
          category: 'supplements',
          image: '/images/protein.jpg'
        },
        // Add more products...
      ]
    }
  },
  computed: {
    filteredProducts() {
      let filtered = [...this.products]
      
      // Apply search filter
      if (this.searchQuery) {
        filtered = filtered.filter(product =>
          product.name.toLowerCase().includes(this.searchQuery.toLowerCase())
        )
      }

      // Apply category filter
      if (this.categoryFilter) {
        filtered = filtered.filter(product => product.category === this.categoryFilter)
      }

      // Apply sorting
      if (this.sortBy) {
        filtered.sort((a, b) => {
          switch (this.sortBy) {
            case 'name-asc':
              return a.name.localeCompare(b.name)
            case 'name-desc':
              return b.name.localeCompare(a.name)
            case 'price-asc':
              return a.price - b.price
            case 'price-desc':
              return b.price - a.price
            case 'stock-asc':
              return a.stock - b.stock
            case 'stock-desc':
              return b.stock - a.stock
            default:
              return 0
          }
        })
      }

      return filtered
    }
  },
  methods: {
    openAddProductModal() {
      // Implement add product modal logic
    },
    editProduct(product) {
      // Implement edit product logic
    },
    confirmDelete(product) {
      this.selectedProduct = product
      this.deleteDialog = true
    },
    deleteProduct() {
      // Implement delete product logic
      if (this.selectedProduct) {
        // Delete logic here
        this.selectedProduct = null
      }
    }
  }
}
</script>    editProduct(product) {
      // Implement edit product logic
      console.log('Editing product:', product);
    },
