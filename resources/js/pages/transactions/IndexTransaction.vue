<template>
  <q-page padding>

    <div class="grid w-full grid-cols-2 gap-5 sm:grid-cols-2 xl:grid-cols-4 mb-5">
      <div
           class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="w-full">
          <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Transfer</h3>
          <span
                class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">$1,340</span>
        </div>
      </div>
      <div
           class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="w-full">
          <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Income</h3>
          <span
                class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">$2,340</span>
        </div>
      </div>
      <div
           class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="w-full">
          <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Expense</h3>
          <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">$2,340</span>

        </div>
      </div>
      <div
           class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="w-full">
          <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total</h3>
          <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">$2,340</span>

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

<script>
import { ref } from 'vue';
import { useQuasar } from 'quasar';

export default {
  setup() {
    const $q = useQuasar();
    const loading = ref(false);
    const filter = ref('');
    const rows = ref([]);
    const dialog = ref(false);
    const totalTransfers = ref(0);

    // call Account, transaction_categories
    return {
      tab: ref('Transfer'),
    }
  },
};


</script>
