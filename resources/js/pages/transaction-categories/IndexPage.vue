<template>

  <ul>
    <li v-for="transactionCategory in data">

      <router-link :to="{ name: 'transaction-categories.edit', params: { id: transactionCategory.id } }">
        #{{ transactionCategory.id }} -
        {{ transactionCategory.name }}
      </router-link>

      <q-btn flat
             color="primary"
             icon="delete"
             @click="deleteItem(transactionCategory)" />
    </li>
  </ul> -->
  <section class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-16">
    <q-btn :to="{ name: 'transaction-categories.create' }" color="primary" icon="sym_r_add" label="Create" />
  <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
    <div class="mb-4 flex items-center justify-between gap-4 md:mb-8">
      <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Shop by category</h2>

      <a href="#" title="" class="flex items-center text-base font-medium text-primary-700 hover:underline dark:text-primary-500">
        See more categories
        <svg class="ms-1 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
        </svg>
      </a>

    </div>

    <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
      <a
      v-for="transactionCategory in data"
      :key="transactionCategory.id"
      href="#"
      class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
      @click.prevent="$router.push({ name: 'transaction-categories.edit', params: { id: transactionCategory.id } })"
    >
      <svg class="me-2 h-4 w-4 shrink-0 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v5m-3 0h6M4 11h16M5 15h14a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1Z"></path>
      </svg>
      <span class="text-sm font-medium text-gray-900 dark:text-white">
        {{ transactionCategory.name }}
      </span>
      <q-btn flat color="primary" icon="delete" @click.stop="deleteItem(transactionCategory)" />
    </a>

    </div>
  </div>
</section>
</template>

<script setup>
import { api } from '@/boot/axios';
import { useResourceIndex } from '@/composables/useResourceIndex';
import { useQuasar } from 'quasar';
import { onMounted } from 'vue';

const $q = useQuasar();

const {
  data,
  loading,
  filter,
  pagination,
  fetch,
} = useResourceIndex('http://localhost:8000/api/transaction-categories');

onMounted(() => {
  fetch();
});

function deleteItem(transactionCategory) {
  $q.dialog({
    title: 'Confirm',
    message: `Are you sure you want to delete ${transactionCategory.name}?`,
    cancel: true,
    persistent: true,
  }).onOk(() => {
    api.delete(`/transaction-categories/${transactionCategory.id}`).then(() => {
      fetch();
    });
  });

}

</script>
