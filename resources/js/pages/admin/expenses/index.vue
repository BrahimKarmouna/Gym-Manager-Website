<template>
  <div class="bg-gray-50 min-h-screen p-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Expenses Management</h1>
      <div class="flex space-x-3">
        <q-btn-group outline>
          <q-btn 
            :color="viewMode === 'list' ? 'primary' : 'grey'" 
            :text-color="viewMode === 'list' ? 'white' : 'black'"
            icon="list" 
            label="List" 
            @click="viewMode = 'list'"
          />
          <q-btn 
            :color="viewMode === 'calendar' ? 'primary' : 'grey'" 
            :text-color="viewMode === 'calendar' ? 'white' : 'black'"
            icon="calendar_today" 
            label="Calendar" 
            @click="viewMode = 'calendar'; generateCalendarDays()"
          />
        </q-btn-group>
        <q-btn 
          color="primary" 
          icon="add" 
          label="Add Expense" 
          @click="showAddModal = true; selectedExpense = null"
        />
      </div>
    </div>
    <!-- Tab Navigation -->
    <div class="mb-6">
      <q-tabs
        v-model="activeTab"
        class="text-primary"
        active-color="primary"
        indicator-color="primary"
        align="left"
        narrow-indicator
      >
        <q-tab name="bill" icon="receipt" label="Bills" @click="changeTab('bill')" />
        <q-tab name="enhancement" icon="construction" label="Gym Enhancements" @click="changeTab('enhancement')" />
      </q-tabs>
    </div>

    <!-- Loading Indicator -->
    <div v-if="isLoading" class="flex justify-center items-center h-64">
      <q-spinner-dots color="primary" size="3em" />
    </div>

    <template v-else>
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <q-card flat bordered class="bg-white">
          <q-card-section>
            <div class="flex items-center">
              <q-avatar color="indigo-1" text-color="indigo" icon="payments" size="3.5rem" class="q-mr-md" />
              <div>
                <div class="text-sm text-gray-500">Total This Month</div>
                <div class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.total_expenses_this_month) }}</div>
              </div>
            </div>
          </q-card-section>
        </q-card>
        
        <q-card flat bordered class="bg-white">
          <q-card-section>
            <div class="flex items-center">
              <q-avatar color="green-1" text-color="green" icon="check_circle" size="3.5rem" class="q-mr-md" />
              <div>
                <div class="text-sm text-gray-500">Paid</div>
                <div class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.paid_expenses) }}</div>
              </div>
            </div>
          </q-card-section>
        </q-card>
        
        <q-card v-if="activeTab === 'bill'" flat bordered class="bg-white">
          <q-card-section>
            <div class="flex items-center">
              <q-avatar color="yellow-1" text-color="yellow" icon="pending" size="3.5rem" class="q-mr-md" />
              <div>
                <div class="text-sm text-gray-500">Pending</div>
                <div class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.pending_expenses) }}</div>
              </div>
            </div>
          </q-card-section>
        </q-card>
        
        <q-card v-if="activeTab === 'bill'" flat bordered class="bg-white">
          <q-card-section>
            <div class="flex items-center">
              <q-avatar color="red-1" text-color="red" icon="warning" size="3.5rem" class="q-mr-md" />
              <div>
                <div class="text-sm text-gray-500">Overdue</div>
                <div class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.overdue_expenses) }}</div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 md:col-span-2">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Trend</h3>
          <div class="h-64">
            <canvas ref="monthlyChart"></canvas>
          </div>
        </div>
        
        <div v-if="stats.category_breakdown.length > 0" class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Category Breakdown</h3>
          <div class="h-64">
            <canvas ref="categoryChart"></canvas>
          </div>
        </div>
      </div>

      <!-- View Modes -->
      <div v-if="viewMode === 'list'">
        <!-- Filter Section -->
        <q-card flat bordered class="q-mb-md">
          <q-card-section>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-3">
                <q-select
                  v-model="filters.category"
                  :options="Object.keys(categoryOptions).map(key => ({
                    label: categoryOptions[key].label,
                    value: key,
                    icon: categoryOptions[key].icon
                  }))"
                  label="Category"
                  clearable
                  emit-value
                  map-options
                  filled
                >
                  <template v-slot:prepend>
                    <q-icon name="category" />
                  </template>
                  <template v-slot:option="scope">
                    <q-item v-bind="scope.itemProps">
                      <q-item-section avatar>
                        <q-icon :name="scope.opt.icon" />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>{{ scope.opt.label }}</q-item-label>
                      </q-item-section>
                    </q-item>
                  </template>
                </q-select>
              </div>
              
              <div v-if="activeTab === 'bill'" class="col-12 col-md-3">
  <q-select
    v-model="filters.status"
    :options="[
      { label: 'All Statuses', value: '' },
      { label: 'Paid', value: 'paid', color: 'green' },
      { label: 'Pending', value: 'pending', color: 'orange' },
      { label: 'Overdue', value: 'overdue', color: 'red' }
    ]"
    label="Status"
    emit-value
    map-options
    filled
  >
    <template v-slot:prepend>
      <q-icon name="layers" />
    </template>
    <template v-slot:option="scope">
      <q-item v-bind="scope.itemProps">
        <q-item-section>
          <q-item-label>
            <q-badge v-if="scope.opt.value" :color="scope.opt.color" text-color="white" class="q-mr-sm">
              {{ scope.opt.value }}
            </q-badge>
            {{ scope.opt.label }}
          </q-item-label>
        </q-item-section>
      </q-item>
    </template>
  </q-select>
