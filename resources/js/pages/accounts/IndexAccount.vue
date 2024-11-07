<template>

  <CreateAccount v-model:visible="showCreateAccountModal"
                 @created="handleCreated" />

  <q-page :class="{ 'overflow-hidden': loading }"
          :style-fn="(offset) => ({ height: `calc(100vh - ${offset}px)` })">

    <div class="mx-auto p-6">

      <div class="grid w-full grid-cols-1 gap-4 mt-4 sm:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3 mb-5">
        <div
             class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
          <div class="w-full">
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400   ">Accounts number</h3>
            <span class=" pt-4 m-4 mt-3text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ totalAccounts }}</span>
          </div>
          <div class="w-full"
               id="new-products-chart"></div>
        </div>
        <div
             class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
          <div class="w-full">
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Income</h3>
            <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">$2,340</span>
            <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
              <span class="flex items-center mr-1.5 text-sm text-green-500 dark:text-green-400">
                <svg class="w-4 h-4"
                     fill="currentColor"
                     viewBox="0 0 20 20"
                     xmlns="http://www.w3.org/2000/svg"
                     aria-hidden="true">
                  <path clip-rule="evenodd"
                        fill-rule="evenodd"
                        d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z">
                  </path>
                </svg>
                12.5%
              </span>
              Since last month
            </p>
          </div>
          <div class="w-full"
               id="new-products-chart"></div>
        </div>
        <div
             class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
          <div class="w-full">
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Expenses</h3>
            <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">$2,340</span>
            <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
              <span class="flex items-center mr-1.5 text-sm text-green-500 dark:text-green-400">
                <svg class="w-4 h-4"
                     fill="currentColor"
                     viewBox="0 0 20 20"
                     xmlns="http://www.w3.org/2000/svg"
                     aria-hidden="true">
                  <path clip-rule="evenodd"
                        fill-rule="evenodd"
                        d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z">
                  </path>
                </svg>
                3.4%
              </span>
              Since last month
            </p>
          </div>
          <div class="w-full"
               id="week-signups-chart"></div>
        </div>
      </div>


      <!-- 2 columns -->
      <div
           class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">

        <div class="flex items-center justify-between mb-4">
          <div class="flex-shrink-0">
            <span class="text-xl font-bold leading-none text-gray-900 sm:text-2xl dark:text-white">{{ formatter.format(totalBalance) }}</span>
            <h3 class="text-base font-light text-gray-500 dark:text-gray-400">Total accounts Balance</h3>
          </div>
          <div class="flex items-center justify-end flex-1 text-base font-medium text-green-500 dark:text-green-400">
            12.5%
            <svg class="w-5 h-5"
                 fill="currentColor"
                 viewBox="0 0 20 20"
                 xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                    d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"></path>
            </svg>
          </div>
        </div>

        <!-- Card Footer -->
        <div class="flex items-center justify-between pt-3 mt-4 border-t border-gray-200 sm:pt-6 dark:border-gray-700">
          <div>
            <button class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-500 rounded-lg hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                    type="button"
                    data-dropdown-toggle="weekly-sales-dropdown">Last 7 days <svg class="w-4 h-4 ml-2"
                   fill="none"
                   stroke="currentColor"
                   viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 9l-7 7-7-7"></path>
              </svg></button>
            <!-- Dropdown menu -->
            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
                 id="weekly-sales-dropdown">
              <div class="px-4 py-3"
                   role="none">
                <p class="text-sm font-medium text-gray-900 truncate dark:text-white"
                   role="none">
                  Sep 16, 2021 - Sep 22, 2021
                </p>
              </div>
              <ul class="py-1"
                  role="none">
                <li>
                  <a href="#"
                     class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                     role="menuitem">Yesterday</a>
                </li>
                <li>
                  <a href="#"
                     class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                     role="menuitem">Today</a>
                </li>
                <li>
                  <a href="#"
                     class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                     role="menuitem">Last 7 days</a>
                </li>
                <li>
                  <a href="#"
                     class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                     role="menuitem">Last 30 days</a>
                </li>
                <li>
                  <a href="#"
                     class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                     role="menuitem">Last 90 days</a>
                </li>
              </ul>
              <div class="py-1"
                   role="none">
                <a href="#"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                   role="menuitem">Custom...</a>
              </div>
            </div>
          </div>
          <div class="flex-shrink-0">
            <a href="#"
               class="inline-flex items-center p-2 text-xs font-medium  rounded-lg text-primary-700 sm:text-sm hover:bg-gray-100 dark:text-primary-500 dark:hover:bg-gray-700">
              See all transactions
              <svg class="w-4 h-4 ml-1"
                   fill="none"
                   stroke="currentColor"
                   viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>

      <div class=" py-5 bg-gray-100 rounded-xl dark:bg-gray-800">
        <h1 class="font-semibold text-2xl text-gray-700 dark:text-white">Add New Bank Account</h1>

        <div class="grid grid-cols-4 gap-4">


          <!-- <div v-for="account in accounts" :key="account.id" class="bg-white rounded-lg shadow p-4">
          <h2 class="text-xl font-semibold">{{ account.name }}</h2>
          <p>RIB: {{ account.rib }}</p>
          <p>Balance: {{ account.balance }}</p>
          <p>Type: {{ account.account_type }}</p>
        </div> -->
        </div>
      </div>

      <div class="grid relative grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 items-stretch gap-4">
        <q-btn unelevated
               label="+"
               padding="0"
               class="bg-gray-300 text-8xl font-bold text-gray-700 rounded-2xl border"
               @click="showCreateAccountModal = true" />

        <template v-if="loading">

          <section v-for="(_, index) in Array.from({ length: 20 })"
                   :key="index"
                   class="h-full p-4 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-tl from-gray-400 to-gray-600">

            <!-- Header -->
            <header class="flex text-white z-20 mb-4">
              <q-icon name="sym_r_account_balance"
                      size="md" />

              <q-btn icon="sym_r_more_vert"
                     unelevated
                     padding="sm"
                     size="sm"
                     class="ms-auto" />
            </header>

            <!-- Body -->
            <div class="text-gray-100 z-30 mb-4">
              <div class="font-thin">NAME</div>
              <div class="tracking-widest text-xl">
                <q-skeleton type="text" />
              </div>

              <div class="font-thin">RIB</div>
              <div class="tracking-widest text-xl">
                <q-skeleton type="text" />
              </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between text-white flex-nowrap">
              <div>
                <div class="text-md truncate">
                  Balance
                </div>

                <div class="flex w-full">
                  $&nbsp;
                  <q-skeleton type="text"
                              class="grow" />
                </div>
              </div>

              <!-- <q-btn unelevated
         class="bg-green-500 whitespace-nowrap rounded-lg">
    Check it Now
  </q-btn> -->

              <q-skeleton type="QBtn" />

            </div>

          </section>

        </template>

        <template v-else>
          <section v-for="account in accounts"
                   :key="account.id"
                   class="h-full p-4 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-tl from-gray-400 to-gray-600">

            <!-- Header -->
            <header class="flex text-white z-20 mb-4">
              <q-icon name="sym_r_account_balance"
                      size="md" />

              <q-btn icon="sym_r_more_vert"
                     unelevated
                     padding="sm"
                     size="sm"
                     class="ms-auto">
                <q-menu class="p-2 min-w-36"
                        anchor="bottom right"
                        self="top right"
                        :offset="[0, 10]">

                  <q-list dense>
                    <q-item class="rounded-lg"
                            clickable
                            v-close-popup
                            @click="editeItem(account)">
                      <q-item-section class="flex-auto">
                        <q-icon name="sym_r_edit"
                                size="xs" />
                      </q-item-section>

                      <q-item-section>
                        <q-item-label>Edit</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item class="rounded-lg text-negative"
                            clickable
                            v-close-popup
                            @click="deleteAccount(account)">
                      <q-item-section class="flex-auto">
                        <q-icon name="sym_r_delete"
                                size="xs" />
                      </q-item-section>

                      <q-item-section>
                        <q-item-label>Delete</q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </q-menu>
              </q-btn>
            </header>

            <!-- Body -->
            <div class="text-gray-100 z-30 mb-4">
              <div class="font-thin">ACCOUNT NAME</div>
              <div class="tracking-widest font-semibold">{{ account.name }}</div>

              <div class="font-thin">RIB</div>
              <div class="tracking-widest font-semibold">{{ account.rib }}</div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between text-white flex-nowrap">
              <span class="text-md truncate">
                <span>Balance: </span>
                <span>{{ formatter.format(account.balance) }}</span>
              </span>


              <q-btn unelevated
              class="bg-green-500 whitespace-nowrap text-white rounded-lg font-semibold text-xs" @click="checkItem(account)">Check it Now</q-btn>

            </div>
            <span class="text-gray-200 text-sm flex-nowrap">Added {{ account.created_at }}</span>

          </section>
        </template>
      </div>
    </div>
  </q-page>



