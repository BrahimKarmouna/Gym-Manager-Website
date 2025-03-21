<template>
  <q-dialog v-model="showModal" persistent>
    <q-card class="rounded-lg expense-form-card" style="width: 500px; max-width: 90vw;">
      <q-card-section class="bg-gradient-to-r from-purple-600 to-pink-500 text-white">
        <div class="row items-center justify-between">
          <div>
            <div class="text-h6">{{ isEditing ? 'Edit Expense' : 'Add New Expense' }}</div>
            <div class="text-subtitle2">{{ isEditing ? 'Update expense details' : 'Create a new expense record' }}</div>
          </div>
          <q-btn flat round dense icon="close" @click="closeModal" />
        </div>
      </q-card-section>

      <q-card-section class="q-pt-md">
        <q-form @submit.prevent="handleSubmit" ref="expenseForm" class="q-gutter-md">
          <!-- Type Selection with custom design -->
          <div>
            <div class="text-subtitle2 q-mb-sm">Expense Type</div>
            <div class="row q-col-gutter-sm">
              <div class="col-6">
                <q-btn
                  :outline="form.type !== 'bill'"
                  :color="form.type === 'bill' ? 'deep-purple' : 'grey-7'"
                  class="full-width q-py-sm"
                  @click="form.type = 'bill'"
                >
                  <div class="row items-center">
                    <q-icon name="receipt" class="q-mr-sm" />
                    <div>Bill</div>
                  </div>
                </q-btn>
              </div>
              <div class="col-6">
                <q-btn
                  :outline="form.type !== 'enhancement'"
                  :color="form.type === 'enhancement' ? 'deep-purple' : 'grey-7'"
                  class="full-width q-py-sm"
                  @click="form.type = 'enhancement'"
                >
                  <div class="row items-center">
                    <q-icon name="upgrade" class="q-mr-sm" />
                    <div>Enhancement</div>
                  </div>
                </q-btn>
              </div>
            </div>
          </div>

          <!-- Description -->
          <q-input
            v-model="form.description"
            label="Description"
            filled
            :rules="[val => !!val || 'Description is required']"
            lazy-rules
            color="deep-purple"
            class="rounded-borders"
          >
            <template v-slot:prepend>
              <q-icon name="description" color="deep-purple" />
            </template>
          </q-input>
          
          <!-- Amount -->
          <q-input
            v-model.number="form.amount"
            label="Amount ($)"
            filled
            type="number"
            :rules="[
              val => !!val || 'Amount is required',
              val => val > 0 || 'Amount must be greater than 0'
            ]"
            lazy-rules
            color="deep-purple"
            class="rounded-borders"
          >
            <template v-slot:prepend>
              <q-icon name="attach_money" color="deep-purple" />
            </template>
            <template v-slot:after>
              <q-chip color="deep-purple" text-color="white" v-if="form.amount">
                {{ formatCurrency(form.amount) }}
              </q-chip>
            </template>
          </q-input>
          
          <!-- Category -->
          <q-select
            v-model="form.category"
            :options="Object.keys(filteredCategories).map(key => ({
              label: filteredCategories[key].label,
              value: key,
              icon: filteredCategories[key].icon
            }))"
            label="Category"
            filled
            :rules="[val => !!val || 'Category is required']"
            lazy-rules
            emit-value
            map-options
            color="deep-purple"
            class="rounded-borders"
          >
            <template v-slot:prepend>
              <q-icon name="category" color="deep-purple" />
            </template>
            <template v-slot:option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section avatar>
                  <q-icon :name="scope.opt.icon" color="deep-purple" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ scope.opt.label }}</q-item-label>
                </q-item-section>
              </q-item>
            </template>
            <template v-slot:selected>
              <div class="row items-center" v-if="form.category">
                <q-icon :name="getCategoryIcon(form.category)" color="deep-purple" class="q-mr-xs" />
                <div>{{ getCategoryLabel(form.category) }}</div>
              </div>
            </template>
          </q-select>
          
          <!-- Bill Date with Quasar date picker -->
          <q-input
            v-model="form.bill_date"
            label="Bill Date"
            filled
            :rules="[val => !!val || 'Bill date is required']"
            lazy-rules
            color="deep-purple"
            class="rounded-borders"
          >
            <template v-slot:prepend>
              <q-icon name="event" color="deep-purple" />
            </template>
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                  <q-date 
                    v-model="form.bill_date" 
                    mask="YYYY-MM-DD"
                    color="deep-purple"
                    today-btn
                  >
                    <div class="row items-center justify-end q-gutter-sm">
                      <q-btn label="Cancel" color="grey-7" flat v-close-popup />
                      <q-btn label="OK" color="deep-purple" flat v-close-popup />
                    </div>
                  </q-date>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          
          <!-- Due Date with Quasar date picker (only for bills) -->
          <q-input
            v-if="form.type === 'bill'"
            v-model="form.due_date"
            label="Due Date"
            filled
            hint="Leave empty if no due date"
            color="deep-purple"
            class="rounded-borders"
          >
            <template v-slot:prepend>
              <q-icon name="event" color="deep-purple" />
            </template>
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                  <q-date 
                    v-model="form.due_date" 
                    mask="YYYY-MM-DD"
                    color="deep-purple"
                    today-btn
                  >
                    <div class="row items-center justify-end q-gutter-sm">
                      <q-btn label="Cancel" color="grey-7" flat v-close-popup />
                      <q-btn label="OK" color="deep-purple" flat v-close-popup />
                    </div>
                  </q-date>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          
          <!-- Status (only for bills) -->
          <div v-if="form.type === 'bill'" class="q-mt-md">
            <div class="text-subtitle2 q-mb-sm">Status</div>
            <div class="row q-col-gutter-sm">
              <div class="col-4">
                <q-btn
                  :outline="form.status !== 'paid'"
                  color="green"
                  class="full-width q-py-sm"
                  @click="form.status = 'paid'"
                >
                  <div class="row items-center">
                    <q-icon name="check_circle" class="q-mr-xs" />
                    <div>Paid</div>
                  </div>
                </q-btn>
              </div>
              <div class="col-4">
                <q-btn
                  :outline="form.status !== 'pending'"
                  color="amber-8"
                  class="full-width q-py-sm"
                  @click="form.status = 'pending'"
                >
                  <div class="row items-center">
                    <q-icon name="schedule" class="q-mr-xs" />
                    <div>Pending</div>
                  </div>
                </q-btn>
              </div>
              <div class="col-4">
                <q-btn
                  :outline="form.status !== 'overdue'"
                  color="red"
                  class="full-width q-py-sm"
                  @click="form.status = 'overdue'"
                >
                  <div class="row items-center">
                    <q-icon name="error" class="q-mr-xs" />
                    <div>Overdue</div>
                  </div>
                </q-btn>
              </div>
            </div>
          </div>
        </q-form>
      </q-card-section>

      <q-card-actions align="right" class="bg-grey-1 q-pa-md">
        <q-btn flat label="Cancel" color="grey-7" v-close-popup @click="closeModal" />
        <q-btn unelevated label="Save" color="deep-purple" @click="handleSubmit" :loading="loading">
          <q-icon name="save" class="q-ml-sm" />
        </q-btn>
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import { useQuasar } from 'quasar';

