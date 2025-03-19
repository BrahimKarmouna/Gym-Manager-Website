<template>
  <q-dialog
    v-model="visible"
    transition-show="slide-up"
    transition-hide="slide-down"
    persistent
  >
    <q-card class="rounded-xl max-w-[900px] w-[90vw] bg-white">
      <div class="flex justify-between items-center p-4 bg-gradient-to-r from-gray-50 to-white rounded-t-xl">
        <div class="text-h5 font-bold">New Payment</div>
        <q-btn flat round color="grey-7" icon="close" @click="closeModal" />
      </div>
      
      <q-separator />

      <q-card-section class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Client Selection -->
          <div>
            <q-input
              v-if="client"
              :model-value="payment.client.Full_name"
              label="Client"
              outlined
              bg-color="white"
              class="rounded-lg"
              disable
              prefix="person |"
            >
              <template v-slot:prepend>
                <q-icon name="person" color="primary" />
              </template>
            </q-input>

            <q-select
              v-else
              v-model="payment.client"
              :options="clients"
              label="Select Client"
              outlined
              bg-color="white"
              class="rounded-lg"
              option-value="id"
              option-label="Full_name"
              use-input
              @filter="searchClients"
              popup-content-class="client-dropdown"
            >
              <template v-slot:prepend>
                <q-icon name="person" color="primary" />
              </template>
              <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey">No clients found</q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>

          <!-- Plan Selection -->
          <div>
            <q-select
              v-model="payment.plan"
              :options="plans"
              label="Select Plan"
              outlined
              bg-color="white"
              class="rounded-lg"
              option-value="id"
              option-label="name"
            >
              <template v-slot:prepend>
                <q-icon name="fitness_center" color="primary" />
              </template>
              <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey">No plans available</q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>

          <!-- Payment Date -->
          <div>
            <q-input
              v-model="payment.payment_date"
              label="Payment Date"
              outlined
              bg-color="white"
              readonly
              class="rounded-lg cursor-pointer"
            >
              <template v-slot:prepend>
                <q-icon name="event" color="primary" />
              </template>
              <template v-slot:append>
                <q-icon name="arrow_drop_down" color="primary" class="cursor-pointer" />
              </template>
              <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                <q-card class="rounded-xl shadow-lg overflow-hidden">
                  <q-date
                    v-model="payment.payment_date"
                    mask="YYYY-MM-DD"
                    today-btn
                    color="primary"
                    navigation-min-years-shown="3"
                    minimal
                    class="p-2"
                  >
                    <div class="flex items-center justify-end p-2">
                      <q-btn 
                        v-close-popup 
                        label="Select" 
                        color="primary" 
                        unelevated
                        rounded
                        dense
                        class="px-4"
                      />
                    </div>
                  </q-date>
                </q-card>
              </q-popup-proxy>
            </q-input>
          </div>
        </div>
      </q-card-section>
      
      <q-card-section class="bg-gray-50 rounded-lg mx-5 mt-4">
        <q-item>
          <q-item-section>
            <div class="text-subtitle2 text-gray-600">Payment Summary</div>
            <div class="flex justify-between mt-2">
              <span class="text-sm">Plan:</span>
              <span class="text-sm font-bold">{{ payment.plan?.name || 'Not selected' }}</span>
            </div>
            <div class="flex justify-between mt-1">
              <span class="text-sm">Price:</span>
              <span class="text-sm font-bold">{{ payment.plan?.price ? '$' + payment.plan.price : 'N/A' }}</span>
            </div>
          </q-item-section>
        </q-item>
      </q-card-section>

      <q-card-actions align="right" class="p-4">
        <q-btn flat color="grey-7" label="Cancel" @click="closeModal" />
        <q-btn 
          unelevated 
          color="primary" 
          label="Complete Payment" 
          @click="savePayment" 
          :loading="false" 
          :disable="!payment.client || !payment.plan"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
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
    clients.value = response.data.clients;
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