</template>

<script setup>
import CreateAccount from './CreateAccount.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useMoney } from '@/composables/useMoney';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';
import { api } from '@/boot/axios';

const $q = useQuasar();

const router = useRouter();

const formatter = useMoney('USD');

const showCreateAccountModal = ref(false);
const selectedAccount = ref(null); // Account to be deleted
const confirm = ref(false); // Controls dialog visibility

const accounts = ref([]);
const loading = ref(false);
const totalAccounts = ref(0);
const totalBalance = ref(0);

const deleteAccount = (account) => {
  $q.dialog({
    title: 'Confirm',
    message: `Are you sure you want to delete ${account.name}?`,
    cancel: true,
    persistent: true,
  }).onOk(() => {
    api.delete(`/accounts/${account.id}`).then(() => {
      fetchAccounts();
    });
  });

};

// Fetch accounts on component mount
async function fetchAccounts() {
  try {
    loading.value = true;

    const response = await axios.get('/api/accounts');

    accounts.value = response.data.data;
    totalAccounts.value = response.data.total;
    totalBalance.value = response.data.totalBalance;

  } catch (error) {
    console.error('Error fetching accounts:', error);
  } finally {
    loading.value = false;
  }
};

const checkItem = (account) => {
  router.push({ name: "account.show", params: { id: account.id } });
};

const handleCreated = () => {
  showCreateAccountModal.value = false;
  fetchAccounts();
};

// Fetch accounts when the component mounts
onMounted(fetchAccounts);
</script>