</div>

<div class="col-12 col-md-3">
  <q-field
    label="From Date"
    filled
    stack-label
  >
    <template v-slot:control>
      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
        <q-date 
          v-model="filters.fromDate" 
          mask="YYYY-MM-DD"
          color="deep-purple"
          today-btn
          flat
          square
          minimal
        >
          <div class="row items-center justify-end q-gutter-sm q-pa-sm">
            <q-btn label="Cancel" color="grey-7" flat v-close-popup />
            <q-btn label="Apply" color="deep-purple-5" flat v-close-popup />
          </div>
        </q-date>
      </q-popup-proxy>
      <div class="self-center full-width no-outline cursor-pointer" tabindex="0">
        {{ filters.fromDate ? formatDateDisplay(filters.fromDate) : 'Select date' }}
      </div>
    </template>
    <template v-slot:prepend>
      <q-icon name="event" color="deep-purple-5" />
    </template>
    <template v-slot:append>
      <q-icon 
        name="close" 
        @click.stop="filters.fromDate = ''" 
        class="cursor-pointer" 
        v-if="filters.fromDate"
        color="deep-purple-5"
      />
    </template>
  </q-field>
</div>

<div class="col-12 col-md-3">
  <q-field label="To Date" filled stack-label>
    <template v-slot:control>
      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
        <q-date 
          v-model="filters.toDate"
          mask="YYYY-MM-DD"
          color="deep-purple"
          today-btn
          flat
          bordered
          minimal
        >
          <div class="row items-center justify-end q-gutter-sm q-pa-sm">
            <q-btn label="Cancel" color="grey-7" flat v-close-popup />
            <q-btn label="Apply" color="deep-purple-5" flat v-close-popup />
          </div>
        </q-date>
      </q-popup-proxy>
      <div class="self-center full-width no-outline cursor-pointer" tabindex="0">
        {{ filters.toDate ? formatDateDisplay(filters.toDate) : 'Select date' }}
      </div>
    </template>
    <template v-slot:prepend>
      <q-icon name="event" color="deep-purple-5" />
    </template>
    <template v-slot:append>
      <q-icon
        name="close"
        class="cursor-pointer"
        color="deep-purple-5"
        @click="filters.toDate = ''"
      />
    </template>
  </q-field>
</div>


<div class="row q-col-gutter-md q-mt-md">
  <div class="col-12 col-md-6">
    <q-input
      v-model="filters.search"
      label="Search expenses"
      filled
      clearable
    >
      <template v-slot:prepend>
        <q-icon name="search" />
      </template>
    </q-input>
  </div>
  
  <div class="col-12 col-md-6 flex items-center justify-end">
    <q-btn
      label="Reset Filters"
      color="grey-7"
      flat
      @click="resetFilters"
      class="q-ml-sm"
      icon="restart_alt"
    />
  </div>
