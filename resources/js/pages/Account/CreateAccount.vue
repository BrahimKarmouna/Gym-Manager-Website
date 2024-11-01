<template>
  <q-dialog v-model="visible" persistent>
    <q-card style="width: 450px;">
      <q-card-section>
        <div class="text-h6">Enter Your Account Information</div>
      </q-card-section>

      <q-card-section class="q-mb-md">
        <div class="row q-col-gutter-md">
          <q-input class="col-6" dense label="Account Name" outlined v-model="account.name" autofocus />
          <q-input class="col-6" dense label="Account Balance" outlined type="number" v-model="account.balance" />
          <q-input class="col-12" dense label="Account RIB" outlined v-model="account.rib" />
          <div class="col-12">
            <q-select v-model="account.account_type" :options="accountTypes" label="Account Type" outlined dense />
          </div>
        </div>
      </q-card-section>

      <q-card-section align="right" class="text-primary pt-0">
        <q-btn flat label="Cancel" v-close-popup />
        <q-btn color="primary" label="Add New Account" @click="handleCreate" />
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const emit = defineEmits('created');

const visible = defineModel('visible', { type: Boolean, default: false });

const account = ref({
  name: '',
  balance: null,
  rib: '',
  account_type: ''
});

const accountTypes = [
  { label: "Savings", value: "savings" },
  { label: "Investment", value: "investment" },
  { label: "Credit", value: "credit" },
  { label: "Cash", value: "cash" },
  { label: "Retirement", value: "retirement" },
];

const handleCreate = async () => {
  try {
    await axios.post('/api/accounts', account.value);

    emit('created');

    resetForm();
    visible.value = false;
  } catch (error) {
    console.error('Error creating account:', error.response.data);
  }
};

const resetForm = () => {
  account.value = {
    name: '',
    balance: null,
    rib: '',
    account_type: ''
  };
};
</script>
