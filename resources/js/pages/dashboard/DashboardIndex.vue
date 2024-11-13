<template>

  <div class="flex-grow text-gray-800">

    <main class="p-4 sm:p-4 space-y-2">
      <div class="flex flex-col  space-y-6 md:space-y-0 md:flex-row ">

      </div>
      <section class="  grid md:grid-cols-2 xl:grid-cols-3 gap-3  ">
        <div class=" h-25 flex items-center p-8 py-5 bg-white shadow rounded-lg dark:bg-gray-800 p   ">
          <div>
            <q-icon name="move_up"
                    class="text-blue-700 ms-5"
                    size="md" />
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 ms-5">Transfers</h3>
            <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white ms-5">{{
              data?.transfers
            }}</span>
          </div>
        </div>
        <div class="h-25 flex items-center p-8 py-5 bg-white shadow rounded-lg dark:bg-gray-800 ">
          <div>
            <q-icon name="trending_up"
                    class="text-emerald-600 ms-5"
                    size="md" />
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 ms-5">Incomes</h3>
            <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white ms-5">{{
              data?.incomes
              }}</span>
          </div>
        </div>
        <div class="h-25 flex items-center p-8 py-5 bg-white shadow rounded-lg dark:bg-gray-800 ">
          <div>
            <q-icon name="trending_down"
                    class="text-red-600 ms-5"
                    size="md" />
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 ms-5">Expenses</h3>
            <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white ms-5">{{
              data?.expenses
            }}</span>
          </div>
        </div>
      </section>

      <div class="flex flex-wrap gap-4  ">
        <incomes class="mr-2" />
        <expenses class=" ml-2" />
      </div>
      {{ console.log(data) }}
      <!-- Card body -->
      <div
           class=" max-h-96 rounded-lg overflow-auto grow border min-h-0 border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 md:p-8 w-3/3 ">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Last 7 days Transactions</h3>
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
            <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">{{ transaction.amount }} MAD
            </dd>
          </dl>

          <dl class="w-1/2 sm:w-1/4 sm:flex-1 lg:w-auto">
            <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Transaction:</dt>
            <dd :class="{
              'mt-1.5 inline-flex items-center rounded': true,
              [`bg-${transaction.transaction_type?.bgColor} dark:bg-${transaction.transaction_type?.bgColor}`]: true,
              [`text-${transaction.transaction_type?.textColor} dark:text-${transaction.transaction_type?.textColor}`]: true,
              'px-2.5 py-0.5 text-xs font-medium': true
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
            <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white"> {{ transaction.note }}</dd>
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
import Incomes from "./Incomes.vue";
import Expenses from "./Expenses.vue";

const transactions = ref([]);
const data = ref(null);
const loading = ref(false);

const fetchTransactions = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/api/transactions');
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
