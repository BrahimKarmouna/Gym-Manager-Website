<template>

  <CreateAccount v-model:visible="showCreateAccountModal"
                 @created="handleCreated" />

  <EditForm v-model:visible="showEditDialog"
            @updated="handleUpdated"
            :id="selectedAccount?.id" />

  <q-page :class="{ 'overflow-hidden': loading }"
          :style-fn="(offset) => ({ height: `calc(100vh - ${offset}px)` })">

    <div class="mx-auto p-3">

      <div class="grid w-full grid-cols-1 gap-4 mt-4 sm:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3 mb-5">
        <div
             class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
          <div class="w-full">
            <q-icon name="sym_r_payments"
                    class="text-blue-600"
                    size="md" />
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Number account</h3>
            <span class=" pt-4 m-4 mt-3text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{
              totalAccounts }}</span>
          </div>
          <div class="w-full"
               id="new-products-chart"></div>
        </div>
        <div
             class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
          <div class="w-full">
            <q-icon name="trending_up"
                    class="text-emerald-600"
                    size="md" />
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Income</h3>
            <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{
              formatter.format(totalIncome) }}</span>
            <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
              <span class="flex items-center mr-1.5 text-sm text-green-500 dark:text-green-400">
                <svg class="w-4 h-4"
                     fill="currentColor"
                     viewBox="0 0 20 20"
                     xmlns="http://www.w3.org/2000/svg"
                     aria-hidden="true">
                </svg>
              </span>
            </p>
          </div>
          <div class="w-full"
               id="new-products-chart"></div>
        </div>
        <div
             class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
          <div class="w-full">
            <q-icon name="trending_down"
                    class="text-red-600"
                    size="md" />
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Expenses</h3>
            <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{
              formatter.format(totalExpense) }}</span>
            <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
              <span class="flex items-center mr-1.5 text-sm text-green-500 dark:text-green-400">
                <svg class="w-4 h-4"
                     fill="currentColor"
                     viewBox="0 0 20 20"
                     xmlns="http://www.w3.org/2000/svg"
                     aria-hidden="true">
                </svg>
              </span>
            </p>
          </div>
          <div class="w-full"
               id="week-signups-chart"></div>
        </div>
      </div>

      <!-- Accounts list -->
      <div class="font-semibold text-md text-2xl text-gray-700 dark:text-white mb-3">
        Add new account :
      </div>

      <!-- bxd -->
      <div class="grid grid-cols-1 relative md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 items-start gap-4 "
           style="grid-auto-rows: 240px !important;">

        <q-btn unelevated
               label="+"
               padding="0"
               class="bg-gray-300 self-stretch text-8xl font-bold text-gray-700 dark:bg-gray-400 rounded-2xl border"
               @click="showCreateAccountModal = true" />

        <template v-if="loading">

          <section v-for="(_, index) in Array.from({ length: 20 })"
                   :key="index"
                   class="p-4 order-last rounded-2xl self-stretch bg-green-500 shadow-lg overflow-hidden bg-gradient-to-tl from-gray-400 to-gray-600">

            <!-- Header -->
            <header class="flex text-white z-20 mb-4">
              <q-icon name="sym_r_account_balance"
                      size="md" />


              <q-skeleton type="QBtn"
                          class="w-10 ms-auto" />

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
              <q-skeleton type="QBtn" />
            </div>
          </section>
        </template>
        <template v-else>
          <section v-for="account in accounts"
                   :key="account.id"
                   class="p-4 rounded-2xl self-stretch shadow-lg overflow-hidden bg-gradient-to-tl from-gray-400 to-gray-600">

            <!-- Header -->
            <header class="flex text-white z-20 mb-4">
              <q-icon name="sym_r_payments"
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
                            @click="editItem(account)">
                      <q-item-section class="flex-auto">
                        <q-icon name="sym_r_edit"
                                size="xs"
                                color="dark: text-gray-400" />
                      </q-item-section>

                      <q-item-section>
                        <q-item-label class="dark: text-gray-400">Edit</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item class="rounded-lg text-negative"
                            clickable
                            v-close-popup
                            @click="deleteAccount(account)">
                      <q-item-section class="flex-auto">
                        <q-icon name="sym_r_delete"
                                size="xs"
                                color="dark: text-red-800" />
                      </q-item-section>

                      <q-item-section>
                        <q-item-label class="dark: text-red-800">Delete</q-item-label>
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

              <div class="font-thin"
                   v-if="account.rib">RIB</div>
              <div class="font-thin"
                   v-else>ACCOUNT TYPE</div>
              <div class="tracking-widest font-semibold">{{ account.rib ?? account.account_type }}</div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between text-white flex-nowrap">
              <span class="text-md truncate">
                <span>Balance: </span>
                <span>{{ formatter.format(account.balance) }}</span>
              </span>
              <q-btn unelevated
                     class="bg-green-500 whitespace-nowrap text-white rounded-lg font-semibold text-xs  dark:bg-blue-900"
                     :to="{ name: 'account.transfers', params: { id: account.id } }">
                Check it Now
              </q-btn>
            </div>
            <span class="text-gray-200 text-sm flex-nowrap">Added {{ account.created_at }}</span>
          </section>
        </template>


        <!-- Latest transactions -->
        <div
             class="flex flex-col flex-nowrap row-span-3 self-stretch md:col-end-3 md:col-span-2 xl:col-end-4 2xl:col-end-5 col-span-1 2xl:col-span-2 2xl:row-end-1 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">

          <div class="flex items-center justify-between mb-2 pl-4 h-28 sm:bg-white">
            <div class="flex-shrink-0 ">
              <span class="text-xl font-bold leading-none text-gray-900 sm:text-2xl dark:text-white">{{
                formatter.format(totalBalance) }}</span>
              <h3 class="text-base font-light text-gray-500 dark:text-gray-400">Total accounts Balance</h3>
            </div>
            <div class="flex items-center justify-end flex-1 text-base font-medium text-green-500 dark:text-green-400">
              <path fill-rule="evenodd"
                    d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"></path>
            </div>
          </div>

          <!-- Card Header -->
          <div class="flex items-center justify-between border-t my-2 border-gray-200 dark:border-gray-700">
            <div>
              <button class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-500 rounded-lg hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                      type="button"
                      data-dropdown-toggle="weekly-sales-dropdown">Last 7 days transfers <svg class="w-4 h-4 ml-2"
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

          </div>

          <!-- Card Body -->
          <div
               class="rounded-lg overflow-auto grow border min-h-0 border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800 md:p-5 sm: p-6 w-full">

            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Last Account transers</h3>
            <div v-if="lastTransfers.length > 0"
                 v-for="transfer in lastTransfers"
                 class="flex flex-wrap items-center gap-y-4 border-b border-gray-200 py-4 pb-4 dark:border-gray-700 md:py-5">
              {{ console.log(transfer.transaction_type) }}
              <dl class="w-1/2 sm:w-48">
                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Transaction ID:</dt>
                <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                  <a href="#"
                     class="hover:underline">#{{ transfer.id }}</a>
                </dd>
              </dl>

              <dl class="w-1/2 sm:w-48">
                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">From:</dt>
                <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                  {{ transfer.source_account?.name ?? "N/A" }}
                </dd>
              </dl>

              <dl class="w-1/2 sm:w-48">
                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">to:</dt>
                <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                  {{ transfer.destination_account?.name ?? "N/A" }}
                </dd>
              </dl>

              <dl class="w-1/2 sm:w-1/4 md:flex-1 lg:w-auto">
                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Date:</dt>
                <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">{{ transfer.date }}</dd>
              </dl>

              <dl class="w-1/2 sm:w-1/5 md:flex-1 lg:w-auto">
                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Amount:</dt>
                <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">${{ transfer.amount }}
                </dd>
              </dl>

              <dl class="w-1/2 sm:w-1/4 sm:flex-1 lg:w-auto">
                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Transaction:</dt>
                <dd :class="{
                  'mt-1.5 inline-flex items-center rounded': true,
                  [`bg-${transfer.transaction_type?.bgColor} dark:bg-${transfer.transaction_type?.bgColor}`]: true,
                  [`text-${transfer.transaction_type?.textColor} dark:text-${transfer.transaction_type?.textColor}`]: true,
                  'px-2.5 py-0.5 text-xs font-medium': true
                }">

                  <q-icon :name="transfer.transaction_type?.icon"
                          class="pr-2" />
                  {{
                    transfer.transaction_type?.value
                  }}
                </dd>
              </dl>

              <dl class="w-1/2 sm:w-1/4 sm:flex-1 lg:w-auto">
                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Note:</dt>
                <dd class="">

                  {{ transfer.note }}
                </dd>
              </dl>
            </div>
            <div v-else>No transfers found</div>
          </div>

          <q-btn :to="{ name: 'transaction.index' }"
                 unelevated
                 label="See all transactions"
                 icon-right="chevron_right"
                 class="rounded-lg sm:text-sm ms-auto mt-3" />
        </div>
      </div>
    </div>
  </q-page>

