<template>

  <div class="flex-grow text-gray-800">

    <main class="p-6 sm:p-10 space-y-6">
      <div class="flex flex-col space-y-6 md:space-y-0 md:flex-row justify-between">
        <div class="mr-6">
          <h1 class="text-4xl font-mono text-bold mb-2 text-black dark:text-white">Dashboard</h1>

        </div>

      </div>
      <section class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="flex items-center p-8 bg-white shadow rounded-lg dark:bg-gray-800">

          <svg width="40"
               class="mr-4"
               lns="http://www.w3.org/2000/svg"
               fill="#bec10b"
               viewBox="0 0 24 24"
               stroke="#bec10b">
            <g id="SVGRepo_bgCarrier"
               stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier"
               stroke-linecap="round"
               stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path
                    d="M3.8 9.2c0-.9 1-2.6 2.3-3.1s3.3-.5 6-.5c2.9 0 5.8.5 5.8.5l-2.3-2.3 1-1.9L22 7.4l-5.5 5.5-1-1.9 2.3-2.3s-2.9-.5-5.9-.5c-2.6 0-4.6 0-5.9.5s-2.2 3.7-2.2 3.7V9.2z">
              </path>
              <path
                    d="M20.2 14.8c0 .9-1 2.6-2.3 3.1s-3.3.5-6 .5c-2.9 0-5.8-.5-5.8-.5l2.3 2.3-1 1.9L2 16.6l5.5-5.5 1 1.9-2.3 2.3s2.9.5 5.9.5c2.6 0 4.6 0 5.9-.5s2.2-3.7 2.2-3.7v3.2z">
              </path>
            </g>
          </svg>







          <div>
            <span class="block text-bold text-2xl font-mono dark:text-gray-400">Transfers</span>
            <span class="block text-center text-2xl font-bold  dark:text-white">{{ data.transfers }}</span>

          </div>
        </div>
        <div class="flex items-center p-8 bg-white shadow rounded-lg dark:bg-gray-800">

          <svg class=" mr-5 "
               width="60"
               xmlns="http://www.w3.org/2000/svg"
               viewBox="0 0 64 64"
               id="income">
            <path fill="#785d55"
                  d="M2,42V38a2.006,2.006,0,0,1,2-2H20a2.006,2.006,0,0,1,2,2v2Z"></path>
            <path fill="#644e48"
                  d="M20,39H4a2.006,2.006,0,0,0-2,2v1l19.735-1.974A2,2,0,0,0,20,39Z"></path>
            <path fill="#e3a22d"
                  d="M39,28a9,9,0,1,1-9-9,9.034,9.034,0,0,1,8.82,7.18A9.134,9.134,0,0,1,39,28Z"></path>
            <circle cx="30"
                    cy="28"
                    r="7"
                    fill="#f9d157"></circle>
            <path fill="#e3a22d"
                  d="M30,27a1,1,0,1,1,1-1h2a3,3,0,0,0-2-2.816V22H29v1.184A2.993,2.993,0,0,0,30,29a1,1,0,1,1-1,1H27a3,3,0,0,0,2,2.816V34h2V32.816A2.993,2.993,0,0,0,30,27Z">
            </path>
            <rect width="28"
                  height="22"
                  x="2"
                  y="40"
                  fill="#785d55"
                  rx="2"></rect>
            <path fill="#876e65"
                  d="M28,40H4V58a2.006,2.006,0,0,0,2,2H30V42A2.006,2.006,0,0,0,28,40Z"></path>
            <path fill="#644e48"
                  d="M30,48H21a3,3,0,0,0-3,3h0a3,3,0,0,0,3,3h9"></path>
            <rect width="8"
                  height="16"
                  x="54"
                  y="2"
                  fill="#a9b0b6"></rect>
            <path fill="#468f43"
                  d="M58,36v5A13,13,0,0,1,45,54h0v3L35,51l10-6v3h0a7,7,0,0,0,7-7V36Z"></path>
            <path fill="#e1b1a9"
                  d="M54,3V16H51a10.973,10.973,0,0,1-7.18,6.48L42,23l-3.18,3.18a2.828,2.828,0,0,1-3.99-4.01L39,18l-1-4-5-1-3,3v3a9,9,0,0,0-6,2.29V14a5.22,5.22,0,0,1,1.33-3.49l5.34-6.02A4.466,4.466,0,0,1,34,3Z">
            </path>
            <path fill="#ffe1d4"
                  d="M30.67,6.49l-5.34,6.02A5.22,5.22,0,0,0,24,16v5.29A9,9,0,0,1,30,19V16l3-3,5,1,1,4-4.17,4.17a2.828,2.828,0,0,0,3.99,4.01L42,23l1.82-.52A10.973,10.973,0,0,0,51,16h3V5H34A4.466,4.466,0,0,0,30.67,6.49Z">
            </path>
            <rect width="8"
                  height="13"
                  x="54"
                  y="5"
                  fill="#c9cacc"></rect>
            <path fill="#58b15e"
                  d="M55,36v5A10,10,0,0,1,45,51H35l10,6V54A13,13,0,0,0,58,41V36Z"></path>
            <path
                  d="M4,63H28a3,3,0,0,0,3-3V42a3,3,0,0,0-3-3H23V38a3,3,0,0,0-3-3H4a3,3,0,0,0-3,3V60A3,3,0,0,0,4,63ZM29,53H21a2,2,0,0,1,0-4h8ZM3,38a1,1,0,0,1,1-1H20a1,1,0,0,1,1,1v1H4a3,3,0,0,0-1,.171Zm0,4a1,1,0,0,1,1-1H28a1,1,0,0,1,1,1v5H21a4,4,0,0,0,0,8h8v5a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1Z">
            </path>
            <path
                  d="M62,1H54a1,1,0,0,0-1,1H34a5.456,5.456,0,0,0-4.076,1.831l-5.342,6.01A6.252,6.252,0,0,0,23,14v6.871A10,10,0,1,0,40,28a10.123,10.123,0,0,0-.12-1.466l2.643-2.644,1.574-.449A12.068,12.068,0,0,0,51.655,17H53v1a1,1,0,0,0,1,1h8a1,1,0,0,0,1-1V2A1,1,0,0,0,62,1ZM30,36a8,8,0,1,1,4.314-14.728l-.193.193A3.809,3.809,0,0,0,37.99,27.8c0,.066.01.131.01.2A8.009,8.009,0,0,1,30,36Zm5.759-16.173A10,10,0,0,0,31,18.055V16.414l2.329-2.329,3.855.771.709,2.837ZM51,15a1,1,0,0,0-.929.628,10.043,10.043,0,0,1-6.523,5.89l-1.822.521a.992.992,0,0,0-.433.254l-3.172,3.172A1.829,1.829,0,0,1,35,24.171a1.819,1.819,0,0,1,.535-1.293l4.172-4.171a1,1,0,0,0,.263-.95l-1-4a1,1,0,0,0-.774-.737l-5-1a.994.994,0,0,0-.9.273l-3,3A1,1,0,0,0,29,16v2.051a9.9,9.9,0,0,0-4,1.3V14a4.259,4.259,0,0,1,1.076-2.831l5.342-6.01A3.458,3.458,0,0,1,34,4H53V15Zm10,2H55V3h6Z">
            </path>
            <path
                  d="M30 25a1 1 0 0 1 1 1h2a3 3 0 0 0-2-2.816V22H29v1.184A2.993 2.993 0 0 0 30 29a1 1 0 1 1-1 1H27a3 3 0 0 0 2 2.816V34h2V32.816A2.993 2.993 0 0 0 30 27a1 1 0 0 1 0-2zM45.493 44.13a1 1 0 0 0-1.008.013l-10 6a1 1 0 0 0 0 1.714l10 6A1 1 0 0 0 46 57V54.965A14.019 14.019 0 0 0 59 41V36a1 1 0 0 0-1-1H52a1 1 0 0 0-1 1v5a6.01 6.01 0 0 1-5 5.917V45A1 1 0 0 0 45.493 44.13zM53 41V37h4v4A12.013 12.013 0 0 1 45 53a1 1 0 0 0-1 1v1.234L36.943 51 44 46.766V48a1 1 0 0 0 1 1A8.009 8.009 0 0 0 53 41z">
            </path>
          </svg>


          <div>
            <span class="block text-bold text-2xl font-mono dark:text-gray-400">Income</span>
            <span class="block text-center text-2xl font-bold  dark:text-white">{{ data.incomes }}</span>

          </div>
        </div>
        <div class="flex items-center p-8 bg-white shadow rounded-lg dark:bg-gray-800">

          <svg aria-hidden="true"
               fill="none"
               viewBox="0 0 24 24"
               stroke="red"
               class="h-7 w-10 mr-4">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
          </svg>

          <div>
            <span class="block text-bold text-2xl font-mono dark:text-gray-400">Expense</span>
            <span class="block text-center text-2xl font-bold  dark:text-white">{{ data.expenses }}</span>


          </div>
        </div>










      </section>

      <!-- Card Body -->
      <div
           class="rounded-lg overflow-auto grow border min-h-0 border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800 md:p-8 w-3/3">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Last Transactions</h3>
        <div v-if="transactions.length > 0"
             v-for="transaction in transactions"
             class="flex flex-wrap items-center gap-y-4 border-b border-gray-200 py-4 pb-4 dark:border-gray-700 md:py-5">

          <dl class="w-1/2 sm:w-48">
            <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Transaction ID:</dt>
            <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
              <a href="#"
                 class="hover:underline">#{{ transaction.id }}</a>
            </dd>
          </dl>

          <dl class="w-1/2 sm:w-48">
            <dt class="text-base font-medium text-gray-500 dark:text-gray-400">From:</dt>
            <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
              {{ transaction.sourceAccount?.name ?? "N/A" }}
            </dd>
          </dl>

          <dl class="w-1/2 sm:w-48">
            <dt class="text-base font-medium text-gray-500 dark:text-gray-400">to:</dt>
            <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
              {{ transaction.destinationAccount?.name ?? "N/A" }}
            </dd>
          </dl>

          <dl class="w-1/2 sm:w-1/4 md:flex-1 lg:w-auto">
            <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Date:</dt>
            <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">{{ transaction.date }}</dd>
          </dl>

          <dl class="w-1/2 sm:w-1/5 md:flex-1 lg:w-auto">
            <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Amount:</dt>
            <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">${{ transaction.amount }}
            </dd>
          </dl>

          <dl class="w-1/2 sm:w-1/4 sm:flex-1 lg:w-auto">
            <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Transaction:</dt>
            <dd :class="{
              'mt-1.5 inline-flex items-center rounded': true,
              [`bg-${transaction.transaction_type?.bgColor} dark:bg-${transaction.transaction_type?.textColor}`]: true,
              [`text-${transaction.transaction_type?.textColor} dark:text-${transaction.transaction_type?.textColor}`]: true,
              'px-2.5 py-0.5 text-xs font-medium dark:bg-red-900 dark:text-red-300': true
            }">
              <q-icon :name="transaction.transaction_type?.icon"
                      class="pr-2" />
              {{
                transaction.transaction_type?.value
              }}
            </dd>
          </dl>

          <dl class="w-1/2 sm:w-1/4 sm:flex-1 lg:w-auto">
            <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Note:</dt>
            <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">


              {{ transaction.note }}
            </dd>
          </dl>

        </div>
        <div v-else>No incomes found</div>
      </div>


    </main>
  </div>


</template>
<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";
import { useFetch } from "../../composables/useFetch.js";

const transactions = ref([]);
const accounts = ref([]);
const data = ref(null);
const loading = ref(false);

const fetchTransactions = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/api/transactions');
    // transactions.value = response.data.data;
  } catch (error) {
    console.error('Error fetching transactions:', error);
  } finally {
    loading.value = false;
  }
};

const { execute: fetchData } = useFetch({
  config: {
    url: 'dashboard'
  },
  onSuccess: (response) => {
    data.value = response.data;
    transactions.value = response.data.last_transactions
    console.log("transactions: ", response.data.last_transactions);
  },
  onError: (err) => {
    console.log({ err });
  }
});

onMounted(() => {
  fetchData();
  fetchTransactions();
});
</script>
