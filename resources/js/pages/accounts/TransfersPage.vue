<template>
  <!-- <q-table flat
           title="Transfer Records"
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
             label="Add Transfer"
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



  <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800 md:p-8">
    <div>
      <h3 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white mt-5">Account Transfers</h3>
      <div v-if="transfers?.length > 0"
           v-for="(transaction, index) in transfers"
           class="flex flex-wrap items-center gap-y-4 border-b border-gray-200 py-4 pb-4 dark:border-gray-700 md:py-5">
        {{ console.log("Transaction: ", transaction) }}
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

        <dl class="w-1/2 sm:w-1/4 md:flex-1 lg:w-auto">
          <dt class="text-base font-medium text-gray-500 dark:text-gray-400">From:</dt>
          <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">{{ transaction.source_account.name }}
          </dd>
        </dl>

        <dl class="w-1/2 sm:w-1/4 md:flex-1 lg:w-auto">
          <dt class="text-base font-medium text-gray-500 dark:text-gray-400">to:</dt>
          <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">{{
            transaction.destination_account.name
            }}
          </dd>
        </dl>
        <dl class="w-1/2 sm:w-1/5 md:flex-1 lg:w-auto">
          <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Price:</dt>
          <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">{{ transaction.amount }} MAD</dd>
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
            {{ transaction.transaction_type?.value }}
          </dd>
        </dl>
      </div>
      <div v-else>No transfers found</div>
    </div>
  </div>


</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useResourceIndex } from '@/composables/useResourceIndex';

const props = defineProps({
  id: {
    type: String,
    required: true
  }
});

const transfers = ref({});

const { data, fetch } = useResourceIndex(() => `accounts/${props.id}/transfers`, {}, {
  onSuccess(resp) {
    console.log("hh: ", resp.data.data);
    transfers.value = resp.data.data;
  }
});

onMounted(() => {
  fetch();
});
</script>
