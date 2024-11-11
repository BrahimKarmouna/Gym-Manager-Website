<template>
  <!-- <q-table flat
           title="Income Records"
           :rows="data ?? []"
           :columns="transferColumns"
           row-key="id"
           :filter="filter"
           :pagination="{ rowsPerPage: 15 }"
           :loading="loadingTransfer"
           @request="onRequest">
    <template v-slot:top>
      <q-btn color="green-8"
             :disable="loading"
             label="Add Income"
             @click="showCreateDialog = true" />

      <q-space />

      <q-input v-model="search"
               filled
               type="search"
               dense>
        <template v-slot:append>
          <q-icon name="search" />
        </template>
</q-input>
</template>

<template v-slot:body-cell-actions="props">
      <q-td v-bind:props="props"
            class="q-gutter-x-xs">
        <q-btn flat
               size="sm"
               padding="sm"
               color="primary"
               icon="edit"
               @click.stop="editRow(props.row)" />

        <q-btn flat
               size="sm"
               padding="sm"
               color="red"
               icon="delete"
               @click.stop="deleteRow(props.row)" />
      </q-td>
    </template>
</q-table> -->


  <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800 md:p-8 w-3/3">
    <div>
      <h3 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white mt-5">Account Incomes</h3>
      <div v-if="data.length > 0"
           v-for="(transaction, index) in data"
           class="flex flex-wrap items-center gap-y-4 border-b border-gray-200 py-4 pb-4 dark:border-gray-700 md:py-5">
        <dl class="w-1/2 sm:w-48">
          <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Transaction ID:</dt>
          <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
            <a href="#"
               class="hover:underline">#{{ transaction.id }}</a>
          </dd>
        </dl>

        <dl class="w-1/2 sm:w-1/4 md:flex-1 lg:w-auto">
          <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Date:</dt>
          <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">{{ transaction.date }}</dd>
        </dl>

        <dl class="w-1/2 sm:w-1/5 md:flex-1 lg:w-auto">
          <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Price:</dt>
          <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">${{ transaction.amount }}</dd>
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

        <div class="w-full sm:flex sm:w-32 sm:items-center sm:justify-end sm:gap-4">
          <button id="actionsMenuDropdownModal11"
                  data-dropdown-toggle="dropdownOrderModal11"
                  type="button"
                  class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 md:w-auto">
            Actions
            <svg class="-me-0.5 ms-1.5 h-4 w-4"
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
                    d="m19 9-7 7-7-7"></path>
            </svg>
          </button>
          <div id="dropdownOrderModal11"
               class="z-10 hidden w-40 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700"
               data-popper-reference-hidden=""
               data-popper-escaped=""
               data-popper-placement="bottom">
            <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400"
                aria-labelledby="actionsMenuDropdown11">
              <li>
                <a href="#"
                   class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                  <svg class="me-1.5 h-4 w-4 text-gray-400 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
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
                          d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4">
                    </path>
                  </svg>
                  <span>Order again</span>
                </a>
              </li>
              <li>
                <a href="#"
                   class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                  <svg class="me-1.5 h-4 w-4 text-gray-400 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                       aria-hidden="true"
                       xmlns="http://www.w3.org/2000/svg"
                       width="24"
                       height="24"
                       fill="none"
                       viewBox="0 0 24 24">
                    <path stroke="currentColor"
                          stroke-width="2"
                          d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"></path>
                    <path stroke="currentColor"
                          stroke-width="2"
                          d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                  </svg>
                  Order details
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div v-else>No incomes found</div>
    </div>
  </div>


</template>

<script setup>
import { onMounted } from 'vue';
import { useResourceIndex } from '@/composables/useResourceIndex';

const props = defineProps({
  id: {
    type: String,
    required: true
  }
});

const { data, fetch } = useResourceIndex(() => `accounts/${props.id}/incomes`);

onMounted(() => {
  fetch();
});
</script>
