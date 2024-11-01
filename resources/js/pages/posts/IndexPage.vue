<template>
  <div>
    <q-btn label="Add Account" @click="visible = true" />

    <q-dialog v-model="visible" persistent>
      <q-card style="height: 600px; width: 300px;">
        <q-card-section>
          <div class="text-h6">Enter Your Account Information</div>
        </q-card-section>

        <q-card-section class="q-mb-md">
          <div class="row q-col-gutter-md">
            <q-input
              class="col-6"
              dense
              label="Account Name"
              outlined
              v-model="account.name"
              autofocus
            />
            <q-input
              class="col-6"
              dense
              label="Account Balance"
              outlined
              type="number"
              v-model="account.balance"
            />
            <q-input
              class="col-12"
              dense
              label="Account RIB"
              outlined
              v-model="account.rib"
            />
            <div class="col-12">
              <q-select
                v-model="account.type"
                :options="accountTypes"
                label="Account Type"
                outlined
                dense
              />
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn color="primary" label="Add New Account" @click="handleCreate" />
        </q-card-actions>
      </q-card>

      <q-banner v-if="successMessage" class="q-mt-md" color="green" text-color="white">
        {{ successMessage }}
      </q-banner>
      <q-banner v-if="errorMessage" class="q-mt-md" color="red" text-color="white">
        {{ errorMessage }}
      </q-banner>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const visible = ref(false);
const account = ref({
  name: '',
  balance: null,
  rib: '',
  type: ''
});

const accountTypes = [
  { label: "Savings", value: "savings" },
  { label: "Investment", value: "investment" },
  { label: "Credit", value: "credit" },
  { label: "Cash", value: "cash" },
  { label: "Retirement", value: "retirement" },
];

const successMessage = ref('');
const errorMessage = ref('');

const handleCreate = async () => {
  try {
    const response = await axios.post('/api/accounts', account.value);
    console.log('Account created:', response.data);
    // Reset the form
    account.value = {
      name: '',
      balance: null,
      rib: '',
      type: ''
    };
    successMessage.value = 'Account successfully created!';
    errorMessage.value = '';
    visible.value = false; // Close the dialog
  } catch (error) {
    console.error('Error creating account:', error.response.data);
    errorMessage.value = 'Failed to create account. Please check your inputs.';
    successMessage.value = '';
  }
};
</script>
