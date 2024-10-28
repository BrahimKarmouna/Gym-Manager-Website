<template>
  <form @submit.prevent="form.post('transaction-categories')">

    <q-input outlined dense v-model="form.fields.name" :error="'name' in form.errors"
      :error-message="form.errors.name?.[0]" />

    <q-btn color="primary" no-caps type="submit" unelevated class="w-full rounded-md" :loading="form.processing">
      Save
    </q-btn>
  </form>
</template>

<script setup>
import { useForm } from '@/composables/useForm';

const form = useForm({
  name: '',
}); xs
</script>
