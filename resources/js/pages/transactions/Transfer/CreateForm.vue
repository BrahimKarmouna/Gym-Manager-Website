<template>

  <q-dialog v-model="visible"
            persistent>
    <q-card>
      <q-card-section>
        <q-form @submit.prevent="onSubmit"
                @reset="onReset"
                class="q-gutter-md">

          <q-input label="Date"
                   v-model="data.date"
                   hide-bottom-space
                   required
                   :error="!!validation?.date"
                   :error-message="validation.date?.[0]"
                   mask="date">
            <template v-slot:append>
              <q-icon name="event"
                      class="cursor-pointer">
                <q-popup-proxy cover
                               transition-show="scale"
                               transition-hide="scale">
                  <q-date v-model="data.date">
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup
                             label="Close"
                             color="primary"
                             flat />
                    </div>
                  </q-date>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>

          <q-input style="width: 430px;"
                   v-model="data.amount"
                   type="number"
                   label="Amount"
                   :error="!!validation?.amount"
                   :error-message="validation.amount?.[0]"
                   lazy-rules
                   hide-bottom-space />

          <!-- Select  From Account -->
          <q-select v-model="data.source_account"
                    :options="accounts"
                    option-value="id"
                    option-label="name"
                    label="From"
                    :error="!!validation?.source_account_id"
                   :error-message="validation.source_account_id?.[0]"
                   hide-bottom-space />

          <!-- Select To Account -->
          <q-select v-model="data.destination_account"
                    :options="accounts"
                    option-value="id"
                    option-label="name"
                    label="To"
                    :error="!!validation?.destination_account_id"
                   :error-message="validation.destination_account_id?.[0]"
                   hide-bottom-space />

          <!-- Select a category -->
          <q-select v-model="data.category"
                    :options="transaction_categories"
                    option-value="id"
                    option-label="name"
                    label="Category"
                    :error="!!validation?.category_id"
                   :error-message="validation.category_id?.[0]"
                   hide-bottom-space />


          <q-input type="text"
                   v-model="data.note"
                   label="Note"
                   lazy-rules
                   :error="!!validation?.note"
                   :error-message="validation.note?.[0]"
                   hide-bottom-space />
          <div>
            <q-btn label="Submit"
                   type="submit"
                   color="green-7" />
            <q-btn label="Reset"
                   type="reset"
                   color="primary"
                   flat
                   class="q-ml-sm" />
          </div>
        </q-form>
      </q-card-section>
      <q-card-actions>
        <q-btn label="Close"
               @click="visible = false"
               color="red"
               flat />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { useResourceIndex } from '@/composables/useResourceIndex';
import { api } from '@/boot/axios';
import { onMounted, ref } from 'vue';

const emit = defineEmits(['created']);

const visible = defineModel('visible', { type: Boolean, default: false });

const loading = ref(false);

const data = ref({
  'transaction_type': {
    'label': 'Transfer',
    'value': 'transfer',
  },
});

const validation = ref({});

const addRow = async () => {
  try {
    loading.value = true;

    await api.post('transactions', data.value).then((response) => {

      $q.notify({
        type: 'positive',
        message: 'Success!',
        caption: 'Transfer added successfully.',
        position: 'bottom-right',
        timeout: 3000
      });

      rows.value.unshift(response.data.data);
      visible.value = false;
      onReset();
    });

  } catch (error) {
    console.log(error);
    console.error('Error creating account:', error.response.data);
    if (error.status === 422) {
      validation.value = error.response.data.errors;
    }
  } finally {
    loading.value = false;
  }
};


const onSubmit = () => {
  addRow();

  emit('created');
};

const onReset = () => {
  data.value = {
    'type': 'transfer',
  };
};

const { data: accounts, fetch: fetchAccount } = useResourceIndex('accounts');
const { data: transaction_categories, fetch: fetchTransactionCategories, loading: transaction_categories_loading } = useResourceIndex('categories');

onMounted(() => {
  fetchAccount();
  fetchTransactionCategories();
  fetch();
});
</script>