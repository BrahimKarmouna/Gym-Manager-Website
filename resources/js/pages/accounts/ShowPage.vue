<template>
  <div>
    Hello
    <pre>{{ account }}</pre>

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
})

const account = ref({});

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

onMounted(async () => {
  await getAccount();
})
</script>
