<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <form @submit.prevent="submitForm">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  {{ editMode ? 'Edit Bill' : 'Add New Bill' }}
                </h3>
                <div class="mt-4 space-y-4">
                  <!-- Description -->
                  <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <input type="text" id="description" v-model="form.description" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                      placeholder="Bill description">
                  </div>
                  
                  <!-- Amount -->
                  <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input type="number" id="amount" v-model="form.amount" step="0.01" min="0"
                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                        placeholder="0.00">
                    </div>
                  </div>
                  
                  <!-- Category -->
                  <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select id="category" v-model="form.category" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                      <option value="">Select a category</option>
                      <option v-for="(cat, key) in categories" :key="key" :value="key">
                        {{ cat.label }}
                      </option>
                    </select>
                  </div>
                  
                  <!-- Bill Date -->
                  <div>
                    <label for="bill_date" class="block text-sm font-medium text-gray-700">Bill Date</label>
                    <input type="date" id="bill_date" v-model="form.bill_date" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                  </div>
                  
                  <!-- Due Date -->
                  <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                    <input type="date" id="due_date" v-model="form.due_date" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                  </div>
                  
                  <!-- Status -->
                  <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" v-model="form.status" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                      <option value="pending">Pending</option>
                      <option value="paid">Paid</option>
                      <option value="overdue">Overdue</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="submit" 
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
              {{ editMode ? 'Update' : 'Save' }}
            </button>
            <button type="button" @click="$emit('close')" 
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, watch, onMounted } from 'vue';
import axios from 'axios';

export default {
  props: {
    show: {
      type: Boolean,
      default: false
    },
    bill: {
      type: Object,
      default: null
    }
  },
  emits: ['close', 'saved'],
  setup(props, { emit }) {
    const editMode = ref(false);
    const categories = ref({});
    const form = reactive({
      description: '',
      amount: '',
      bill_date: '',
      due_date: '',
      status: 'pending',
      category: ''
    });

    // Format date to YYYY-MM-DD for input fields
    const formatDateForInput = (dateString) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toISOString().split('T')[0];
    };

    // Load categories
    onMounted(async () => {
      try {
        const response = await axios.get('/api/bills/categories');
        categories.value = response.data;
      } catch (error) {
        console.error('Error loading categories:', error);
        // Fallback categories if API fails
        categories.value = {
          'rent': { label: 'Rent', icon: 'home' },
          'utilities': { label: 'Utilities', icon: 'bolt' },
          'water': { label: 'Water Bill', icon: 'water_drop' },
          'equipment': { label: 'Equipment', icon: 'fitness_center' },
          'maintenance': { label: 'Maintenance', icon: 'build' },
          'salary': { label: 'Salary', icon: 'payments' },
          'insurance': { label: 'Insurance', icon: 'shield' },
          'marketing': { label: 'Marketing', icon: 'campaign' },
          'taxes': { label: 'Taxes', icon: 'receipt_long' },
          'supplies': { label: 'Supplies', icon: 'inventory_2' },
          'software': { label: 'Software', icon: 'computer' },
          'other': { label: 'Other', icon: 'more_horiz' }
        };
      }
    });

    // Watch for changes in the bill prop to update the form
    watch(() => props.bill, (newBill) => {
      if (newBill) {
        editMode.value = true;
        form.description = newBill.description || '';
        form.amount = newBill.amount || '';
        form.bill_date = formatDateForInput(newBill.bill_date);
        form.due_date = formatDateForInput(newBill.due_date);
        form.status = newBill.status || 'pending';
        form.category = newBill.category || '';
      } else {
        editMode.value = false;
        resetForm();
      }
    }, { immediate: true });

    // Reset form to default values
    const resetForm = () => {
      form.description = '';
      form.amount = '';
      form.bill_date = '';
      form.due_date = '';
      form.status = 'pending';
      form.category = '';
    };

    // Submit the form
    const submitForm = async () => {
      try {
        let response;
        const formData = {
          description: form.description,
          amount: parseFloat(form.amount),
          bill_date: form.bill_date,
          due_date: form.due_date,
          status: form.status,
          category: form.category
        };

        if (editMode.value && props.bill) {
          // Update existing bill
          response = await axios.put(`/api/bills/${props.bill.id}`, formData);
        } else {
          // Create new bill
          response = await axios.post('/api/bills', formData);
        }

        emit('saved', response.data.bill);
        emit('close');
        resetForm();
      } catch (error) {
        console.error('Error saving bill:', error);
        alert('There was an error saving the bill. Please try again.');
      }
    };

    return {
      editMode,
      form,
      categories,
      submitForm
    };
  }
}
</script>
