<template>

  <ShowModal
    v-model:visible="ShowClient"
    :clients="clients"
  />

  <!-- <div class="flex space justify-center py-10 px-3 gap-16 h-80  ">
        <div class=" flex w-1/4 border-2 rounded-xl ">
            <div class="w-1/2 h-2/3 bg-red-500 "></div>
            <div class="w-1/2 h-2/3 bg-blue-500 "></div>
            <div class="w-full h-1/3  bg-green-500">f</div>
        </div>
        <div class="flex w-1/4 border-2 rounded-xl ">
            <div class="w-1/2 h-2/3 bg-red-500 "></div>
            <div class="w-1/2 h-2/3 bg-blue-500 "></div>
            <div class="w-full h-1/3  bg-green-500">f</div>
        </div>
        <div class="flex w-1/4 border-2 rounded-xl">
            <div class="w-1/2 h-2/3 bg-red-500 "></div>
            <div class="w-1/2 h-2/3 bg-blue-500 "></div>
            <div class="w-full h-1/3  bg-green-500">f</div>
        </div>
    </div> -->
  <div class="flex justify-center py-10 px-3 gap-10 h-80">
    <!-- Card 1 -->
    <div class="flex flex-col w-1/4 border-2 rounded-xl p-4 shadow-lg bg-white dark:bg-gray-800">
      <div class="flex justify-between">
        <div class="text-gray-800 dark:text-gray-100 text-lg font-semibold">Gross Revenue</div>
        <span class="text-green-600">+2.15%</span>
      </div>
      <div class="text-3xl font-bold text-gray-800 dark:text-gray-100">$2,480.32</div>
      <div class="text-sm text-gray-500">From Jan 01, 2024 - Mar 30, 2024</div>
      <div class="w-full h-10 bg-blue-500 mt-4 rounded"></div>
    </div>

    <!-- Card 2 -->
    <div class="flex flex-col w-1/4 border-2 rounded-xl p-4 shadow-lg bg-white dark:bg-gray-800">
      <div class="flex justify-between">
        <div class="text-gray-800 dark:text-gray-100 text-lg font-semibold">Order Value</div>
        <span class="text-red-600">-2.15%</span>
      </div>
      <div class="text-3xl font-bold text-gray-800 dark:text-gray-100">$56.12</div>
      <div class="text-sm text-gray-500">From Jan 01, 2024 - Mar 30, 2024</div>
      <div class="w-full h-10 bg-red-500 mt-4 rounded"></div>
    </div>

    <!-- Card 3 -->
    <div class="flex flex-col w-1/4 border-2 rounded-xl p-4 shadow-lg bg-white dark:bg-gray-800">
      <div class="flex justify-between">
        <div class="text-gray-800 dark:text-gray-100 text-lg font-semibold">Total Orders</div>
        <span class="text-green-600">+2.15%</span>
      </div>
      <div class="text-3xl font-bold text-gray-800 dark:text-gray-100">$230</div>
      <div class="text-sm text-gray-500">From Jan 01, 2024 - Mar 30, 2024</div>
      <div class="w-full h-10 bg-green-500 mt-4 rounded"></div>
    </div>
  </div>
  <div class="flex space  py-5 px-8 gap-5 w-full justify-center h-64 ">

    <div class="w-1/2  h-72 rounded-xl border-x-black max-h-96 overflow-auto ">
      <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th
              scope="col"
              class="p-4"
            >
              <div class="flex items-center">
                <input
                  id="checkbox-all-search"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                />
                <label
                  for="checkbox-all-search"
                  class="sr-only"
                >checkbox</label>
              </div>
            </th>
            <th
              scope="col"
              class="px-6 py-3"
            >ID</th>
            <th
              scope="col"
              class="px-6 py-3"
            >Name</th>

            <th
              scope="col"
              class="px-6 py-3"
            >Assurance</th>
            <th
              scope="col"
              class="px-6 py-3"
            >Payment</th>
            <th
              scope="col"
              class="px-6 py-3"
            >Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="client in clients"
            :key="client.id"
            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600"
          >
            <td class="w-4 p-4">
              <div class="flex items-center">
                <input
                  id="checkbox-table-search-1"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                />
                <label
                  for="checkbox-table-search-1"
                  class="sr-only"
                >checkbox</label>
              </div>
            </td>
            <td class="px-6 py-4">#{{ client.id }}</td>
            <th
              scope="row"
              class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white"
            >
              <img
                :src="'storage/' + client.client_picture"
                alt="Client Image"
                class="w-12 h-12 rounded-full border border-gray-300 shadow-sm object-cover"
              />
              <div class="ps-3">
                <div class="text-bas
                e font-semibold">
                  <button @click="showClient(client)">
                    {{ client.Full_name }}
                  </button>
                </div>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                  {{ client.name }}
                </div>
                <div class="font-normal text-gray-500">
                  {{ client.email }}
                </div>
              </div>
            </th>


            <td class="px-6 py-4">
              {{ console.log(client) }}
              <div
                class="flex items-center"
                v-if="client.is_assured"
              >
                <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div>
                Assuré
              </div>
              <div
                class="flex items-center"
                v-else
              >
                <div class="h-2.5 w-2.5 rounded-full me-2"></div>
                Non assuré
              </div>
            </td>
            <td class="px-6 py-4">
              <div
                v-if="client.is_payed"
                class="flex items-center"
              >
                <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div>
                Payé
              </div>
              <div
                class="flex items-center"
                v-else
              >
                <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div>
                Non payé
              </div>
            </td>
            <td class="px-6 py-4 relative">
              <!-- Three-dot (Ellipsis) Action Button -->
              <q-btn-dropdown
                dense
                unelevated
              >
                <q-list>
                  <!-- Create Insurance -->
                  <q-item
                    clickable
                    v-close-popup
                    @click="openInsuranceModal(client)"
                    :disable="client.is_assured"
                  >
                    <q-item-section>
                      <q-item-label>
                        Create Insurance
                        <q-icon
                          name="shield"
                          size="sm"
                          class="ms-1"
                        ></q-icon>
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <!-- Create Payment -->
                  <q-item
                    clickable
                    v-close-popup
                    @click="openPaymentModal(client)"
                    :disable="client.is_payed"
                  >
                    <q-item-section>
                      <q-item-label>
                        Create Payment
                        <q-icon
                          name="attach_money"
                          size="sm"
                          class="ms-1"
                        ></q-icon>
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-btn-dropdown>

            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="w-1/3 bg-gray-200 h-72 rounded-xl"></div>
  </div>

  <!-- <div class=" overflow-x-auto shadow-md sm:rounded-lg">
        <div
            class=" flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
            <div class="ml-40">


            </div>
            <label
                for="table-search"
                class="sr-only"
            >Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg
                        class="w-4 h-4 text-gray-500 dark:text-gray-400"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 20 20"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                        />
                    </svg>
                </div>
                <input
                    type="text"
                    id="table-search-users"
                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Search for users"
                />
            </div>
        </div>

    </div> -->
  <!-- Insurance Modal -->
  <InsuranceForm
    v-model:visible="isInsuranceModalVisible"
    :client="selectedClient"
    @insuranceAdded="refreshPayments"
  />
  <!-- Payment Modal -->
  <PaymentForm
    v-model:visible="isPaymentModalVisible"
    :client="selectedClient"
    @new_payment="refreshPayments"
  />
</template>
<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import PaymentForm from '../Payments/createForm.vue';
import InsuranceForm from '../Insurance/createForm.vue';
const isInsuranceModalVisible = ref(false);
const isPaymentModalVisible = ref(false);
const selectedClient = ref(null);
// Open the Insurance Modal
const openInsuranceModal = (client) => {
  selectedClient.value = client;
  isInsuranceModalVisible.value = true;
};

const refreshPayments = () => {
  fetchClients();
};

// Open the Payment Modal
const openPaymentModal = (client) => {
  selectedClient.value = client;
  isPaymentModalVisible.value = true;
};
const clients = ref([]);
const ShowClient = ref(false);
function showClient() {
  ShowClient.value = true;
}
// console.log(clients);
const fetchClients = () => {
  axios
    .get("/api/clients")

    .then(function (response) {
      console.log(response.data);
      clients.value = response.data;
      // handle success
      // console.log(response);
    })
    .catch(function (error) {
      // handle error
      // console.log(error);
    })
    .then(function () {
      // always executed
    });
};
onMounted(() => {
  fetchClients();
});

</script>