</div>
</div>

          </q-card-section>
        </q-card>
        
        <!-- Expenses Table -->
        <q-card v-if="filteredExpenses.length > 0" flat bordered>
          <q-table
            :rows="filteredExpenses"
            :columns="[
              { name: 'date', label: 'Date', field: 'date', sortable: true, align: 'left' },
              { name: 'description', label: 'Description', field: 'description', sortable: true, align: 'left' },
              { name: 'category', label: 'Category', field: 'category', sortable: true, align: 'left' },
              { name: 'amount', label: 'Amount', field: 'amount', sortable: true, align: 'right' },
              { name: 'status', label: 'Status', field: 'status', sortable: true, align: 'left' },
              { name: 'actions', label: 'Actions', field: 'actions', align: 'center' }
            ]"
            row-key="id"
            flat
            bordered
            separator="horizontal"
            :pagination="{ rowsPerPage: 10 }"
          >
            <template v-slot:body="props">
              <q-tr :props="props">
                <q-td key="date" :props="props">
                  {{ new Date(props.row.date).toLocaleDateString() }}
                </q-td>
                <q-td key="description" :props="props">
                  {{ props.row.description }}
                </q-td>
                <q-td key="category" :props="props">
                  <div class="flex items-center">
                    <q-icon 
                      :name="categoryOptions[props.row.category]?.icon || 'help'" 
                      :color="categoryOptions[props.row.category]?.color || 'grey'" 
                      size="sm"
                      class="q-mr-xs"
                    />
                    {{ categoryOptions[props.row.category]?.label || props.row.category }}
                  </div>
                </q-td>
                <q-td key="amount" :props="props" class="text-right">
                  {{ formatCurrency(props.row.amount) }}
                </q-td>
                <q-td key="status" :props="props">
                  <q-badge
                    v-if="props.row.status"
                    :color="
                      props.row.status === 'paid' ? 'green' :
                      props.row.status === 'pending' ? 'orange' :
                      props.row.status === 'overdue' ? 'red' : 'grey'
                    "
                    text-color="white"
                    class="text-capitalize"
                  >
                    {{ props.row.status }}
                  </q-badge>
                </q-td>
                <q-td key="actions" :props="props" class="text-center">
                  <q-btn flat round color="primary" icon="edit" @click="editExpense(props.row)" size="sm">
                    <q-tooltip>Edit</q-tooltip>
                  </q-btn>
                  <q-btn flat round color="negative" icon="delete" @click="showDeleteDialog = true; selectedExpense = props.row" size="sm">
                    <q-tooltip>Delete</q-tooltip>
                  </q-btn>
                </q-td>
              </q-tr>
            </template>
            <template v-slot:no-data>
              <div class="full-width row flex-center q-pa-md text-grey-8">
                No expenses found matching your filters
              </div>
            </template>
          </q-table>
        </q-card>
        
        <q-card v-else class="text-center q-pa-lg">
          <q-icon name="info" size="3rem" color="grey-6" />
          <div class="text-h6 q-mt-md text-grey-8">No expenses found</div>
          <div class="text-grey-6 q-mb-md">Try adjusting your filters or add a new expense</div>
          <q-btn color="primary" icon="add" label="Add Expense" @click="showAddModal = true; selectedExpense = null" />
        </q-card>
      </div>
      
      <div v-else-if="viewMode === 'calendar'">
        <q-card flat bordered>
          <q-card-section class="bg-grey-2">
            <div class="row justify-between items-center">
              <q-btn flat round icon="chevron_left" @click="changeMonth(-1)" />
              <div class="text-h6">
                {{ new Date(calendarYear, calendarMonth).toLocaleString('default', { month: 'long', year: 'numeric' }) }}
              </div>
              <q-btn flat round icon="chevron_right" @click="changeMonth(1)" />
            </div>
          </q-card-section>
          
          <q-card-section>
            <div class="row q-col-gutter-sm">
              <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day" class="col text-center text-weight-medium text-grey-8 q-py-sm">
                {{ day }}
              </div>
            </div>
            
            <div class="row q-col-gutter-sm">
              <div 
                v-for="(day, index) in calendarDays" 
                :key="index" 
                class="col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl"
                style="min-height: 100px"
              >
                <q-card 
                  v-if="day.day" 
                  flat 
                  bordered 
                  class="full-height"
                  :class="{ 
                    'bg-indigo-1': day.expenses.length > 0,
                    'bg-grey-2': isToday(calendarYear, calendarMonth, day.day),
                    'cursor-pointer': day.expenses.length > 0
                  }"
                  @click="day.expenses.length > 0 ? showDayDetails(day) : null"
                >
                  <q-card-section class="q-py-xs">
                    <div class="row justify-between items-center">
                      <div 
                        class="text-weight-medium" 
                        :class="{ 
                          'text-indigo-9': day.expenses.length > 0,
                          'text-primary': isToday(calendarYear, calendarMonth, day.day) 
                        }"
                      >
                        {{ day.day }}
                      </div>
                      <q-badge v-if="day.expenses.length > 0" color="primary" rounded>
                        {{ formatCurrency(day.total) }}
                      </q-badge>
                    </div>
                  </q-card-section>
                  
                  <q-card-section v-if="day.expenses.length > 0" class="q-py-xs">
                    <q-list dense padding>
                      <q-item 
                        v-for="expense in day.expenses.slice(0, 3)" 
                        :key="expense.id" 
                        clickable
                        dense
                        class="q-py-xs text-caption"
                        @click.stop="editExpense(expense)"
                      >
                        <q-item-section>
                          <q-item-label lines="1">{{ expense.description }}</q-item-label>
                        </q-item-section>
                        <q-item-section side>
                          <q-badge 
                            :color="
                              expense.status === 'paid' ? 'green' :
                              expense.status === 'pending' ? 'orange' :
                              expense.status === 'overdue' ? 'red' : 'grey'
                            "
                            text-color="white"
                            class="text-caption"
                          >
                            {{ formatCurrency(expense.amount) }}
                          </q-badge>
                        </q-item-section>
                      </q-item>
                      <q-item v-if="day.expenses.length > 3" dense class="text-caption text-primary text-weight-medium">
                        <q-item-section>
                          + {{ day.expenses.length - 3 }} more
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-card-section>
                </q-card>
              </div>
            </div>
          </q-card-section>
        </q-card>
        
        <!-- Day Details Dialog -->
        <q-dialog v-model="showDayDetailsModal" persistent>
          <q-card style="min-width: 350px">
            <q-card-section class="bg-primary text-white">
              <div class="text-h6">Expenses for {{ selectedDay ? formatDate(new Date(calendarYear, calendarMonth, selectedDay.day)) : '' }}</div>
            </q-card-section>
            
            <q-card-section class="q-pt-none scroll" style="max-height: 50vh">
              <q-list separator>
                <q-item v-for="expense in selectedDay?.expenses || []" :key="expense.id">
                  <q-item-section>
                    <q-item-label>{{ expense.description }}</q-item-label>
                    <q-item-label caption>
                      <q-icon 
                        :name="categoryOptions[expense.category]?.icon || 'help'" 
                        :color="categoryOptions[expense.category]?.color || 'grey'" 
                        size="xs"
                        class="q-mr-xs"
                      />
                      {{ categoryOptions[expense.category]?.label || expense.category }}
                    </q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <div class="text-weight-medium">{{ formatCurrency(expense.amount) }}</div>
                    <q-badge 
                      v-if="expense.status"
                      :color="
                        expense.status === 'paid' ? 'green' :
                        expense.status === 'pending' ? 'orange' :
                        expense.status === 'overdue' ? 'red' : 'grey'
                      "
                      text-color="white"
                    >
                      {{ expense.status }}
                    </q-badge>
                  </q-item-section>
                  <q-item-section side>
                    <q-btn flat round color="primary" icon="edit" @click="editExpense(expense); showDayDetailsModal = false" size="sm">
                      <q-tooltip>Edit</q-tooltip>
                    </q-btn>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card-section>
            
            <q-card-actions align="right">
              <q-btn flat label="Close" color="primary" v-close-popup />
            </q-card-actions>
          </q-card>
        </q-dialog>
      </div>
    </template>

    <!-- Expense Form Modal -->
    <ExpenseFormModal
      :show-modal="showAddModal"
      :expense="selectedExpense"
      :type="activeTab"
      @close="showAddModal = false"
      @saved="handleExpenseSaved"
    />
    
    <!-- Delete Confirmation Dialog -->
    <q-dialog v-model="showDeleteDialog" persistent>
      <q-card>
        <q-card-section class="bg-negative text-white">
          <div class="text-h6">Confirm Delete</div>
        </q-card-section>

        <q-card-section class="q-pt-md">
          <p>Are you sure you want to delete this expense?</p>
          <p class="text-caption q-mt-sm">This action cannot be undone.</p>
        </q-card-section>

        <q-card-actions align="right" class="bg-white text-primary q-pa-md">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn flat label="Delete" color="negative" @click="confirmDeleteExpense" :loading="deleteLoading" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import axios from 'axios';
