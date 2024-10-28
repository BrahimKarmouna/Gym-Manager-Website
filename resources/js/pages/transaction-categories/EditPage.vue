<template>
  <form @submit.prevent="form.put(`transaction-categories/${props.id}`)">

    <q-input outlined dense v-model="form.fields.name" />

    <q-btn color="primary" no-caps type="submit" unelevated class="w-full rounded-md" :loading="form.processing">
      Save
    </q-btn>
  </form>
</template>

<script setup>
import { useForm } from '@/composables/useForm';
import { useResourceShow } from '@/composables/useResourceShow';
import { watch } from 'vue';

const props = defineProps({
  id: String,
});

const { fetch, loading, record } = useResourceShow(() => `transaction-categories/${props.id}`);

const form = useForm(() => record.value);

watch(() => props.id, (newId) => {
  fetch();
}, { immediate: true });
</script>
