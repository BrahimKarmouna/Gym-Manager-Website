<template>
  <div class="bills-management">
    <q-card flat bordered class="my-card q-mb-md">
      <q-card-section>
        <div class="text-h5 q-mb-md">Bills Management</div>
        
        <!-- Dashboard Statistics Cards -->
        <div class="row q-col-gutter-md q-mb-lg">
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-card class="bg-card1 text-white dashboard-card">
              <q-card-section>
                <div class="row items-center no-wrap">
                  <div class="col">
                    <div class="text-caption">Total Bills This Month</div>
                    <div class="text-h5">${{ stats.total_bills_this_month }}</div>
                  </div>
                  <div class="col-auto">
                    <q-icon name="account_balance_wallet" size="3rem" />
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>
          
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-card class="bg-card2 text-white dashboard-card">
              <q-card-section>
                <div class="row items-center no-wrap">
                  <div class="col">
                    <div class="text-caption">Paid Bills</div>
                    <div class="text-h5">${{ stats.paid_bills }}</div>
                  </div>
                  <div class="col-auto">
                    <q-icon name="verified" size="3rem" />
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>
          
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-card class="bg-card3 text-white dashboard-card">
              <q-card-section>
                <div class="row items-center no-wrap">
                  <div class="col">
                    <div class="text-caption">Pending Bills</div>
                    <div class="text-h5">${{ stats.pending_bills }}</div>
                  </div>
                  <div class="col-auto">
                    <q-icon name="schedule" size="3rem" />
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>
          
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-card class="bg-card4 text-white dashboard-card">
              <q-card-section>
                <div class="row items-center no-wrap">
                  <div class="col">
                    <div class="text-caption">Average Monthly</div>
                    <div class="text-h5">${{ stats.average_monthly }}</div>
                  </div>
                  <div class="col-auto">
                    <q-icon name="show_chart" size="3rem" />
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>
        </div>
        
        <!-- Monthly Chart -->
        <div class="row q-mb-lg">
          <div class="col-md-12">
            <q-card flat bordered>
              <q-card-section>
                <div class="text-subtitle1 q-mb-sm">Monthly Bills Overview</div>
                <apexchart 
                  type="bar" 
                  height="250" 
                  :options="chartOptions" 
                  :series="chartSeries">
                </apexchart>
              </q-card-section>
            </q-card>
          </div>
        </div>
        
        <!-- Filter Tools -->
        <div class="row q-col-gutter-md q-mb-md">
          <div class="col-md-4 col-sm-6 col-xs-12">
            <q-input outlined dense v-model="search" label="Search bills" clearable>
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-select 
              outlined 
              dense 
              v-model="statusFilter" 
              :options="statusOptions" 
              label="Status" 
              emit-value 
              map-options 
              clearable />
          </div>
          
          <div class="col-md-2 col-sm-6 col-xs-12">
            <q-btn 
              color="primary" 
              label="Add Bill" 
              class="full-width"
              @click="openDialog()" />
          </div>
        </div>
        
        <!-- Bills Calendar View -->
        <div class="q-mb-md">
          <q-toggle
            v-model="calendarView"
            label="Calendar View"
            left-label
          />
        </div>
        
        <!-- Calendar View -->
        <div v-if="calendarView" class="q-mb-md">
          <q-card flat bordered>
            <q-card-section>
              <q-date
                v-model="selectedDate"
                :events="calendarEvents"
                :event-color="getEventColor"
                @update:model-value="handleDateSelect"
                color="primary"
                landscape
              />
            </q-card-section>
          </q-card>
          
          <!-- Bills for Selected Date -->
          <q-card v-if="selectedDateBills.length > 0" flat bordered class="q-mt-md">
            <q-card-section>
              <div class="text-subtitle1">Bills for {{ formatDate(selectedDate) }}</div>
              <q-list>
                <q-item v-for="bill in selectedDateBills" :key="bill.id" class="q-mb-xs">
                  <q-item-section>
                    <q-item-label>{{ bill.description }}</q-item-label>
                    <q-item-label caption>{{ bill.gym.name }}</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-badge :color="getStatusColor(bill.status)">{{ bill.status }}</q-badge>
                  </q-item-section>
                  <q-item-section side>
                    ${{ bill.amount }}
                  </q-item-section>
                  <q-item-section side>
                    <q-btn flat dense round icon="more_vert">
                      <q-menu>
                        <q-list style="min-width: 100px">
                          <q-item clickable @click="openDialog(bill)">
                            <q-item-section>Edit</q-item-section>
                          </q-item>
                          <q-item clickable @click="confirmDelete(bill)">
                            <q-item-section>Delete</q-item-section>
                          </q-item>
                        </q-list>
                      </q-menu>
                    </q-btn>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card-section>
          </q-card>
        </div>
        
        <!-- Bills Table View -->
        <div v-else>
          <q-table
            :rows="filteredBills"
            :columns="columns"
            row-key="id"
            :pagination.sync="pagination"
            :loading="loading"
            :filter="search"
            binary-state-sort
            flat
            bordered
          >
            <template v-slot:body="props">
              <q-tr :props="props">
                <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                <q-td key="description" :props="props">{{ props.row.description }}</q-td>
                <q-td key="gym" :props="props">{{ props.row.gym.name }}</q-td>
                <q-td key="amount" :props="props">
                  ${{ props.row.amount }}
                </q-td>
                <q-td key="bill_date" :props="props">
                  {{ formatDate(props.row.bill_date) }}
                </q-td>
                <q-td key="due_date" :props="props">
                  {{ props.row.due_date ? formatDate(props.row.due_date) : '-' }}
                </q-td>
                <q-td key="status" :props="props">
                  <q-badge :color="getStatusColor(props.row.status)">
                    {{ props.row.status }}
                  </q-badge>
                </q-td>
                <q-td key="actions" :props="props">
                  <q-btn flat dense round icon="more_vert">
                    <q-menu>
                      <q-list style="min-width: 100px">
                        <q-item clickable @click="openDialog(props.row)">
                          <q-item-section>Edit</q-item-section>
                        </q-item>
                        <q-item clickable @click="confirmDelete(props.row)">
                          <q-item-section>Delete</q-item-section>
                        </q-item>
                      </q-list>
                    </q-menu>
                  </q-btn>
                </q-td>
              </q-tr>
            </template>
          </q-table>
        </div>
      </q-card-section>
    </q-card>
    
    <!-- Add/Edit Bill Dialog -->
    <q-dialog v-model="dialogVisible" persistent>
      <q-card style="min-width: 500px">
        <q-card-section class="row items-center">
          <div class="text-h6">{{ editingBill ? 'Edit Bill' : 'Add New Bill' }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        
        <q-card-section>
          <q-form @submit.prevent="saveBill">
            <div class="row q-col-gutter-md">
              <div class="col-md-6 col-sm-12">
                <q-input
                  outlined
                  v-model="formData.description"
                  label="Description"
                  hint="Optional"
                />
              </div>
              
              <div class="col-md-6 col-sm-12">
                <q-input
                  outlined
                  v-model.number="formData.amount"
                  label="Amount *"
                  type="number"
                  prefix="$"
                  :rules="[
                    val => !!val || 'Amount is required',
                    val => val > 0 || 'Amount must be greater than 0'
                  ]"
                />
              </div>
              
              <div class="col-md-6 col-sm-12">
                <q-input
                  outlined
                  v-model="formData.bill_date"
                  label="Bill Date *"
                  :rules="[val => !!val || 'Bill date is required']"
                >
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-date v-model="formData.bill_date">
                          <div class="row items-center justify-end">
                            <q-btn v-close-popup label="Close" color="primary" flat />
                          </div>
                        </q-date>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
              
              <div class="col-md-6 col-sm-12">
                <q-select
                  outlined
                  v-model="formData.status"
                  :options="statusOptions"
                  label="Status *"
                  :rules="[val => !!val || 'Status is required']"
                />
              </div>
              
              <div class="col-md-6 col-sm-12">
                <q-input
                  outlined
                  v-model="formData.due_date"
                  label="Due Date"
                >
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-date v-model="formData.due_date">
                          <div class="row items-center justify-end">
                            <q-btn v-close-popup label="Close" color="primary" flat />
                          </div>
                        </q-date>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
              
              <div class="col-md-6 col-sm-12">
                <q-select
                  outlined
                  v-model="formData.category"
                  :options="categoryOptions"
                  label="Category"
                  option-label="label"
                  option-value="value"
                  emit-value
                  map-options
                >
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
                  
                  <template v-slot:selected>
                    <div class="row items-center" v-if="formData.category">
                      <q-icon :name="getCategoryIcon(formData.category)" class="q-mr-xs" />
                      <div>{{ getCategoryLabel(formData.category) }}</div>
                    </div>
                  </template>
                </q-select>
              </div>
            </div>
            
            <div class="row justify-end q-mt-md">
              <q-btn label="Cancel" flat @click="dialogVisible = false" />
              <q-btn label="Save" type="submit" color="primary" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
    
    <!-- Delete Confirmation Dialog -->
    <q-dialog v-model="deleteDialog" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <q-avatar icon="delete" color="negative" text-color="white" />
          <span class="q-ml-sm">Delete Bill</span>
        </q-card-section>

        <q-card-section>
          Are you sure you want to delete this bill?
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn flat label="Delete" color="negative" @click="deleteBill" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { date } from 'quasar';
import axios from 'axios';

