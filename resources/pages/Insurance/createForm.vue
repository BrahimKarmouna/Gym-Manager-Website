<template>
  <q-dialog
    v-model="visible"
    transition-show="slide-up"
    transition-hide="slide-down"
    persistent
  >
    <q-card class="rounded-xl max-w-[900px] w-[90vw] bg-white">
      <div
        class="flex justify-between items-center p-4 bg-gradient-to-r from-gray-50 to-white rounded-t-xl"
      >
        <div class="text-h5 font-bold">Add Insurance</div>
        <q-btn flat round color="grey-7" icon="close" @click="closeModal" />
      </div>

      <q-separator />

      <q-card-section class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Client Selection -->
          <div>
            <q-input
              v-if="client"
              :model-value="insurance.client.Full_name"
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
              v-model="insurance.client"
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
                  <q-item-section class="text-grey"
                    >No clients found</q-item-section
                  >
                </q-item>
              </template>
            </q-select>
          </div>

          <!-- Plan Selection -->
          <div>
            <q-select
              v-model="insurance.plan"
              :options="plans"
              option-label="name"
              label="Insurance Plan"
              outlined
              map-options
              emit-value
              class="mb-4"
              :rules="[val => !!val || 'Please select an insurance plan']"
              @update:model-value="calculateExpiryDate"
            >
              <template v-slot:option="scope">
                <q-item v-bind="scope.itemProps">
                  <q-item-section>
                    <q-item-label>{{ scope.opt.name }}</q-item-label>
                    <q-item-label caption>
                      Price: {{ scope.opt.price }} | Duration: {{ scope.opt.duration }} months
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </template>
              <template v-slot:selected>
                <template v-if="insurance.plan">
                  <div>
                    <div>{{ insurance.plan.name }}</div>
                    <div class="text-xs text-gray-500">
                      Price: {{ insurance.plan.price }} | Duration: {{ insurance.plan.duration }} months
                    </div>
                  </div>
                </template>
                <template v-else>Select Insurance Plan</template>
              </template>
            </q-select>
          </div>

          <!-- Payment Date -->
          <div>
            <q-input
              v-model="insurance.payment_date"
              label="Payment Date"
              outlined
              type="date"
              class="mb-4"
              :rules="[val => !!val || 'Payment date is required']"
              @update:model-value="calculateExpiryDate"
            />
          </div>

          <!-- Expiry Date -->
          <div>
            <q-input
              v-model="insurance.expiry_date"
              label="Insurance Expiry Date"
              outlined
              type="date"
              class="mb-4"
              :rules="[val => !!val || 'Expiry date is required']"
              readonly
              disable
              hint="Automatically calculated based on payment date and plan duration"
            />
          </div>
        </div>
      </q-card-section>

      <q-card-section class="bg-gray-50 rounded-lg mx-5 mt-4">
        <q-item>
          <q-item-section>
            <div class="text-subtitle2 text-gray-600">Insurance Summary</div>
            <div class="flex justify-between mt-2">
              <span class="text-body2">Plan:</span>
              <span class="text-body2 font-bold">{{
                insurance.plan ? insurance.plan.name : "Not selected"
              }}</span>
            </div>
            <div class="flex justify-between mt-1">
              <span class="text-body2">Client:</span>
              <span class="text-body2 font-bold">{{
                insurance.client
                  ? insurance.client.Full_name
                  : "Not selected"
              }}</span>
            </div>
            <div class="flex justify-between mt-1">
              <span class="text-body2">Duration:</span>
              <span class="text-body2 font-bold">{{
                insurance.plan
                  ? insurance.plan.duration + " months"
                  : "Not selected"
              }}</span>
            </div>
            <div class="flex justify-between mt-1">
              <span class="text-body2">Price:</span>
              <span class="text-body2 font-bold">{{
                insurance.plan ? "$" + insurance.plan.price : "Not selected"
              }}</span>
            </div>
          </q-item-section>
        </q-item>
      </q-card-section>

      <q-card-actions align="right" class="p-4">
        <q-btn
          flat
          label="Cancel"
          color="grey-7"
          class="px-4 rounded-lg"
          @click="closeModal"
        />
        <q-btn
          unelevated
          label="Save Insurance"
          color="primary"
          class="px-4 rounded-lg"
          :disable="!insurance.client || !insurance.plan"
          @click="saveInsurance"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import { useQuasar } from "quasar";
import { defineEmits, defineModel, defineProps } from "vue";
const $q = useQuasar();
const emit = defineEmits(["insuranceAdded"]);
const props = defineProps({
  client: Object, // Optional: Preselected client
});
// Visibility control
const visible = defineModel("visible", { default: false, type: Boolean });

