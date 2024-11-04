<template>
  <form @submit.prevent="submitForm">

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
import { useRouter } from 'vue-router';

const props = defineProps({
  id: String,
});

const { fetch, loading, record } = useResourceShow(() => `categories/${props.id}`);

const form = useForm(() => record.value);

const router = useRouter();

async function submitForm() {
  await form.put(`transaction-categories/${props.id}`);

  router.push({ name: 'transaction-categories.index' });
}

watch(() => props.id, (newId) => {
  fetch();
}, { immediate: true });
</script>
