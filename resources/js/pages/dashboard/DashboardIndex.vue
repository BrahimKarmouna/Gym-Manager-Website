<template>

  <div class="flex-grow text-gray-800">

    <main class="p-6 sm:p-10 space-y-6">
      <div class="flex flex-col space-y-6 md:space-y-0 md:flex-row justify-between">
        <div class="mr-6">
          <h1 class="text-4xl font-semibold mb-2 text-black dark:text-white">Dashboard</h1>

        </div>

      </div>
      <section class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="flex items-center p-8 bg-white shadow rounded-lg dark:bg-gray-800">

          <svg aria-hidden="true"
               fill="none"
               viewBox="0 0 24 24"
               stroke="blue"
               class="h-7 w-10 mr-4">
            <path stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>

          <div>
            <span class="block text-2xl font-bold dark:text-white">{{ data.transfers }}</span>
            <span class="block text-gray-500 dark:text-gray-400">Transfers</span>
          </div>
        </div>
        <div class="flex items-center p-8 bg-white shadow rounded-lg dark:bg-gray-800">

          <svg aria-hidden="true"
               fill="none"
               viewBox="0 0 24 24"
               stroke="green"
               class="h-7 w-10 mr-4">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
          </svg>

          <div>
            <span class="block text-2xl font-bold  dark:text-white">{{ data.incomes }}</span>
            <span class="block  dark:text-gray-400">Income</span>
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
            <span class="inline-block text-2xl font-bold dark:text-white">{{ data.expenses }}</span>

            <span class="block text-gray-400">Expense</span>
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
              {{ transaction.source_account?.name ?? "N/A" }}
            </dd>
          </dl>

          <dl class="w-1/2 sm:w-48">
            <dt class="text-base font-medium text-gray-500 dark:text-gray-400">to:</dt>
            <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
              {{ transaction.destination_account?.name ?? "N/A" }}
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
            <dd
                class="mt-1.5 inline-flex items-center rounded bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">
              <svg class="me-1 h-3 w-3"
                   aria-hidden="true"
                   xmlns="http://www.w3.org/2000/svg"
                   width="24"
                   height="24"
                   fill="none"
                   viewBox="0 0 24 24">
                <path stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18 17.94 6M18 18 6.06 6"></path>
              </svg>
              {{ transaction.transaction_type }}
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
    transactions.value = response.data.data;
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
