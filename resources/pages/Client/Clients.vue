<template>
  <div class="bg-gray-100 dark:bg-gray-900 min-h-screen p-6">
    <!-- Modals -->
    <CreateForm v-model:visible="is_visible" />
    <UpdateModal
      v-model:visible="updateModalClient"
      :clients="clients"
      :id="selectedUpdateClientid?.id"
    />
    <ShowModal
      v-model:visible="ShowClient"
      :clients="clients"
      :id="selectedClientid?.id"
    />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto">
      <!-- Header Section -->
      <div class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Clients Management</h1>
        <button 
          @click="openModal"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 shadow-lg hover:shadow-xl transition-all"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          <span>Add New Client</span>
        </button>
      </div>

      <!-- Search and Filter Section -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-wrap gap-4 justify-between items-center">
          <div class="relative flex-1 min-w-[300px]">
            <input
              type="text"
              v-model="search"
              placeholder="Search clients..."
              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
            <svg class="h-5 w-5 text-gray-400 absolute left-3 top-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="flex gap-3">
            <select v-model="filter" class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
              <option value="All">All</option>
              <option value="Assured">Assured</option>
              <option value="Not Assured">Not Assured</option>
            </select>
            <select v-model="paymentFilter" class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
              <option value="All">All</option>
              <option value="Paid">Paid</option>
              <option value="Unpaid">Unpaid</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th scope="col" class="p-4 text-left">
                <input type="checkbox" v-model="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
              </th>
              <th v-for="header in ['ID', 'Client Details', 'Phone', 'Insurance', 'Payment', 'Actions']" 
                  :key="header"
                  class="px-6 py-4 text-sm font-semibold text-gray-700 dark:text-gray-200"
              >
                {{ header }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="client in clients" 
                :key="client.id"
                class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
            >
              <td class="p-4">
                <input type="checkbox" v-model="selectedClients" :value="client.id" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
              </td>
              <td class="px-6 py-4 text-gray-600 dark:text-gray-300">#{{ client.id }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center space-x-4">
                  <img
                    :src="'/storage/' + client.client_picture"
                    class="w-12 h-12 rounded-full object-cover border-2 border-gray-200"
                  />
                  <div>
                    <div class="font-semibold text-gray-800 dark:text-white">{{ client.Full_name }}</div>
                    <div class="text-sm text-gray-500">{{ client.email }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <svg class="h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                  </svg>
                  {{ client.phone }}
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-sm font-medium" :class="client.is_assured ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ client.is_assured ? 'Assured' : 'Not Assured' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-sm font-medium" :class="client.is_payed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ client.is_payed ? 'Paid' : 'Unpaid' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex space-x-2">
                  <button @click="showClient(client)" class="text-blue-600 hover:text-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                      <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  <button @click="updateClient(client)" class="text-yellow-600 hover:text-yellow-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                  </button>
                  <button @click="deleteClient(client.id)" class="text-red-600 hover:text-red-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref } from "vue";
import ShowModal from "./ShowModal.vue";
import UpdateModal from "./EditeForm.vue";
import CreateForm from "./CreateForm.vue";
const is_visible = ref(false);
function openModal() {
  is_visible.value = true;
}
const ShowClient = ref(false);
const updateModalClient = ref(false);

const clients = ref([]);
const selectedClientid = ref();
const selectedUpdateClientid = ref();

function showClient(client) {
  selectedClientid.value = client;
  ShowClient.value = true;
}
function updateClient(client) {
  selectedUpdateClientid.value = client;
  updateModalClient.value = true;
}

// Fetch clients data
axios
  .get("/api/clients")
  .then((response) => {
    clients.value = response.data.clients;
    console.log(clients.value);
  })
  .catch((error) => {
    console.error(error);
  });
</script>
