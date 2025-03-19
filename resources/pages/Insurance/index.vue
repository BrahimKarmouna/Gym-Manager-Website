<template>
  <div class="insurance-container q-pa-md">
    <!-- Header Section -->
    <div class="row justify-between items-center q-mb-md">
      <h4 class="text-h5 q-my-none">Insurance Management</h4>
      <q-btn
        @click="OpenModal"
        color="primary"
        icon="add_circle"
        label="New Insurance"
        class="q-px-md"
      />
    </div>

    <!-- Modal for creating insurance -->
    <CreateForm v-model:visible="is_visible" @insuranceAdded="Refresh" />

    <!-- Search & Filters -->
    <div class="row q-mb-md q-col-gutter-md">
      <div class="col-12 col-sm-6">
        <q-input
          dense
          outlined
          v-model="search"
          placeholder="Search..."
          clearable
        >
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </div>
      <div class="col-12 col-sm-6 row justify-end items-center">
        <q-btn flat round color="primary" icon="refresh" @click="Refresh" />
      </div>
    </div>

    <!-- Insurance Table -->
    <q-card flat bordered>
      <q-table
        :rows="insurances"
        :columns="columns"
        row-key="id"
        :filter="search"
        :loading="loading"
        :pagination="{ rowsPerPage: 10 }"
        flat
      >
        <template v-slot:body-cell-expiry_date="props">
          <q-td :props="props">
            <div class="row items-center">
              <q-chip
                size="sm"
                :color="isExpired(props.value) ? 'negative' : 'positive'"
                text-color="white"
              >
                {{ props.value }}
              </q-chip>
            </div>
          </q-td>
        </template>

        <template v-slot:body-cell-actions="props">
          <q-td :props="props" class="text-center">
            <q-btn-group flat>
              <q-btn flat round color="info" icon="visibility" size="sm" />
              <q-btn
                flat
                round
                color="negative"
                icon="delete"
                size="sm"
                @click="confirmDeleteInsurance(props.row.id)"
              />
            </q-btn-group>
          </q-td>
        </template>

        <template v-slot:no-data>
          <div class="full-width row flex-center q-pa-md text-grey-8">
            No insurance records found
          </div>
        </template>
      </q-table>
    </q-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import CreateForm from "./createForm.vue";
import { useQuasar } from "quasar";
const $q = useQuasar();

// Visibility control for modal
const is_visible = ref(false);

// Open modal for creating insurance
const OpenModal = () => {
  is_visible.value = true;
};

// Ref to hold insurance data
const insurances = ref([]);

// Define columns for the insurance table
const columns = [
  { name: "id", align: "center", label: "ID", field: "id" },
  {
    name: "client_name",
    align: "center",
    label: "Client Name",
    field: (row) => (row.client ? row.client.Full_name : "N/A"),
  },
  {
    name: "plan_name",
    align: "center",
    label: "Plan Name",
    field: (row) => (row.plan ? row.plan.name : "N/A"),
  },
  {
    name: "amount",
    align: "center",
    label: "Amount",
    field: (row) => (row.plan ? row.plan.price : "N/A"),
  },
  {
    name: "insurance_date",
    align: "center",
    label: "Insurance Date",
    field: "payment_date",
  },
  {
    name: "expiry_date",
    align: "center",
    label: "Expiry Date",
    field: "expiry_date",
  },
  { name: "actions", align: "center", label: "Actions", field: "actions" },
];

// Fetch insurance data from the API
const getInsurances = () => {
  axios
    .get("/api/insurance")
    .then((response) => {
      insurances.value = response.data;
    })
    .catch((error) => {
      console.error("Error fetching insurance data:", error);
    });
};

const confirmDeleteInsurance = (id) => {
  $q.dialog({
    title: "Delete Insurance",
    message: "Are you sure you want to delete this insurance?",
    cancel: true,
    persistent: true,
  }).onOk(() => deleteInsurance(id));
};

function deleteInsurance(id) {
  const url = `/api/insurance/${id}`;
  console.log("Deleting insurance with URL:", url);
  $q.notify({
    type: "positive",
    message: "Success",
    caption: "Payment deleted successfully.",
    position: "bottom-right",
    timeout: 4000,
    progress: true,
  });
  axios
    .delete(url) // Ensure no space or extra characters
    .then((response) => {
      console.log("Insurance deleted successfully:", response.data);

      getInsurances(); // Refresh the insurance list
    })
    .catch((error) => {
      console.error(
        "Error deleting insurance:",
        error.response ? error.response.data : error
      );
    });
}

const Refresh = () => {
  getInsurances();
};
// const getInsurancesPlans = () => {
//     axios
//         .get('/api/insurance-plans') // Assumes API returns { clients: [...], plans: [...] }
//         .then((response) => {
//             insurances.value = response.data.clients.flatMap(client =>
//                 client.insurances.map(insurance => ({
//                     id: insurance.id,
//                     client: client,
//                     plan: insurance.plan,
//                     payment_date: insurance.payment_date,
//                     expiry_date: insurance.expiry_date,
//                 }))
//             );
//         })
//         .catch((error) => {
//             console.error('Error fetching insurance data:', error);
//         });
// };

// // Delete insurance based on the ID
// const deleteInsurance = (id) => {
//     axios
//         .delete(`/api/insurance/${id}`)
//         .then(() => {
//             console.log('Insurance deleted successfully.');
//             getInsurances(); // Refresh the insurance list
//         })
//         .catch((error) => {
//             console.error('Error deleting insurance:', error);
//         });
// };

// // Fetch insurances on component mount
// onMounted(() => {
//     getInsurances();
// });
onMounted(() => {
  getInsurances();
});
</script>
