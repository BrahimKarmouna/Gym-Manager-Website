<template>
  <q-dialog v-model="visible"
            persistent>
    <q-card style="width: 450px;">
      <q-card-section>
        <div class="text-h6">Enter Your Account Information</div>
      </q-card-section>

      <q-card-section class="q-mb-md">
        <div class="row q-col-gutter-md">
          <q-input class="col-6"
                   dense
                   label="Account Name"
                   outlined
                   v-model="account.name"
                   autofocus
                   :error="!!validation?.name"
                   :error-message="validation.name?.[0]"
                   hide-bottom-space />
          <q-input class="col-6"
                   dense
                   label="Account Balance"
                   outlined
                   type="number"
                   v-model="account.balance"
                   :error="!!validation?.balance"
                   :error-message="validation.balance?.[0]"
                   hide-bottom-space />
          <q-input class="col-12"
                   dense
                   label="Account RIB"
                   maxlength="24"
                   outlined
                   type="text"
                   v-model="account.rib"
                   :error="!!validation?.rib"
                   :error-message="validation.rib?.[0]"
                   hide-bottom-space />
          <div class="col-12">
            <q-select v-model="account.account_type"
                      :options="accountTypes"
                      label="Account Type"
                      :error="!!validation?.account_type"
                      :error-message="validation.account_type?.[0]"
                      outlined
                      dense
                      hide-bottom-space />
          </div>
        </div>
      </q-card-section>

      <q-card-section align="right"
                      class="text-primary pt-0">
        <q-btn flat
               label="Cancel"
               v-close-popup />
        <q-btn color="primary"
               label="Add New Account"
               @click="handleCreate" />
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { computed, ref } from 'vue';
import axios from 'axios';

const emit = defineEmits('created');

const visible = defineModel('visible', { type: Boolean, default: false });


const account = ref({
  name: '',
  balance: null,
  rib: '',
  account_type: ''
});

const validation = ref({});

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
    if (error.status === 422) {
      validation.value = error.response.data.errors;
      console.log(validation.value);
    }
  }
};

const resetForm = () => {
  account.value = {
    name: '',
    balance: null,
    rib: '',
    account_type: ''
  };

  validation.value = {};
};
</script>