import Chart from 'chart.js/auto';
import ExpenseFormModal from '../../../components/expenses/ExpenseFormModal.vue';

export default {
  components: {
    ExpenseFormModal
  },
  setup() {
    const showAddModal = ref(false);
    const expenses = ref([]);
    const currentPage = ref(1);
    const itemsPerPage = ref(10);
    const selectedExpense = ref(null);
    const isLoading = ref(true);
    const viewMode = ref('list'); // 'list' or 'calendar'
    const activeTab = ref('bill'); // 'bill' or 'enhancement'
    const calendarMonth = ref(new Date().getMonth());
    const calendarYear = ref(new Date().getFullYear());
    const calendarDays = ref([]);
    const monthlyChart = ref(null);
    const categoryChart = ref(null);
    const monthlyChartInstance = ref(null);
    const categoryChartInstance = ref(null);
    const showDayExpensesDialog = ref(false);
    const selectedDay = ref(null);
    const showDayDetailsModal = ref(false);
    const showDeleteDialog = ref(false);
    const deleteLoading = ref(false);
    
    const stats = ref({
      total_expenses_this_month: 0,
      paid_expenses: 0,
      pending_expenses: 0,
      overdue_expenses: 0,
      average_monthly: 0,
      monthly_data: [],
      category_breakdown: [],
      calendar_data: []
    });
    
    const filters = ref({
      category: '',
      status: '',
      fromDate: '',
      toDate: '',
      search: ''
    });

    // Category options
    const categoryOptions = {
      rent: { label: 'Rent', icon: 'home' },
      utilities: { label: 'Utilities', icon: 'bolt' },
      water: { label: 'Water Bill', icon: 'water_drop' },
      equipment: { label: 'Equipment', icon: 'fitness_center' },
      maintenance: { label: 'Maintenance', icon: 'build' },
      salary: { label: 'Salary', icon: 'payments' },
      insurance: { label: 'Insurance', icon: 'shield' },
      marketing: { label: 'Marketing', icon: 'campaign' },
      taxes: { label: 'Taxes', icon: 'receipt_long' },
      supplies: { label: 'Supplies', icon: 'inventory_2' },
      software: { label: 'Software', icon: 'computer' },
      renovation: { label: 'Renovation', icon: 'construction' },
      decoration: { label: 'Decoration', icon: 'palette' },
      furniture: { label: 'Furniture', icon: 'chair' },
      other: { label: 'Other', icon: 'more_horiz' }
    };

    // Load expenses
    const loadExpenses = async () => {
      isLoading.value = true;
      try {
        const response = await axios.get(`/api/expenses?type=${activeTab.value}`);
        expenses.value = response.data.expenses;
        stats.value = response.data.stats;
        
        // Setup calendar days if in calendar view
        if (viewMode.value === 'calendar') {
          setupCalendarDays();
        }
        
        // Update charts with a slight delay to ensure DOM is ready
        nextTick(() => {
          setTimeout(() => {
            updateCharts();
          }, 100);
        });
      } catch (error) {
        console.error('Error loading expenses:', error);
      } finally {
        isLoading.value = false;
      }
    };

    // Filter expenses
    const filteredExpenses = computed(() => {
      return expenses.value.filter(expense => {
        // Filter by search term
        if (filters.value.search && !expense.description.toLowerCase().includes(filters.value.search.toLowerCase())) {
          return false;
        }
        
        // Filter by category
        if (filters.value.category && expense.category !== filters.value.category) {
          return false;
        }
        
        // Filter by status (only for bills)
        if (activeTab.value === 'bill' && filters.value.status && expense.status !== filters.value.status) {
          return false;
        }
        
        // Filter by date range
        if (filters.value.fromDate) {
          const fromDate = new Date(filters.value.fromDate);
          const expenseDate = new Date(expense.bill_date);
          if (expenseDate < fromDate) return false;
        }
        
        if (filters.value.toDate) {
          const toDate = new Date(filters.value.toDate);
          const expenseDate = new Date(expense.bill_date);
          if (expenseDate > toDate) return false;
        }
        
        return true;
      });
    });

    // Paginated expenses
    const paginatedExpenses = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      const end = start + itemsPerPage.value;
      return filteredExpenses.value.slice(start, end);
    });

    // Total pages
    const totalPages = computed(() => {
      return Math.ceil(filteredExpenses.value.length / itemsPerPage.value);
    });

    // Setup calendar days
    const setupCalendarDays = () => {
      const daysInMonth = new Date(calendarYear.value, calendarMonth.value + 1, 0).getDate();
      const firstDayOfMonth = new Date(calendarYear.value, calendarMonth.value, 1).getDay();
      
      // Create array of days
      const days = [];
      
      // Add empty days for the start of the month
      for (let i = 0; i < firstDayOfMonth; i++) {
        days.push({ day: null, expenses: [] });
      }
      
      // Add days of the month
      for (let day = 1; day <= daysInMonth; day++) {
        const date = `${calendarYear.value}-${(calendarMonth.value + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        const dayExpenses = stats.value.calendar_data.find(item => item.date === date);
        
        days.push({
          day,
          date,
          expenses: dayExpenses ? dayExpenses.expenses : [],
          total: dayExpenses ? dayExpenses.total : 0
        });
      }
      
      calendarDays.value = days;
    };

    // Update charts
    const updateCharts = () => {
      // Destroy previous chart instances
      if (monthlyChartInstance.value) {
        monthlyChartInstance.value.destroy();
      }
      
      if (categoryChartInstance.value) {
        categoryChartInstance.value.destroy();
      }
      
      // Create monthly chart
      if (monthlyChart.value && stats.value.monthly_data && stats.value.monthly_data.length > 0) {
        const ctx = monthlyChart.value.getContext('2d');
        
        const labels = stats.value.monthly_data.map(item => item.month);
        const data = stats.value.monthly_data.map(item => item.total);
        
        // Check if we have valid data
        if (labels.length > 0 && data.length > 0) {
          monthlyChartInstance.value = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: labels,
              datasets: [{
                label: 'Monthly Expenses',
                data: data,
                backgroundColor: 'rgba(79, 70, 229, 0.6)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    callback: function(value) {
                      return '$' + value;
                    }
                  }
                }
              }
            }
          });
        } else {
          console.warn('No monthly data available for chart');
        }
      }
      
      // Create category chart
      if (categoryChart.value && stats.value.category_breakdown && stats.value.category_breakdown.length > 0) {
        const ctx = categoryChart.value.getContext('2d');
        
        const labels = stats.value.category_breakdown.map(item => item.label);
        const data = stats.value.category_breakdown.map(item => item.amount);
        
        // Check if we have valid data
        if (labels.length > 0 && data.length > 0) {
          categoryChartInstance.value = new Chart(ctx, {
            type: 'doughnut',
            data: {
              labels: labels,
              datasets: [{
                data: data,
                backgroundColor: [
                  'rgba(79, 70, 229, 0.6)',
                  'rgba(59, 130, 246, 0.6)',
                  'rgba(16, 185, 129, 0.6)',
                  'rgba(245, 158, 11, 0.6)',
                  'rgba(239, 68, 68, 0.6)',
                  'rgba(139, 92, 246, 0.6)',
                  'rgba(236, 72, 153, 0.6)',
                  'rgba(75, 85, 99, 0.6)'
                ],
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'right'
                }
              }
            }
          });
        } else {
          console.warn('No category data available for chart');
        }
      }
    };

    // Change month
    const changeMonth = (increment) => {
      let newMonth = calendarMonth.value + increment;
      let newYear = calendarYear.value;
      
      if (newMonth < 0) {
        newMonth = 11;
        newYear--;
      } else if (newMonth > 11) {
        newMonth = 0;
        newYear++;
      }
      
      calendarMonth.value = newMonth;
      calendarYear.value = newYear;
      
      // Reload expenses for the new month
      loadExpenses();
    };

    // Format currency
    const formatCurrency = (value) => {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
      }).format(value);
    };

    // Format date
    const formatDate = (dateString) => {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      }).format(date);
    };

    // Format date for display
    const formatDateDisplay = (dateString) => {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      }).format(date);
    };

    // Get status color
    const getStatusColor = (status) => {
      switch (status) {
        case 'paid':
          return 'bg-green-100 text-green-800';
        case 'pending':
          return 'bg-yellow-100 text-yellow-800';
        case 'overdue':
          return 'bg-red-100 text-red-800';
        default:
          return 'bg-gray-100 text-gray-800';
      }
    };

    // Get category icon
    const getCategoryIcon = (category) => {
      return categoryOptions[category]?.icon || 'help';
    };
    
    // Get category label
    const getCategoryLabel = (category) => {
      return categoryOptions[category]?.label || category;
    };

    // Edit expense
    const editExpense = (expense) => {
      selectedExpense.value = expense;
      showAddModal.value = true;
    };

    // Delete expense
    const deleteExpense = async (id) => {
      if (confirm('Are you sure you want to delete this expense?')) {
        try {
          await axios.delete(`/api/expenses/${id}`);
          loadExpenses();
        } catch (error) {
          console.error('Error deleting expense:', error);
        }
      }
    };

    // Handle expense saved
    const handleExpenseSaved = () => {
      loadExpenses();
    };

    // Change tab
    const changeTab = (tab) => {
      activeTab.value = tab;
      loadExpenses();
    };

    // Reset filters
    const resetFilters = () => {
      filters.value = {
        category: '',
        status: '',
        fromDate: '',
        toDate: '',
        search: ''
      };
    };

    // Confirm delete expense
    const confirmDeleteExpense = async () => {
      deleteLoading.value = true;
      try {
        await axios.delete(`/api/expenses/${selectedExpense.value.id}`);
        loadExpenses();
        showDeleteDialog.value = false;
      } catch (error) {
        console.error('Error deleting expense:', error);
      } finally {
        deleteLoading.value = false;
      }
    };

    // Watch for view mode changes
    watch(viewMode, (newMode) => {
      if (newMode === 'calendar') {
        setupCalendarDays();
      }
    });

    // Generate calendar days
    const generateCalendarDays = () => {
      const days = [];
      const date = new Date(calendarYear.value, calendarMonth.value, 1);
      const firstDay = date.getDay();
      
      // Add empty slots for days before the first day of the month
      for (let i = 0; i < firstDay; i++) {
        days.push({ day: null, expenses: [], total: 0 });
      }
      
      // Get the number of days in the month
      const lastDay = new Date(calendarYear.value, calendarMonth.value + 1, 0).getDate();
      
      // Add days of the month
      for (let i = 1; i <= lastDay; i++) {
        const dayExpenses = expenses.value.filter(expense => {
          const expenseDate = new Date(expense.bill_date);
          return expenseDate.getDate() === i && 
                 expenseDate.getMonth() === calendarMonth.value && 
                 expenseDate.getFullYear() === calendarYear.value;
        });
        
        const total = dayExpenses.reduce((sum, expense) => sum + parseFloat(expense.amount), 0);
        days.push({
          day: i,
          expenses: dayExpenses,
          total
        });
      }
      
      // Fill remaining slots to complete the grid
      const remainingSlots = 42 - days.length; // 6 rows of 7 days
      for (let i = 0; i < remainingSlots; i++) {
        days.push({ day: null, expenses: [], total: 0 });
      }
      
      calendarDays.value = days;
    };
    
    // Check if a day is today
    const isToday = (year, month, day) => {
      const today = new Date();
      return day === today.getDate() && 
             month === today.getMonth() && 
             year === today.getFullYear();
    };
    
    // Show day expenses
    const showDayDetails = (day) => {
      selectedDay.value = day;
      showDayDetailsModal.value = true;
    };

    // Initialize
    onMounted(() => {
      loadExpenses();
    });

    return {
      showAddModal,
      expenses,
      currentPage,
      itemsPerPage,
      selectedExpense,
      isLoading,
      viewMode,
      activeTab,
      stats,
      filters,
      calendarMonth,
      calendarYear,
      calendarDays,
      monthlyChart,
      categoryChart,
      filteredExpenses,
      paginatedExpenses,
      totalPages,
      categoryOptions,
      loadExpenses,
      setupCalendarDays,
      formatCurrency,
      formatDate,
      formatDateDisplay,
      getStatusColor,
      getCategoryIcon,
      getCategoryLabel,
      editExpense,
      deleteExpense,
      handleExpenseSaved,
      changeTab,
      resetFilters,
      changeMonth,
      generateCalendarDays,
      showDayExpensesDialog,
      selectedDay,
      isToday,
      showDayDetails,
      showDayDetailsModal,
      showDeleteDialog,
      confirmDeleteExpense,
      deleteLoading
    };
  }
};
</script>
