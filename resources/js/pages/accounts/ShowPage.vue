<template>
  <div>
    <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-8">
      <div class="mx-auto max-w-screen-2xl px-4 3xl:px-0">
        <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl md:mb-6">Account overview</h2>
        <div class="py-4 md:py-8">
          <div class="mb-4 grid gap-4 sm:grid-cols-2 sm:gap-8 lg:gap-16">
            <div class="flex space-y-4 space-x-4">
              <img class="h-20 w-20 rounded-full bg-gray-200 "
                   src="https://i.ibb.co/HFmGNjx/Design-sans-titre.png"
                   alt="Helene avatar" />
              <div>
                <span
                      class="mb-2 inline-block rounded bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800 dark:bg-primary-900 dark:text-primary-300">
                  Account Name </span>
                <h2 class="flex items-center text-xl font-bold leading-none text-gray-900 dark:text-white sm:text-2xl">
                  {{ account.name }}</h2>
              </div>
            </div>
            <div class="space-y-4">
              <dl v-if="account.rib">
                <dt class="font-semibold text-gray-900 dark:text-white">Account RIB:</dt>
                <dd class="text-gray-500 dark:text-gray-400 text-bold">{{ account.rib }}</dd>
              </dl>

              <dl>
                <dt class="font-semibold text-gray-900 dark:text-white"> Account Type:</dt>
                <dd class="flex items-center gap-1 text-gray-500 dark:text-gray-400">
                  {{ account.account_type }}
                </dd>
              </dl>
              <dl>
                <dt class="font-semibold text-gray-900 dark:text-white">Created AT: </dt>
                <dd class="flex items-center gap-1 text-gray-500 dark:text-gray-400">
                  {{ account.created_at }}
                </dd>
              </dl>
            </div>
          </div>
        </div>

        <!-- <q-dialog v-model="prompt"
                  persistent>
          <q-card style="min-width: 350px">
            <q-card-section>
              <div class="text-h6">Your address</div>
            </q-card-section>

            <q-card-section class="q-pt-none">
              <q-input dense
                       v-model="address"
                       autofocus
                       @keyup.enter="prompt = false" />
            </q-card-section>

            <q-card-actions align="right"
                            class="text-primary">
              <q-btn flat
                     label="Cancel"
                     v-close-popup />
              <q-btn flat
                     label="Add address"
                     v-close-popup />
            </q-card-actions>
          </q-card>
        </q-dialog> -->
        <div class="grid w-full grid-cols-1 gap-4 mt-4 sm:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3 mb-5">
          <div
               class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="w-full">
              <q-icon name="account_balance_wallet"
                      class="text-blue-600"
                      size="md" />
              <h3 class="text-base font-normal text-gray-800 dark:text-gray-400">Account Balance</h3>
              <span class=" pt-4 m-4 mt-3text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">
                {{ account.balance }} MAD
              </span>
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
              <h3 class="text-base font-normal text-gray-500 dark:text-gray-400"> Account Income</h3>
              <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{
                account.incomes_sum }} MAD</span>
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
              <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Account Expenses</h3>
              <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">
                {{ account.expenses_sum }} MAD </span>

            </div>
            <div class="w-full"
                 id="week-signups-chart"></div>
          </div>
        </div>
        <q-card>
          <q-tabs class="text-primary dark:text-gray-400"
                  active-color="primary dark:text-white"
                  indicator-color="primary dark:gray-700"
                  align="justify">
            <q-route-tab name="Transfer"
                         :to="{ name: 'account.transfers' }"
                         label="Transfer" />

            <q-route-tab name="Income"
                         :to="{ name: 'account.incomes' }"
                         label="Income" />

            <q-route-tab name="Expense"
                         :to="{ name: 'account.expenses' }"
                         label="Expense" />
          </q-tabs>
          <q-separator />
          <RouterView />
        </q-card>
      </div>
    </section>
    <div v-if="loading">Loading...</div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';

const props = defineProps({
  id: {
    type: [Number, String]
  }
});

const account = ref({});
const transactionsData = ref([]);
const loading = ref(true);

const getAccount = async () => {
  try {
    loading.value = true;
    const response = await axios.get(`/api/accounts/${props.id}`);
    account.value = response.data.data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await getAccount();
  // await fetchTransactions();
})
</script>