export default {
  data() {
    return {
      bills: [],
      stats: {
        total_bills_this_month: 0,
        paid_bills: 0,
        pending_bills: 0,
        average_monthly: 0
      },
      chartData: [],
      loading: true,
      search: '',
      statusFilter: null,
      selectedDate: date.formatDate(new Date(), 'YYYY/MM/DD'),
      calendarView: false,
      dialogVisible: false,
      deleteDialog: false,
      editingBill: null,
      billToDelete: null,
      formData: this.getDefaultFormData(),
      statusOptions: [
        { label: 'Paid', value: 'paid' },
        { label: 'Pending', value: 'pending' },
        { label: 'Overdue', value: 'overdue' }
      ],
      categoryOptions: [
        { label: 'Rent', value: 'rent', icon: 'home' },
        { label: 'Utilities', value: 'utilities', icon: 'bolt' },
        { label: 'Water Bill', value: 'water', icon: 'water_drop' },
        { label: 'Equipment', value: 'equipment', icon: 'fitness_center' },
        { label: 'Maintenance', value: 'maintenance', icon: 'build' },
        { label: 'Salary', value: 'salary', icon: 'payments' },
        { label: 'Insurance', value: 'insurance', icon: 'shield' },
        { label: 'Marketing', value: 'marketing', icon: 'campaign' },
        { label: 'Taxes', value: 'taxes', icon: 'receipt_long' },
        { label: 'Supplies', value: 'supplies', icon: 'inventory_2' },
        { label: 'Software', value: 'software', icon: 'computer' },
        { label: 'Other', value: 'other', icon: 'more_horiz' }
      ],
      pagination: {
        rowsPerPage: 10
      },
      columns: [
        { name: 'id', label: 'ID', field: 'id', sortable: true, align: 'left' },
        { name: 'description', label: 'Description', field: 'description', sortable: true, align: 'left' },
        { name: 'gym', label: 'Gym', field: row => row.gym.name, sortable: true, align: 'left' },
        { name: 'amount', label: 'Amount', field: 'amount', sortable: true, align: 'right' },
        { name: 'bill_date', label: 'Bill Date', field: 'bill_date', sortable: true, align: 'left' },
        { name: 'due_date', label: 'Due Date', field: 'due_date', sortable: true, align: 'left' },
        { name: 'status', label: 'Status', field: 'status', sortable: true, align: 'left' },
        { name: 'actions', label: 'Actions', field: 'actions', align: 'center' }
      ]
    };
  },
  
  computed: {
    filteredBills() {
      let bills = [...this.bills];
      
      if (this.statusFilter) {
        bills = bills.filter(bill => bill.status === this.statusFilter);
      }
      
      return bills;
    },
    
    selectedDateBills() {
      const formattedDate = date.formatDate(this.selectedDate, 'YYYY-MM-DD');
      return this.bills.filter(bill => {
        const billDate = date.formatDate(bill.bill_date, 'YYYY-MM-DD');
        return billDate === formattedDate;
      });
    },
    
    calendarEvents() {
      const events = {};
      this.bills.forEach(bill => {
        const formattedDate = date.formatDate(bill.bill_date, 'YYYY-MM-DD');
        events[formattedDate] = true;
      });
      return events;
    },
    
    chartOptions() {
      return {
        chart: {
          id: 'bills-monthly-chart',
          toolbar: {
            show: false
          }
        },
        plotOptions: {
          bar: {
            borderRadius: 5,
            columnWidth: '60%',
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: this.chartData.map(item => `${item.month} ${item.year}`)
        },
        colors: ['#4CAF50'],
        tooltip: {
          y: {
            formatter: function (val) {
              return `$${val}`
            }
          }
        }
      };
    },
    
    chartSeries() {
      return [{
        name: 'Bills Total',
        data: this.chartData.map(item => item.total)
      }];
    }
  },
  
  mounted() {
    this.fetchData();
  },
  
  methods: {
    async fetchData() {
      this.loading = true;
      try {
        // Fetch bills with stats
        const { data } = await axios.get('/api/bills');
        this.bills = data.bills;
        
        // Fetch dashboard stats
        const statsResponse = await axios.get('/api/bills/stats');
        this.stats = statsResponse.data;
        this.chartData = statsResponse.data.monthly_data;
      } catch (error) {
        console.error('Error fetching bills:', error);
        this.$q.notify({
          color: 'negative',
          message: 'Failed to load bills data'
        });
      } finally {
        this.loading = false;
      }
    },
    
    getDefaultFormData() {
      return {
        description: '',
        amount: null,
        bill_date: date.formatDate(new Date(), 'YYYY/MM/DD'),
        status: 'pending',
        due_date: null,
        category: ''
      };
    },
    
    formatDate(dateStr) {
      if (!dateStr) return '-';
      return date.formatDate(dateStr, 'MMMM D, YYYY');
    },
    
    getStatusColor(status) {
      const colors = {
        paid: 'positive',
        pending: 'warning',
        overdue: 'negative'
      };
      return colors[status] || 'grey';
    },
    
    getEventColor(date) {
      const formattedDate = date.replace(/\//g, '-');
      const bill = this.bills.find(b => {
        return date.formatDate(b.bill_date, 'YYYY-MM-DD') === formattedDate;
      });
      
      if (bill) {
        return this.getStatusColor(bill.status);
      }
      return 'primary';
    },
    
    handleDateSelect(selectedDate) {
      this.selectedDate = selectedDate;
    },
    
    openDialog(bill = null) {
      this.editingBill = bill;
      
      if (bill) {
        this.formData = {
          description: bill.description,
          amount: bill.amount,
          bill_date: date.formatDate(bill.bill_date, 'YYYY/MM/DD'),
          status: bill.status,
          due_date: bill.due_date ? date.formatDate(bill.due_date, 'YYYY/MM/DD') : null,
          category: bill.category || ''
        };
      } else {
        this.formData = this.getDefaultFormData();
      }
      
      this.dialogVisible = true;
    },
    
    confirmDelete(bill) {
      this.billToDelete = bill;
      this.deleteDialog = true;
    },
    
    async saveBill() {
      try {
        // Ensure status is a valid string value
        const formDataToSend = { ...this.formData };
        
        // Make sure status is one of the valid options
        if (!formDataToSend.status || !['paid', 'pending', 'overdue'].includes(formDataToSend.status)) {
          formDataToSend.status = 'pending';
        }
        
        if (this.editingBill) {
          // Update existing bill
          await axios.put(`/api/bills/${this.editingBill.id}`, formDataToSend);
          this.$q.notify({
            color: 'positive',
            message: 'Bill updated successfully'
          });
        } else {
          // Create new bill
          await axios.post('/api/bills', formDataToSend);
          this.$q.notify({
            color: 'positive',
            message: 'Bill created successfully'
          });
        }
        
        this.dialogVisible = false;
        this.fetchData(); // Refresh data
      } catch (error) {
        console.error('Error saving bill:', error);
        this.$q.notify({
          color: 'negative',
          message: error.response?.data?.message || 'Failed to save bill'
        });
      }
    },
    
    async deleteBill() {
      if (!this.billToDelete) return;
      
      try {
        await axios.delete(`/api/bills/${this.billToDelete.id}`);
        this.$q.notify({
          color: 'positive',
          message: 'Bill deleted successfully'
        });
        this.fetchData(); // Refresh data
      } catch (error) {
        console.error('Error deleting bill:', error);
        this.$q.notify({
          color: 'negative',
          message: 'Failed to delete bill'
        });
      }
    },
    
    getCategoryIcon(category) {
      const icons = {
        rent: 'home',
        utilities: 'bolt',
        water: 'water_drop',
        equipment: 'fitness_center',
        maintenance: 'build',
        salary: 'payments',
        insurance: 'shield',
        marketing: 'campaign',
        taxes: 'receipt_long',
        supplies: 'inventory_2',
        software: 'computer',
        other: 'more_horiz'
      };
      return icons[category] || 'help';
    },
    
    getCategoryLabel(category) {
      const labels = {
        rent: 'Rent',
        utilities: 'Utilities',
        water: 'Water Bill',
        equipment: 'Equipment',
        maintenance: 'Maintenance',
        salary: 'Salary',
        insurance: 'Insurance',
        marketing: 'Marketing',
        taxes: 'Taxes',
        supplies: 'Supplies',
        software: 'Software',
        other: 'Other'
      };
      return labels[category] || 'Unknown';
    }
  }
};
</script>

<style scoped>
.my-card {
  width: 100%;
  max-width: 100%;
}

.bg-card1 {
  background-color: #4CAF50;
}

.bg-card2 {
  background-color: #03A9F4;
}

.bg-card3 {
  background-color: #FF9800;
}

.bg-card4 {
  background-color: #2196F3;
}

.dashboard-card {
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
