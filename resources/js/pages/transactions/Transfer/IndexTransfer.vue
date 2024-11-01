<template>

<CreateForm v-model:visible="showCreateDialog" @created="handleCreated" />

    <div class="q-pa-md">
      <div class="q-gutter-y-md" style="max-width: 1700px; height:550px;">
        <q-card>
         
          <q-separator />

        <!-- transfer  -->
          <q-tab-panels v-model="tab" animated>
           
            <q-tab-panel name="Transfer">
              <div class="text-h6 q-pa-md">
                <div class="row items-start q-gutter-md">
                  <q-card class="my-card text-black bg-grey-1" style="width: 500px;">
                    <q-card-section>
                      <div class="text-h6">Income</div>
                    </q-card-section>
                    <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
                  </q-card>
  
                  <q-card class="my-card text-black bg-grey-1" style="width: 500px;">
                    <q-card-section>
                      <div class="text-h6">Expense</div>
                    </q-card-section>
                    <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
                  </q-card>
  
                  <q-card class="my-card text-black bg-grey-1" style="width: 500px;">
                    <q-card-section>
                      <div class="text-h6">Total</div>
                    </q-card-section>
                    <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
                  </q-card>
                </div>
  
                <div class="q-pa-md">
                  <q-table
                    flat
                    bordered
                    title="Transfer Records"
                    
                    :rows="data ?? []"
                    :columns="transferColumns"
                    row-key="id"
                    :filter="filter"
                    :loading="loading"
                    @request="onRequest"
                  >
                    <template v-slot:top>
                      <q-btn color="green-8" :disable="loading" label="Add Transfer" @click="showCreateDialog = true" />
                      <!-- <q-btn v-if="selected.length > 0" class="q-ml-sm" color="primary" :disable="loading" label="Remove Transfer" @click="removeRow" :loading="deleting"/> -->
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
              </div>
             
            </q-tab-panel>
          </q-tab-panels> 
        </q-card>
      </div>
    </div>
  </template>
  
  <script >
  import { onMounted, ref } from 'vue';
  import { useQuasar } from 'quasar';
  import { api } from '@/boot/axios';
  import { useResourceIndex } from '@/composables/useResourceIndex';
  import { data } from 'autoprefixer';
import CreateForm from './CreateForm.vue';
  
  export default {
    setup() {
      const $q = useQuasar();
      const loading = ref(false);
      const filter = ref('');
      const rows = ref([]);
      const dialog = ref(false);

      const showCreateDialog = ref(false);
      
  
      // const selected = ref([]);
  
      const amount = ref(null);
      const from = ref('');
      const to = ref('');
      const category = ref('');
      const note = ref('');
      const description = ref('');
  
      const transferColumns = [
        { name: 'user', label: 'User', align: 'left', field: (row) => row.user.name ?? 'N/A'},
        { name: 'amount', label: 'Amount', align: 'left', field: 'amount', sortable: true },
        { name: 'date', label: 'Date', align: 'left', field: 'date', sortable: true },
        { name: 'from', label: 'From', align: 'left', field: (row) => row.from ?? 'N/A', sortable: true },
        { name: 'to', label: 'To', align: 'left', field: (row) => row.to, sortable: true },
        { name: 'note', label: 'Note', align: 'left', field: 'note' },
        { name: 'transaction_category', label: 'Transaction Category', align: 'left', field: 'category', sortable: true },
        { name: 'actions', label: '', align: 'right', field: 'actions' },
      ];
  

       
const { data , fetch , loading: loadingTransfer } = useResourceIndex('transactions?type=transfer');


      onMounted(() => {
        fetch();
        console.log(data);
      })
    
  
      const removeRow = () => {
       
      };
  
    
  
      onMounted(() => {
        loading.value = true;
  
        api.get('transactions').then(response => {
          rows.value = response.data.data;
          loading.value = false;
        });
      });
  
  
      function deleteRow(transaction) {
    $q.dialog({
      title: 'Confirm',
      message: `Are you sure you want to delete ${transaction.amount}?`,
      cancel: true,
      persistent: true,
    }).onOk(() => {
      api.delete(`/transactions/${transaction.id}`).then(() => {
        fetch();
      });
    });
  }
  
  function editRow(transaction) {
    createForm.fields.amount = transaction.amount;
    createForm.fields.source_account_id = transaction.source_account_id;
    createForm.fields.destination_account_id = transaction.destination_account_id;
    createForm.fields.transaction_type = transaction.transaction_type;
    createForm.fields.id = transaction.id; 
    isEditing.value = true;
    showCreateDialog.value = true;
  }
  
  
  
      return {
        tab: ref('Transfer'),
        transferColumns,
        rows,
        loading,
        filter,
        amount,
        from,
        to,
        category,
        note,
        dialog,
        search: ref(''),
       data,
        removeRow,
        deleteRow,
        editRow,
          
        text: ref(''),
      ph: ref(''),
      dense: ref(false),
      date: ref(''),
        // selected,
      };
    },
  };
  </script>
  
  