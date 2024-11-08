<template>
  <q-page padding>


    <div class="grid w-full grid-cols-1 md:grid-cols-3 gap-5 mb-5">
      <div
           class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="w-full">
          <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Income</h3>
          <span
                class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ formatter.format(transactions.incomes) }}</span>
        </div>
      </div>
      <div
           class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="w-full">
          <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Expense</h3>
          <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ formatter.format(transactions.expenses) }}</span>

        </div>
      </div>
      <div
           class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="w-full">
          <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Balance</h3>
          <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ formatter.format(transactions.total_balance) }}</span>

        </div>
      </div>
    </div>

    <q-card>
      <q-tabs v-model="tab"
              class="text-primary dark:text-gray-400"
              active-color="primary dark:text-white"
              indicator-color="primary "
              align="start"             >
        <q-route-tab name="Transfer"
                     :to="{ name: 'transaction.index' }"
                     label="Transfer" />

        <q-route-tab name="Income"
                     :to="{ name: 'transaction.income' }"
                     label="Income" />

        <q-route-tab name="Expense"
                     :to="{ name: 'transaction.expense' }"
                     label="Expense" />
      </q-tabs>

      <q-separator />

      <RouterView />
    </q-card>
  </q-page>
</template>

<script setup>
import { useResourceIndex } from '../../composables/useResourceIndex';
import { onMounted } from 'vue';
import { ref } from 'vue';
import { useMoney } from '../../composables/useMoney';

const tab = ref('Transfer');

const transactions = ref({});

const formatter = useMoney('MAD');

async function fetchTransactions() {
  try {
    const response = await axios.get('/api/transactions/dashboard');

    transactions.value = response.data;

  } catch (error) {
    console.error('Error fetching accounts:', error);
  }
};

onMounted(fetchTransactions);
</script>
