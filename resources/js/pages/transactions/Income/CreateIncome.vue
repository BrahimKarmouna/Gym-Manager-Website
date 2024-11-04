<template>

  <q-dialog v-model="visible"
            persistent>
    <q-card>
      <q-card-section>
        <q-form @submit.prevent="onSubmit"
                @reset="onReset"
                class="q-gutter-md">

          <q-input label="Date"
                   v-model="form.fields.date"
                   hide-bottom-space
                   :error="'date' in form.errors"
                   :error-message="form.errors.date?.[0]"
                   mask="date">
            <template v-slot:append>
              <q-icon name="event"
                      class="cursor-pointer">
                <q-popup-proxy cover
                               transition-show="scale"
                               transition-hide="scale">
                  <q-date v-model="form.fields.date">
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
                   v-model="form.fields.amount"
                   type="number"
                   label="Amount"
                   :error="'amount' in form.errors"
                   :error-message="form.errors.amount?.[0]"
                   lazy-rules
                   hide-bottom-space />

          <!-- Select a category -->
          <q-select v-model="form.fields.category"
                    :options="transaction_categories"
                    option-value="id"
                    option-label="name"
                    label="Category"
                    :error="'category_id' in form.errors"
                   :error-message="form.errors.category_id?.[0]"
                   hide-bottom-space />


          <q-input type="text"
                   v-model="form.fields.note"
                   label="Note"
                   lazy-rules
                    :error="'note' in form.errors"
                   :error-message="form.errors.note?.[0]"
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
               @click="onReset"
               color="red"
               flat />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { useResourceIndex } from '@/composables/useResourceIndex';
import { onMounted, ref } from 'vue';
import { useQuasar } from 'quasar';
import { useForm } from '@/composables/useForm';

const emit = defineEmits(['created']);

const visible = defineModel('visible', { type: Boolean, default: false });

const $q = useQuasar();

const form = useForm({
  date: '',
  amount: '',
  source_account_id: '',
  destination_account_id: '',
  category_id: '',
  note: '',
  transaction_type: {
    'label': 'Income',
    'value': 'Income',
  },
})



const onSubmit = () => {

form.post('/transactions', {}, {
  onSuccess: () => {
    $q.notify({
      type: 'positive',
      message: 'Success!',
      caption: 'Income added successfully.',
      position: 'bottom-right',
      timeout: 3000
    });

    onReset();
    emit('created');
  }
});
};

const onReset = () => {

  visible.value = false;
  form.reset();
  form.clearErrors();
};

const { data: accounts, fetch: fetchAccount } = useResourceIndex('accounts');
const { data: transaction_categories, fetch: fetchTransactionCategories, loading: transaction_categories_loading } = useResourceIndex('categories');

onMounted(() => {
  fetchAccount();
  fetchTransactionCategories();
});
</script>
