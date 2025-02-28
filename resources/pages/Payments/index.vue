<template>
  <CreateForm
    :visible="is_visible"
    @update:visible="is_visible = $event"
    @new_payment="refreshPayments"
  />


  <q-btn
    @click="OpenModal"
    color="primary"
    label="Add Payment"
  >
    Add Payment
    <q-icon name="add" />
  </q-btn>

  <q-table
    title="Payments"
    :rows="payments"
    :columns="columns"
    row-key="id"
  >
    <template v-slot:body-cell-actions="props">
      <q-td :props="props">
        <q-btn
          color="negative"
          icon="delete"
          @click="confirmDeletePayment(props.row.id)"
        />
      </q-td>
    </template>
  </q-table>
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
