<template>
    <q-btn :to="{ name: 'transaction-categories.create' }" color="primary" icon="sym_r_add" label="Create" />

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
  </ul>
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