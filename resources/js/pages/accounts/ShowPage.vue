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
              <dl>
                <dt class="font-semibold text-gray-900 dark:text-white">Account RIB:</dt>
                <dd class="text-gray-500 dark:text-gray-400 text-bold ">{{ account.rib }}</dd>
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
              <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Account Balance</h3>
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
              <h3 class="text-base font-normal text-gray-500 dark:text-gray-400"> Account Income</h3>
              <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{
                account.incomes_sum }} MAD</span>
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
              <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Account Expenses</h3>
              <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">
                {{ account.expenses_sum }} MAD </span>
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
        <q-card>
          <q-tabs class="text-primary"
                  active-color="primary"
                  indicator-color="primary"
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

      <!-- Account Information Modal -->



    </section>

    <!--
    <pre>Account: {{ account }}</pre>
    <pre>Transaction Data:  {{ transactionsData }}</pre> -->

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



// const fetchTransactions = async () => {
//   try {
//     loading.value = true;
//     const response = await axios.get(`/api/transactions`);
//     console.log(response.data);
//     transactionsData.value = response.data.data;
//   } catch (error) {
//     console.error(error);
//   } finally {
//     loading.value = false;
//   }
// }

onMounted(async () => {
  await getAccount();
  // await fetchTransactions();
})
</script>
