<template>
  <div class="payments-dashboard q-pa-md">
    <!-- Header section -->
    <div class="row items-center q-mb-lg">
      <div class="col-12 col-sm-8">
        <h4 class="text-h4 q-my-none text-weight-bold text-primary">Payments Management</h4>
        <p class="text-subtitle1 q-mt-sm text-grey-7">Track and manage all payment transactions</p>
      </div>
      <div class="col-12 col-sm-4 text-right">
        <q-btn
          @click="OpenModal"
          unelevated
          rounded
          color="primary"
          class="q-px-md q-py-sm"
          icon-right="add_circle"
          label="New Payment"
          :loading="false"
        />
      </div>
    </div>

    <!-- Filter and search section -->
    <q-card flat bordered class="q-mb-md bg-grey-1">
      <q-card-section>
        <div class="row q-col-gutter-md">
          <div class="col-12 col-sm-4">
            <q-input dense filled v-model="search" label="Search payments" clearable>
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="col-12 col-sm-4">
            <q-select dense filled v-model="dateFilter" label="Filter by date" :options="['All', 'Last 7 days', 'Last month']" />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Payments table -->
    <q-card flat bordered class="payments-table-card">
      <q-table
        :rows="payments"
        :columns="columns"
        row-key="id"
        :loading="false"
        :filter="search"
        :pagination="{ rowsPerPage: 10 }"
        class="payments-table"
        flat
      >
        <template v-slot:top>
          <div class="text-h6 text-weight-bold q-py-sm">Payment Records</div>
        </template>
        
        <template v-slot:body-cell-payment_date="props">
          <q-td :props="props">
            <div class="text-weight-medium">
              {{ new Date(props.value).toLocaleDateString() }}
            </div>
          </q-td>
        </template>
        
        <template v-slot:body-cell-amount="props">
          <q-td :props="props" class="text-weight-medium text-primary">
            ${{ props.value }}
          </q-td>
        </template>
        
        <template v-slot:body-cell-actions="props">
          <q-td :props="props" class="text-center">
            <q-btn
              flat
              round
              color="negative"
              icon="delete_outline"
              size="sm"
              class="q-ml-sm"
              @click.stop="confirmDeletePayment(props.row.id)"
            >
              <q-tooltip>Delete Payment</q-tooltip>
            </q-btn>
            <q-btn
              flat
              round
              color="info"
              icon="receipt_long"
              size="sm"
              class="q-ml-sm"
            >
              <q-tooltip>View Receipt</q-tooltip>
            </q-btn>
          </q-td>
        </template>
        
        <template v-slot:no-data>
          <div class="full-width row flex-center q-pa-lg">
            <q-icon name="payments" size="3em" color="grey-6" />
            <div class="q-ml-md text-grey-6">No payment records found</div>
          </div>
        </template>
      </q-table>
    </q-card>
  </div>

  <CreateForm
    :visible="is_visible"
    @update:visible="is_visible = $event"
    @new_payment="refreshPayments"
  />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import CreateForm from './createForm.vue';
import { useQuasar } from 'quasar';
const $q = useQuasar();


const is_visible = ref(false);

const OpenModal = () => {
  is_visible.value = true;
};

const payments = ref([]);
const client_id = ref(null);
const plan_id = ref(null);
const payment_date = ref(null);



const columns = [
  { name: 'id', align: 'center', label: 'ID', field: 'id' },
  {
    name: 'client_name',
    align: 'center',
    label: 'Client Name',
    field: row => row.client ? row.client.Full_name : 'N/A'
  },
  {
/*************  ✨ Codeium Command ⭐  *************/
/**
 * Fetch all payments from the API.
 *
 * This function sends a GET request to `/api/payments` and updates the `payments` ref with the response data.
 *
 * @returns {Promise<void>} A promise that resolves when the request is complete.
 */
/******  0f31dfba-fab7-40b4-86c3-3f4268756e84  *******/        name: 'plan_name',
    align: 'center',
    label: 'Plan Name',
    field: row => row.plan ? row.plan.name : 'N/A'
  },
  { name: 'amount', align: 'center', label: 'Amount', field: row => row.plan ? row.plan.price : 'N/A' },
  { name: 'payment_date', align: 'center', label: 'Payment Date', field: 'payment_date' },
  { name: 'actions', align: 'center', label: 'Actions', field: 'actions' },
];



const getPayments = () => {
  axios
    .get('/api/payments')
    .then((response) => {
      payments.value = response.data;


    })
    .catch((error) => {
      console.error('Error fetching payments:', error);
    });
};
const confirmDeletePayment = (id) => {
  $q.dialog({
    title: "Delete Payment",
    message: "Are you sure you want to delete this payment?",
    cancel: true,
    persistent: true,
  }).onOk(() => deletePayment(id));
};

const deletePayment = async (id) => {
  try {
    await axios.delete(`/api/payments/${id}`);
    console.log("Payment deleted.");
    $q.notify({
      type: "positive",
      message: "Success",
      caption: "Payment deleted successfully.",
      position: "bottom-right",
      timeout: 4000,
      progress: true,

    });
    getPayments();
  } catch (error) {
    console.error("Failed to delete payment:", error);
  }
};

const refreshPayments = () => {
  getPayments();
};
onMounted(() => {
  getPayments();
});

// const handlePayment = () => {
//     client_id.value = payment.client.id;
//     plan_id.value = payment.plan.id;
//     payment_date.value = payment.payment_date;
//     is_visible.value = false;
//     getPayments();
//     is_visible.value = false;
// };
const handlePayment = (paymentData) => {
  console.log("Received emitted payment:", paymentData); // Debugging Log

  if (!paymentData.client_id || !paymentData.plan_id) {
    console.error("Error: Missing client_id or plan_id in payment data");
    return;
  }

  axios.post("/api/payments", paymentData)
    .then(() => {
      getPayments();  // Refresh table first
      is_visible.value = false;  // Close modal after successful update
      $q.notify({
        color: "green-4",
        textColor: "white",
        icon: "check",
        message: "Payment saved successfully.",
      });
    })
    .catch((error) => {
      console.error("Error saving payment:", error);
    });
};

</script>
