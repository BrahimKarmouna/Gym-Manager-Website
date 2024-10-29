<template>
  <q-dialog v-model="showCreateDialog" persistent>
    <q-card style="min-width: 350px">
      <q-card-section>
        <div class="flex justify-between items-center">
          <div class="text-h6">
            {{ isEditing ? 'Edit Category' : 'Create Category' }}
          </div>
          <q-btn flat size="sm" padding="xs" dense icon="sym_r_close" v-close-popup />
        </div>
      </q-card-section>

      <q-card-section class="q-pt-none">
        <div class="flex flex-col gap-3">
          <q-input dense :error="'name' in createForm.errors" hide-bottom-space label="Name"
            :error-message="createForm.errors.name?.[0]" v-model="createForm.fields.name" autofocus>
            <template v-if="createForm.fields.emoji" v-slot:prepend>
              <img
                :src="`https://cdn.jsdelivr.net/npm/emoji-datasource-apple@6.0.1/img/apple/64/${createForm.fields.emoji}.png`"
                alt="" style="width: 20px;">
            </template>
            <template v-slot:append>
              <q-icon name="mood" class="cursor-pointer">
                <q-popup-proxy cover :breakpoint="600">
                  <EmojiPicker :native="false" @select="createForm.fields.emoji = $event.u" />
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
        </div>
      </q-card-section>

      <q-card-actions align="right" class="text-primary">
        <q-btn flat label="Cancel" @click="resetForm" />
        <q-btn flat label="Save" @click="isEditing ? updateItem() : createItem()" :loading="createForm.processing" />
      </q-card-actions>
    </q-card>
  </q-dialog>

  <section class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-16">
    <q-btn :to="{ name: 'transaction-categories.create' }" color="primary" icon="sym_r_add" label="Create" />
    <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
      <div class="mb-4 flex items-center justify-between gap-4 md:mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Shop by category</h2>

        <a href="#" title=""
          class="flex items-center text-base font-medium text-primary-700 hover:underline dark:text-primary-500">
          See more categories
          <svg class="ms-1 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 12H5m14 0-4 4m4-4-4-4" />
          </svg>
        </a>

      </div>

      <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <a v-for="transactionCategory in data" :key="transactionCategory.id" href="#"
          class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
          @click.prevent="$router.push({ name: 'transaction-categories.edit', params: { id: transactionCategory.id } })">
          <!-- <svg class="me-2 h-4 w-4 shrink-0 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v5m-3 0h6M4 11h16M5 15h14a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1Z"></path>
      </svg> -->

          <img v-if="transactionCategory.emoji"
            :src="`https://cdn.jsdelivr.net/npm/emoji-datasource-apple@6.0.1/img/apple/64/${transactionCategory.emoji}.png`"
            class="me-2 w-6">

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
import { useForm } from '@/composables/useForm';
import { useResourceIndex } from '@/composables/useResourceIndex';
import { useQuasar } from 'quasar';
import { onMounted, ref } from 'vue';
import EmojiPicker from 'vue3-emoji-picker';

import 'vue3-emoji-picker/css';

const $q = useQuasar();

const showCreateDialog = ref(false);
const isEditing = ref(false);

const createForm = useForm({
  name: '',
  emoji: '',
  id: null, // Initialize id to null
});

const {
  data,
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

function editeItem(transactionCategory) {
  createForm.fields.name = transactionCategory.name;
  createForm.fields.emoji = transactionCategory.emoji;
  createForm.fields.id = transactionCategory.id; // Set the ID for updates
  isEditing.value = true;
  showCreateDialog.value = true;
}

function updateItem() {
  console.log("Updating with data:", createForm.fields);

  api.put(`/transaction-categories/${createForm.fields.id}`, createForm.fields)
    .then(response => {
      console.log("Update response:", response.data);
      resetForm();
      fetch();
    })
    .catch(error => {
      console.error("Update failed:", error.response ? error.response.data : error.message);
    });
}

function createItem() {
  createForm.post('/transaction-categories').then(() => {
    resetForm();
    fetch();
  });
}

function openCreateModal() {
  resetForm();
  showCreateDialog.value = true;
}

function resetForm() {
  createForm.reset();
  createForm.clearErrors();
  createForm.fields.id = null; // Reset the ID
  isEditing.value = false;
  showCreateDialog.value = false;
}
</script>
