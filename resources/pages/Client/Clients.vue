<template>
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

  <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

    <div
      class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900"
    >
      <div>
        <q-btn
          class="mr-7"
          @click="openModal"
        >
          <q-icon name="add" />
          <q-tooltip>Add Client</q-tooltip>
        </q-btn>
        <button
          id="dropdownActionButton"
          data-dropdown-toggle="dropdownAction"
          class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
          type="button"
        >
          <span class="sr-only">Action button</span>
          Action
          <svg
            class="w-2.5 h-2.5 ms-2.5"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 10 6"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="m1 1 4 4 4-4"
            />
          </svg>
        </button>
        <!-- Dropdown menu -->
        <div
          id="dropdownAction"
          class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700 dark:divide-gray-600"
        >
          <ul
            class="py-1 text-sm text-gray-700 dark:text-gray-200"
            aria-labelledby="dropdownActionButton"
          >
            <li>
              <a
                href="#"
                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
              >Reward</a>
            </li>
            <li>
              <a
                href="#"
                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
              >Promote</a>
            </li>
            <li>
              <a
                href="#"
                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
              >Activate account</a>
            </li>
          </ul>
          <div class="py-1">
            <a
              href="#"
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
            >Delete User</a>
          </div>
        </div>
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
          >id</th>
          <th
            scope="col"
            class="px-6 py-3"
          >Name</th>

          <th
            scope="col"
            class="px-6 py-3"
          >Phone</th>
          <th
            scope="col"
            class="px-6 py-3"
          >Asserance</th>
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
              <div class="text-base font-semibold">
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
            <div class="flex items-center">
              {{ client.phone }}
              <div class="mb-1">
                <q-icon name="phone_iphone"></q-icon>
              </div>
            </div>
          </td>
          <td class="px-6 py-4">
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
              <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div>
              Non assuré
            </div>



          </td>
          <td class="px-6 py-4">

            <div
              v-if="client.is_payed"
              class="flex items-center


                       "
            >
              <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2">
              </div>
              payee
            </div>

            <div
              class="flex items-center"
              v-else
            >
              <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div>
              non payee
            </div>
          </td>
          <!-- <td class="px-6 py-4">
                        <q-btn
                            icon="edit"
                            @click="updateClient(client)"
                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                        ></q-btn>


                    </td> -->

          <td class="px-6 py-4 relative"> <!-- Add relative positioning to the <td> -->
            <q-btn-dropdown
              dense
              unelevated
            >

              <q-list>
                <q-item
                  clickable
                  v-close-popup
                  @click="onItemClick"
                >
                  <q-btn
                    icon="edit"
                    color="blue"
                    width="100%"
                    background-color="white"
                    @click="updateClient(client)"
                  ></q-btn>
                </q-item>

                <q-item
                  clickable
                  v-close-popup
                  @click="onItemClick"
                >
                  <q-item-section>
                    <q-item-label>
                      <q-btn
                        icon="delete"
                        color="red"
                        width="100%"
                        background-color="white"
                        @click="deleteClient(client.id)"
                      ></q-btn>
                    </q-item-label>
                  </q-item-section>
                </q-item>

                <q-item
                  clickable
                  v-close-popup
                  @click="onItemClick"
                >
                  <q-item-section>
                    <q-item-label>
                      <q-btn
                        icon="visibility"
                        color="blue"
                        width="100%"
                        background-color="white"
                        @click="showClient(client)"
                      ></q-btn>


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
    clients.value = response.data;
    console.log(clients.value);
  })
  .catch((error) => {
    console.error(error);
  });
</script>