// Insurance form model

// Clients and plans data
const clients = ref([]);
const plans = ref([]);

// Search clients function
const searchClients = (val, update) => {
  if (val === '') {
    // If the search string is empty, fetch all clients
    fetchClients('').then(update);
  } else {
    // Otherwise, search for clients matching the input
    fetchClients(val).then(update);
  }
};

// Fetch clients from the API
const fetchClients = async (search = '') => {
  try {
    const url = search ? `/api/clients/search?q=${search}` : '/api/clients';
    const response = await axios.get(url);
    clients.value = response.data.clients || [];
    console.log('Fetched clients:', clients.value);
    return clients.value;
  } catch (error) {
    console.error('Error fetching clients:', error);
    return [];
  }
};

// Fetch plans from the API
const fetchPlans = async () => {
  try {
    const response = await axios.get('/api/insurance-plans');
    plans.value = response.data || [];
    console.log('Fetched plans:', plans.value);
  } catch (error) {
    console.error('Error fetching plans:', error);
  }
};

// Fetch clients and plans data on component mount
onMounted(() => {
  fetchClients();
  fetchPlans();
  
  // If a client was passed as a prop, set it in the insurance form
  if (props.client) {
    insurance.value.client = props.client;
  }
});

// Watch for changes to the client prop
watch(() => props.client, (newClient) => {
  if (newClient) {
    insurance.value.client = newClient;
  }
});

// Close modal and reset form state
const closeModal = () => {
  visible.value = false;
  resetInsurance();
};

// Reset the insurance form
const resetInsurance = () => {
  insurance.value = {
    client: props.client || null,
    plan: null,
    payment_date: new Date().toISOString().substr(0, 10),
    expiry_date: '', // Will be calculated when plan is selected
  };
};

const insurance = ref({
  client: props.client || null,
  plan: null,
  payment_date: new Date().toISOString().substr(0, 10),
  expiry_date: '', // Will be calculated when plan is selected
});

// Calculate expiry date based on payment date and plan duration
const calculateExpiryDate = () => {
  if (insurance.value.payment_date && insurance.value.plan) {
    const paymentDate = new Date(insurance.value.payment_date);
    const durationMonths = parseInt(insurance.value.plan.duration);
    
    // Add duration months to payment date
    const expiryDate = new Date(paymentDate);
    expiryDate.setMonth(expiryDate.getMonth() + durationMonths);
    
    // Format the date as YYYY-MM-DD
    insurance.value.expiry_date = expiryDate.toISOString().substr(0, 10);
    
    console.log(`Calculated expiry date: ${insurance.value.expiry_date} based on payment date: ${insurance.value.payment_date} and duration: ${durationMonths} months`);
  }
};

// Save insurance to the backend
const saveInsurance = () => {
  if (!insurance.value.client || !insurance.value.plan) {
    $q.notify({
      type: 'negative',
      message: 'Error',
      caption: 'Please select a client and a plan.',
      position: 'bottom-right',
      timeout: 4000,
      progress: true
    });
    return;
  }

  // Prepare the payload by sending only the required IDs and dates
  const insuranceData = {
    client_id: insurance.value.client.id,
    insurance_plan_id: insurance.value.plan.id,
    payment_date: insurance.value.payment_date,
    expiry_date: insurance.value.expiry_date,
  };
  
  console.log('Saving insurance with data:', insuranceData);
  
  axios
    .post("/api/insurance", insuranceData)
    .then((response) => {
      console.log('Insurance saved successfully:', response.data);
      $q.notify({
        type: "positive",
        message: "Success",
        caption: "New insurance saved successfully.",
        position: "bottom-right",
        timeout: 4000,
        progress: true,
      });
      
      // Refresh client data to update status
      refreshClientData(insurance.value.client.id);
      
      emit("insuranceAdded");
      closeModal();
    })
    .catch((error) => {
      console.error("Error saving insurance:", error);
      $q.notify({
        type: "negative",
        message: "Error",
        caption: error.response?.data?.message || "Failed to save insurance.",
        position: "bottom-right",
        timeout: 4000,
        progress: true,
      });
    });
};

// Function to refresh client data after creating insurance
const refreshClientData = (clientId) => {
  axios.get(`/api/clients/${clientId}`)
    .then(response => {
      console.log('Client data refreshed:', response.data);
      // Update the client in the clients array
      const index = clients.value.findIndex(c => c.id === clientId);
      if (index !== -1) {
        clients.value[index] = response.data;
      }
    })
    .catch(error => {
      console.error('Error refreshing client data:', error);
    });
};
</script>
