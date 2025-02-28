<template>
    <div class="q-pa-md q-gutter-sm">
        <q-dialog
            v-model="visible"
            persistent
        >
            <q-card style="min-width: 600px; height: auto;">
                <q-card-section class="row justify-between items-center">
                    <div class="text-h6">Add Insurance</div>
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


                        <q-input
                            v-if="client"
                            :model-value="client.Full_name"
                            label="Client"
                            filled
                            class="col-6 q-mb-md"
                            disable
                        />


                        <!-- Client Selection -->
                        <q-select
                            v-else
                            v-model="insurance.client"
                            :options="clients"
                            label="Select Client"
                            filled
                            class="col-6 q-mb-md"
                            option-value="id"
                            option-label="Full_name"
                        />

                        <!-- Insurance Plan Selection -->
                        <q-select
                            v-model="insurance.plan"
                            :options="plans"
                            label="Select Insurance Plan"
                            filled
                            class="col-6 q-mb-md"
                            option-value="id"
                            option-label="name"
                        />

                        <!-- Payment Date -->
                        <q-input
                            dense
                            v-model="insurance.payment_date"
                            label="Payment Date"
                            type="date"
                            filled
                            class="col-6 q-mb-md"
                        />

                        <!-- Expiry Date -->
                        <q-input
                            dense
                            v-model="insurance.expiry_date"
                            label="Insurance Expiry Date"
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
                        label="Save Insurance"
                        @click="saveInsurance"
                    />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useQuasar } from 'quasar';
import { defineEmits, defineModel, defineProps } from "vue";
const $q = useQuasar();
const emit = defineEmits(["assuranceAdded"]);
const props = defineProps({
    client: Object // Optional: Preselected client
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
        clients.value = response.data;
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
    client: null,   // Ensure client starts as null and gets updated properly
    plan: null,     // Ensure plan starts as null and gets updated properly
    payment_date: new Date().toISOString().substr(0, 10), // Set default payment date
    expiry_date: new Date().toISOString().substr(0, 10),  // Set default expiry date
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
