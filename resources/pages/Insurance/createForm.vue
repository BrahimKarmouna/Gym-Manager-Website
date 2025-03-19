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
              label="Select Plan"
              outlined
              bg-color="white"
              option-value="id"
              option-label="name"
              class="rounded-lg"
            >
              <template v-slot:prepend>
                <q-icon name="fitness_center" color="primary" />
              </template>
              <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey"
                    >No plans available</q-item-section
                  >
                </q-item>
              </template>
            </q-select>
          </div>

          <!-- Payment Date -->
          <div>
            <q-input
              v-model="insurance.payment_date"
              label="Payment Date"
              outlined
              bg-color="white"
              readonly
              class="[&_.q-field__native]:cursor-pointer rounded-lg"
            >
              <template v-slot:prepend>
                <q-icon name="event" color="primary" />
              </template>
              <template v-slot:append>
                <q-icon
                  name="arrow_drop_down"
                  color="primary"
                  class="cursor-pointer"
                />
              </template>
              <q-popup-proxy
                cover
                transition-show="scale"
                transition-hide="scale"
              >
                <q-card class="rounded-xl shadow-lg overflow-hidden">
                  <q-date
                    v-model="insurance.payment_date"
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

          <!-- Expiry Date -->
          <div>
            <q-input
              v-model="insurance.expiry_date"
              label="Insurance Expiry Date"
              outlined
              bg-color="white"
              readonly
              class="[&_.q-field__native]:cursor-pointer rounded-lg"
            >
              <template v-slot:prepend>
                <q-icon name="event" color="primary" />
              </template>
              <template v-slot:append>
                <q-icon
                  name="arrow_drop_down"
                  color="primary"
                  class="cursor-pointer"
                />
              </template>
              <q-popup-proxy
                cover
                transition-show="scale"
                transition-hide="scale"
              >
                <q-card class="rounded-xl shadow-lg overflow-hidden">
                  <q-date
                    v-model="insurance.expiry_date"
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
            <div class="text-subtitle2 text-gray-600">Insurance Summary</div>
            <div class="flex justify-between mt-2">
              <span class="text-body2">Plan:</span>
              <span class="text-body2 font-bold">{{
                insurance.plan?.name || "Not selected"
              }}</span>
            </div>
            <div class="flex justify-between mt-1">
              <span class="text-body2">Price:</span>
              <span class="text-body2 font-bold">{{
                insurance.plan?.price ? "$" + insurance.plan.price : "N/A"
              }}</span>
            </div>
          </q-item-section>
        </q-item>
      </q-card-section>

      <q-card-actions align="right" class="p-4">
        <q-btn flat color="grey-7" label="Cancel" @click="closeModal" />
        <q-btn
          unelevated
          color="primary"
          label="Save Insurance"
          @click="saveInsurance"
          :loading="false"
          :disable="!insurance.client || !insurance.plan"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useQuasar } from "quasar";
import { defineEmits, defineModel, defineProps } from "vue";
const $q = useQuasar();
const emit = defineEmits(["assuranceAdded"]);
const props = defineProps({
  client: Object, // Optional: Preselected client
});
// Visibility control
const visible = defineModel("visible", { default: false, type: Boolean });

// Insurance form model

// Clients and plans data
const clients = ref([]);
const plans = ref([]);

// Fetch clients and plans data on component mount
onMounted(() => {
  // Fetch clients
  axios.get("/api/clients").then((response) => {
    clients.value = response.data.clients;
  });

  // Fetch insurance plans
  axios.get("/api/insurance-plans").then((response) => {
    plans.value = response.data;
  });
});

// Close modal and reset form state
const closeModal = () => {
  visible.value = false;
  resetInsurance();
};

// Reset the insurance form
const resetInsurance = () => {
  insurance.value = {
    client: null,
    plan: null,
    payment_date: new Date().toISOString().substr(0, 10),
    expiry_date: new Date().toISOString().substr(0, 10),
  };
};

const insurance = ref({
  client: null, // Ensure client starts as null and gets updated properly
  plan: null, // Ensure plan starts as null and gets updated properly
  payment_date: new Date().toISOString().substr(0, 10), // Set default payment date
  expiry_date: new Date().toISOString().substr(0, 10), // Set default expiry date
});

// Save insurance to the backend
const saveInsurance = () => {
  // Prepare the payload by sending only the required IDs and dates
  const insuranceData = {
    client_id: props.client?.id ?? insurance.value.client.id,
    insurance_plan_id: insurance.value.plan.id,
    payment_date: insurance.value.payment_date,
    expiry_date: insurance.value.expiry_date,
  };
  axios
    .post("/api/insurance", insuranceData)
    .then(() => {
      $q.notify({
        type: "positive",
        message: "Success",
        caption: "New insurance saved successfully.",
        position: "bottom-right",
        timeout: 4000,
        progress: true,
      });
      emit("insuranceAdded");

      closeModal();
    })
    .catch((error) => {
      console.error("Error saving insurance:", error);
    });
};
</script>