</template>

<script setup>
import CreateAccount from './CreateAccount.vue';
import EditForm from './EditForm.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useMoney } from '@/composables/useMoney';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';
import { api } from '@/boot/axios';

const $q = useQuasar();

const router = useRouter();

const formatter = useMoney('MAD', 'fr-FR');

const showCreateAccountModal = ref(false);
const showEditDialog = ref(false);
const selectedAccount = ref(null); // A_acount to be deleted
const transactions = ref([]);
const accounts = ref([]);
const loading = ref(false);
const totalAccounts = ref(0);
const totalBalance = ref(0);
const totalExpense = ref(0);
const totalIncome = ref(0);

const lastTransfers = ref({});

async function fetchTransactions() {
  try {
    loading.value = true;

    const response = await axios.get('/api/transactions');

    // Assuming the response contains 'data' and 'lastTransfers'
    transactions.value = response.data.data;  // Regular transactions
    lastTransfers.value = response.data.last_transfers;  // Last transfers from the API
    totalBalance.value = response.data.total_balance;

  } catch (error) {
    console.error('Error fetching transactions:', error);
  } finally {
    loading.value = false;
  }
};

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
    totalExpense.value = response.data.totalExpense;
    totalIncome.value = response.data.totalIncome;

  } catch (error) {
    console.error('Error fetching accounts:', error);
  } finally {
    loading.value = false;
  }
};


const editItem = (account) => {
  selectedAccount.value = account;
  showEditDialog.value = true;
};

const handleCreated = () => {
  showCreateAccountModal.value = false;
  fetchAccounts();
};

const handleUpdated = () => {
  showEditDialog.value = false;
  fetchAccounts();
};

// Fetch accounts when the component mounts
onMounted(() => {
  fetchAccounts();
  fetchTransactions();
});
</script>
