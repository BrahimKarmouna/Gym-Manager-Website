<template>
  <form @submit.prevent="submitForm">

    <q-input outlined dense v-model="form.fields.name" :error="'name' in form.errors"
      :error-message="form.errors.name?.[0]" />

    <q-btn color="primary" no-caps type="submit" unelevated class="w-full" :loading="form.processing">
      Save
    </q-btn>
  </form>
</template>

<script setup>
import { useForm } from '@/composables/useForm';
import { useRouter } from 'vue-router';

const router = useRouter();

const form = useForm({
  name: '',
});

async function submitForm() {
  await form.post('transaction-categories');

  router.push({ name: 'transaction-categories.index' });
}
</script>