export default {
  props: {
    showModal: {
      type: Boolean,
      default: false
    },
    expense: {
      type: Object,
      default: null
    },
    type: {
      type: String,
      default: 'all'
    }
  },
  
  emits: ['close', 'saved'],
  
  setup(props, { emit }) {
    const $q = useQuasar();
    const expenseForm = ref(null);
    const isEditing = computed(() => props.expense !== null);
    const loading = ref(false);
    const showModal = ref(false);
    
    // Define categories
    const categories = ref({
      rent: {
        label: 'Rent',
        icon: 'home',
        type: ['bill']
      },
      utilities: {
        label: 'Utilities',
        icon: 'bolt',
        type: ['bill']
      },
      water: {
        label: 'Water Bill',
        icon: 'water_drop',
        type: ['bill']
      },
      equipment: {
        label: 'Equipment',
        icon: 'fitness_center',
        type: ['bill', 'enhancement']
      },
      maintenance: {
        label: 'Maintenance',
        icon: 'build',
        type: ['bill']
      },
      salary: {
        label: 'Salary',
        icon: 'payments',
        type: ['bill']
      },
      insurance: {
        label: 'Insurance',
        icon: 'shield',
        type: ['bill']
      },
      marketing: {
        label: 'Marketing',
        icon: 'campaign',
        type: ['bill']
      },
      renovation: {
        label: 'Renovation',
        icon: 'construction',
        type: ['enhancement']
      },
      decor: {
        label: 'Decor',
        icon: 'format_paint',
        type: ['enhancement']
      },
      technology: {
        label: 'Technology',
        icon: 'computer',
        type: ['bill', 'enhancement']
      },
      other: {
        label: 'Other',
        icon: 'more_horiz',
        type: ['bill', 'enhancement']
      }
    });
    
    // Filter categories based on selected type
    const filteredCategories = computed(() => {
      const result = {};
      for (const [key, category] of Object.entries(categories.value)) {
        if (category.type.includes(form.value.type)) {
          result[key] = category;
        }
      }
      return result;
    });
    
    // Initialize form data
    const form = ref({
      type: 'bill',
      description: '',
      amount: '',
      category: '',
      bill_date: '',
      due_date: '',
      status: 'pending'
    });
    
    // Watch for changes in the expense prop
    watch(() => props.expense, (newExpense) => {
      if (newExpense) {
        form.value = {
          type: newExpense.type || 'bill',
          description: newExpense.description || '',
          amount: newExpense.amount || '',
          category: newExpense.category || '',
          bill_date: newExpense.bill_date ? new Date(newExpense.bill_date).toISOString().split('T')[0] : '',
          due_date: newExpense.due_date ? new Date(newExpense.due_date).toISOString().split('T')[0] : '',
          status: newExpense.status || 'pending'
        };
      } else {
        resetForm();
      }
    }, { immediate: true });
    
    // Watch for changes in the show prop
    watch(() => props.showModal, (newShow) => {
      showModal.value = newShow;
      if (newShow && props.type !== 'all') {
        form.value.type = props.type;
      }
    }, { immediate: true });
    
    // Watch for changes in showModal
    watch(() => showModal.value, (newShow) => {
      if (!newShow) {
        emit('close');
      }
    });
    
    // Watch for changes in type to reset category if not valid for the new type
    watch(() => form.value.type, (newType) => {
      if (!Object.keys(filteredCategories.value).includes(form.value.category)) {
        form.value.category = '';
      }
      
      // For enhancements, status is always 'paid'
      if (newType === 'enhancement') {
        form.value.status = 'paid';
      }
    });
    
    // Reset form to default values
    const resetForm = () => {
      form.value = {
        type: props.type !== 'all' ? props.type : 'bill',
        description: '',
        amount: '',
        category: '',
        bill_date: new Date().toISOString().split('T')[0],
        due_date: '',
        status: 'pending'
      };
    };
    
    // Handle form submission
    const handleSubmit = async () => {
      // Validate form
      const isValid = await expenseForm.value.validate();
      if (!isValid) {
        $q.notify({
          color: 'negative',
          textColor: 'white',
          icon: 'warning',
          message: 'Please fill in all required fields correctly'
        });
        return;
      }
      
      try {
        loading.value = true;
        
        // Prepare form data
        const formData = {
          ...form.value,
          amount: parseFloat(form.value.amount)
        };
        
        // For enhancements, status is always 'paid'
        if (formData.type === 'enhancement') {
          formData.status = 'paid';
        }
        
        // Set endpoint and method based on whether we're editing or creating
        const endpoint = isEditing.value 
          ? `/api/expenses/${props.expense.id}` 
          : '/api/expenses';
        
        const method = isEditing.value ? 'put' : 'post';
        
        // Send request
        const response = await axios[method](endpoint, formData);
        
        // Show success notification
        $q.notify({
          color: 'positive',
          textColor: 'white',
          icon: 'check_circle',
          message: isEditing.value ? 'Expense updated successfully' : 'Expense added successfully'
        });
        
        // Emit saved event with the updated/created expense
        emit('saved', response.data.expense || response.data);
        closeModal();
      } catch (error) {
        console.error('Error saving expense:', error);
        
        // Handle error message
        let errorMessage = 'An error occurred while saving the expense';
        if (error.response && error.response.data) {
          if (error.response.data.message) {
            errorMessage = error.response.data.message;
          } else if (error.response.data.errors) {
            const errors = error.response.data.errors;
            errorMessage = Object.values(errors).flat().join(', ');
          }
        }
        
        // Show error notification
        $q.notify({
          color: 'negative',
          textColor: 'white',
          icon: 'error',
          message: errorMessage
        });
      } finally {
        loading.value = false;
      }
    };
    
    // Close the modal
    const closeModal = () => {
      showModal.value = false;
    };
    
    // Initialize with today's date
    onMounted(() => {
      if (!form.value.bill_date) {
        form.value.bill_date = new Date().toISOString().split('T')[0];
      }
    });
    
    // Format currency
    const formatCurrency = (amount) => {
      return `$${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };
    
    // Get category icon
    const getCategoryIcon = (category) => {
      return categories.value[category].icon;
    };
    
    // Get category label
    const getCategoryLabel = (category) => {
      return categories.value[category].label;
    };
    
    return {
      expenseForm,
      isEditing,
      form,
      filteredCategories,
      showModal,
      loading,
      handleSubmit,
      closeModal,
      formatCurrency,
      getCategoryIcon,
      getCategoryLabel
    };
  }
};
</script>
