<template>
  <!-- <q-tab-panels v-model="tab" animated>    
             <div class="q-pa-md">
               <q-table
                 flat
                 bordered
                 title="Transfer Records"
                 :rows="rows"
                 :columns="transferColumns"
                 row-key="id"
                 :filter="filter"
                 :loading="loading"
                 
               >
                 <template v-slot:top>
                   <q-btn color="green-8" :disable="loading" label="Add Transfer" @click="visible = true" />
                   <q-btn v-if="selected.length > 0" class="q-ml-sm" color="primary" :disable="loading" label="Remove Transfer" @click="removeRow" :loading="deleting"/>
                   <q-space />
                   <q-input v-model="search" filled type="search" dense>
                     <template v-slot:append>
                       <q-icon name="search" />
                     </template>
</q-input>
</template>

<template v-slot:body-cell-actions="props">
                   <q-td v-bind:props="props">
                     <q-btn flat class="pr-0 ml-2" color="primary" icon="edit" @click.stop="editRow(transaction)" />
                     <q-btn flat class="pl-0" color="red" icon="delete" @click.stop="deleteRow(transaction)" />
                   </q-td>
                 </template>
</q-table>
</div>

</q-tab-panels> -->

  <q-dialog v-model="visible"
            persistent>
    <q-card>
      <q-card-section>
        <q-form @submit.prevent="onSubmit"
                @reset="onReset"
                class="q-gutter-md">

          <q-input
            label="Date"
            v-model="data.date"
            hide-bottom-space
            required
            dense
            mask="date"
            >
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy cover
                                transition-show="scale"
                                transition-hide="scale">
                  <q-date v-model="data.date">
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup label="Close"
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
                   lazy-rules />

          <!-- Select  From Account -->
          <q-select v-model="data.source_account"
                    :options="accounts"
                    option-value="id"
                    option-label="name"
                    label="From" />

          <!-- Select To Account -->
          <q-select v-model="data.destination_account"
                    :options="accounts"
                    option-value="id"
                    option-label="name"
                    label="To" />

          <!-- Select a category -->
          <q-select v-model="data.category"
                    :options="transaction_categories"
                    option-value="id"
                    option-label="name"
                    label="Category" />


          <q-input type="text"
                   v-model="data.note"
                   label="Note"
                   lazy-rules />
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

// const amount = ref(null);
// const from = ref('');
// const date = ref('');
// const to = ref('');
// const category = ref('');
// const note = ref('');
// const description = ref('');

const addRow = () => {
  loading.value = true;

  // const newRow = {
  //   date: date.value,
  //   amount: amount.value,
  //   from: from.value,
  //   to: to.value,
  //   category: category.value,
  //   note: note.value,
  //   description: description.value,
  //   category: category.value,
  //   type: 'transfer',
  // };

  api.post('transactions', data.value).then((response) => {
    loading.value = false;

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
};


const onSubmit = () => {
  addRow();

  emit('created');
};

const onReset = () => {
  // date.value = '';
  // amount.value = null;
  // from.value = '';
  // to.value = '';
  // category.value = '';
  // note.value = '';
  // description.value = '';
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