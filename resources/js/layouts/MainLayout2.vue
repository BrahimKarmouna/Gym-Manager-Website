<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
      <div class="p-6">
        <h2 class="text-2xl font-bold">Dashboard</h2>
      </div>
      <nav>
        <ul>
          <li class="py-2 px-4 hover:bg-gray-200">
            <router-link to="/"
                         class="text-gray-800">Home</router-link>
          </li>
          <li class="py-2 px-4 hover:bg-gray-200">
            <router-link to="/transaction-categories"
                         class="text-gray-800">Transaction Categories</router-link>
          </li>
          <!-- Other links... -->
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
      <header class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Transaction Categories</h1>
        <q-btn @click="openCreateModal"
               color="primary"
               label="Create Category" />
      </header>

      <!-- Transaction Categories List -->
      <ul>
        <li v-for="category in categories"
            :key="category.id"
            class="flex justify-between items-center mb-4">
          <span>{{ category.name }}</span>
          <div>
            <q-btn @click="openEditModal(category)"
                   color="blue"
                   label="Edit" />
            <q-btn @click="deleteCategory(category)"
                   color="red"
                   label="Delete" />
          </div>
        </li>
      </ul>

      <!-- Create/Edit Modal -->
      <q-dialog v-model="isModalOpen">
        <q-card>
          <q-card-section>
            <div class="text-h6">{{ isEditing ? 'Edit Category' : 'Create Category' }}</div>
            <q-input v-model="form.name"
                     label="Category Name"
                     outlined />
          </q-card-section>
          <q-card-actions>
            <q-btn @click="submitForm"
                   color="primary"
                   label="Save" />
            <q-btn @click="isModalOpen = false"
                   label="Cancel" />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { api } from '@/boot/axios';

const categories = ref([]);
const isModalOpen = ref(false);
const isEditing = ref(false);
const form = ref({ name: '' });

// Fetch categories from the API
const fetchCategories = async () => {
  try {
    const response = await api.get('transaction-categories');
    categories.value = response.data.data; // Adjust to match your resource structure
    console.log('Fetched categories:', response.data.data); // Log fetched data for debugging
  } catch (error) {
    console.error("Error fetching categories:", error);
  }
};

// Open modal for creating a new category
const openCreateModal = () => {
  form.value = { name: '' };
  isEditing.value = false;
  isModalOpen.value = true;
};

// Open modal for editing an existing category
const openEditModal = (category) => {
  form.value = { ...category };
  isEditing.value = true;
  isModalOpen.value = true;
};

// Submit the form to create or update a category
const submitForm = async () => {
  try {
    if (isEditing.value) {
      await api.put(`transaction-categories/${form.value.id}`, { name: form.value.name });
    } else {
      await api.post('transaction-categories', { name: form.value.name });
    }
    isModalOpen.value = false;
    await fetchCategories(); // Re-fetch categories after creating/updating
  } catch (error) {
    console.error("Error saving category:", error);
  }
};

// Delete a category
const deleteCategory = async (category) => {
  if (confirm(`Are you sure you want to delete ${category.name}?`)) {
    try {
      await api.delete(`transaction-categories/${category.id}`);
      await fetchCategories(); // Re-fetch categories after deletion
    } catch (error) {
      console.error("Error deleting category:", error);
    }
  }
};

// Fetch categories when the component is mounted
onMounted(fetchCategories);
</script>

<style>
/* Add styles as needed */
</style>
