<template>
  <div class="q-pa-md q-gutter-sm">
    <q-dialog
      v-model="visible"
      persistent
    >
      <q-card style="min-width: 900px; height: 600px;">
        <q-card-section class="row justify-between items-center">
          <div class="text-h6">Add Payment</div>
          <q-btn
            flat
            round
            dense
            icon="close"
            @click="closeModal"
          />
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div class="row q-col-gutter-md">
            <!-- Client (Preselected and Disabled if Provided) -->
            <q-input
              v-if="client"
              :model-value="payment.client.Full_name"
              label="Client"
              filled
              class="col-6 q-mb-md"
              disable
            />

            <!-- Client Selection Dropdown (Only If No Client Is Provided) -->
            <q-select
              v-else
              v-model="payment.client"
              :options="clients"
              label="Select Client"
              filled
              class="col-6 q-mb-md"
              option-value="id"
              option-label="Full_name"
              use-input
              @filter="searchClients"
            />

            <!-- Plan Selection -->
            <q-select
              v-model="payment.plan"
              :options="plans"
              label="Select Plan"
              filled
              class="col-6 q-mb-md"
              option-value="id"
              option-label="name"
            />

            <!-- Payment Date -->
            <q-input
              dense
              v-model="payment.payment_date"
              label="Payment Date"
              type="date"
              filled
              class="col-6 q-mb-md"
            />
          </div>
        </q-card-section>

        <q-card-actions
          align="right"
          class="text-primary"
        >
          <q-btn
            flat
            label="Cancel"
            @click="closeModal"
          />
          <q-btn
            flat
            label="Save Payment"
            @click="savePayment"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { defineEmits, defineProps, defineModel } from "vue";
import axios from "axios";
const emit = defineEmits(["new_payment"]);
import { useQuasar } from 'quasar';
const $q = useQuasar();

// Props
const props = defineProps({
  client: Object // Optional: Preselected client
});

// Visibility control
const visible = defineModel("visible", { default: false, type: Boolean });

// Payment form model
const payment = ref({
  client_id: props.client || null,  // Use client from props if available
  plan_id: null,
  payment_date: new Date().toISOString().substr(0, 10)
});

// Clients and plans data
const clients = ref([]);
const plans = ref([]);
// const search = ref("");


// FILTER CLIENTS IN FRONTEND USING JS

// const filterClients = () => {
//     clients.value = clients.filter( (client) => {
//         if (!search.value) return;
//         if (client.Full_name.includes(search.value)) {
//             return client;
//         }
//     })
// }

const searchClients = (value, update, abort) => {
  update(() => fetchClients(value));
}

const fetchClients = async (search = null) => {
  let url;
  if (!search) url = '/api/clients';
  else url = `/api/clients?search=${search}`;
  await axios.get(url).then((response) => {
    clients.value = response.data;
  });
};

// Fetch data on mount
onMounted(() => {
  fetchClients();
  axios.get("/api/plans").then((response) => {
    plans.value = response.data;
  });
});

// Watch for changes in client prop and update payment.client
watch(() => props.client, (newClient) => {
  payment.value.client = newClient;
});

// Close modal and reset form state
const closeModal = () => {
  visible.value = false;
  resetPayment();
};

// Reset the payment form
const resetPayment = () => {
  payment.value = {
    client: props.client || null, // Reset to selected client
    plan: null,
    payment_date: new Date().toISOString().substr(0, 10),
  };
};

// Save payment to the backend
const savePayment = () => {
  if (!payment.value.client || !payment.value.plan) {
    console.error("Client and Plan are required!");
    return;
  }

  // Prepare payload
  const paymentData = {
    client_id: payment.value.client.id,
    plan_id: payment.value.plan.id,
    payment_date: payment.value.payment_date
  };

  axios.post("/api/payments", paymentData)
    .then(() => {
      console.log("Payment saved successfully.");
      $q.notify({
        type: "positive",
        message: "Success",
        caption: "Payment saved successfully.",
        position: "bottom-right",
        timeout: 4000,
        progress: true,

      });
      closeModal();
      emit("new_payment");
    })
    .catch((error) => {
      console.error("Error saving payment:", error);
    });
};

// API call


</script>
