<template>
  <div class="q-pa-md">
    <div class="q-gutter-y-md" style="max-width: 1700px; max-height:500px;">
      <q-card>
       
        <q-tabs
          v-model="tab"
          dense
          class="text-primary"
          active-color="primary"
          indicator-color="primary"
          align="justify"
          narrow-indicator
        >
          <q-tab name="Transfert" label="Transfert" />
          <q-tab name="Income" label="Income" />
          <q-tab name="Expense" label="Expense" />
        </q-tabs>

        <q-separator />

        
        <q-tab-panels v-model="tab" animated>
         
          <q-tab-panel name="Transfert">
            <div class="text-h6 q-pa-md">
              <div class="row items-start q-gutter-md">
                <q-card class="my-card text-black bg-grey-1" style="width: 510px;">
                  <q-card-section>
                    <div class="text-h6">Income</div>
                  </q-card-section>
                  <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
                </q-card>

                <q-card class="my-card text-black bg-grey-1" style="width: 510px;">
                  <q-card-section>
                    <div class="text-h6">Expense</div>
                  </q-card-section>
                  <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
                </q-card>

                <q-card class="my-card text-black bg-grey-1" style="width: 510px;">
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
                  title="Transfert Records"
                  :rows="rows"
                  :columns="transferColumns"
                  row-key="id"
                  :filter="filter"
                  :loading="loading"
                  
                >
                  <template v-slot:top>
                    <q-btn color="green-8" :disable="loading" label="Add Transfer" @click="dialog = true" />
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
           
            <q-dialog v-model="dialog" persistent>
              <q-card>
                <q-card-section>
                  <q-form @submit.prevent="onSubmit" @reset="onReset" class="q-gutter-md">
                    <q-input
                      style="width: 430px;"
                      v-model="amount"
                      type="number"
                      label="Amount"
                      lazy-rules
                      :rules="[val => val && val.length > 0 || 'Please insert an amount']"
                    />
                    <q-input
                      type="text"
                      v-model="from"
                      label="From"
                      lazy-rules
                      :rules="[val => val && val.length > 0 || 'Please insert a sender']"
                    />
                    <q-input
                    type="text"
                    v-model="to"
                    label="To"
                    lazy-rules
                    :rules="[val => val && val.length > 0 || 'Please insert a recipient']"
                    />
                    <q-input
                      v-model="category" 
                   :options="transactionOptions"
                      label="Category"
                      lazy-rules
                      :rules="[val => val && val.length > 0 || 'Please insert a category']"
                    />
                    <q-input
                      type="text"
                      v-model="note"
                      label="Note"
                      lazy-rules
                      :rules="[val => val && val.length > 0 || 'Please insert a note']"
                    />
                    <div>
                      <q-btn label="Submit" type="submit" color="green-7" />
                      <q-btn label="Reset" type="reset" color="primary" flat class="q-ml-sm" />
                    </div>
                  </q-form>
                </q-card-section>
                <q-card-actions>
                  <q-btn label="Close" @click="dialog = false" color="red" flat />
                </q-card-actions>
              </q-card>
            </q-dialog>
          </q-tab-panel>

         

        </q-tab-panels>

        <q-tab-panels v-model="tab" animated>
         
         <q-tab-panel name="Income">
           <div class="text-h6 q-pa-md">
             <div class="row items-start q-gutter-md">
               <q-card class="my-card text-black bg-grey-1" style="width: 510px;">
                 <q-card-section>
                   <div class="text-h6">Income</div>
                 </q-card-section>
                 <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
               </q-card>

               <q-card class="my-card text-black bg-grey-1" style="width: 510px;">
                 <q-card-section>
                   <div class="text-h6">Expense</div>
                 </q-card-section>
                 <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
               </q-card>

               <q-card class="my-card text-black bg-grey-1" style="width: 510px;">
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
                 title="Income Records"
                 :rows="rows"
                 :columns="incomeColumns"
                 row-key="id"
                 :filter="filter"
                 :loading="loading"
               >
                 <template v-slot:top>
                   <q-btn color="green-8" :disable="loading" label="Add Income" @click="dialog = true" />
                   <q-btn v-if="rows.length !== 0" class="q-ml-sm" color="primary" :disable="loading" label="Remove Income" @click="removeRow" />
                   <q-space />
                   <q-input v-model="search" filled type="search" dense>
                     <template v-slot:append>
                       <q-icon name="search" />
                     </template>
                   </q-input>
                 </template>
               </q-table>
             </div>
           </div>

          
           <q-dialog v-model="dialog" persistent>
             <q-card>
               <q-card-section>
                 <q-form @submit.prevent="onSubmit" @reset="onReset" class="q-gutter-md">
                  <q-input filled v-model="date" mask="date" :rules="['date']">
                    <template v-slot:append>
        <q-icon name="event" class="cursor-pointer">
          <q-popup-proxy cover transition-show="scale" transition-hide="scale">
            <q-date v-model="date">
              <div class="row items-center justify-end">
                <q-btn v-close-popup label="Close" color="primary" flat />
              </div>
            </q-date>
          </q-popup-proxy>
        </q-icon>
      </template>
                  </q-input>
                   <q-input
                     style="width: 430px;"
                     v-model="amount"
                     type="number"
                     label="Amount"
                     lazy-rules
                     :rules="[val => val && val.length > 0 || 'Please insert an amount']"
                   />
                   <q-select v-model="category" 
                   :options="incomeOptions" 
                   label="Category"
                   :rules="[val => val && val.length > 0 || 'Please insert a category']" />
                   <q-input
                     type="text"
                     v-model="to"
                     label="To"
                     lazy-rules
                     :rules="[val => val && val.length > 0 || 'Please insert an account']"
                   />
                   <q-input
                     type="text"
                     v-model="note"
                     label="Note"
                     lazy-rules
                     :rules="[val => val && val.length > 0 || 'Please insert a note']"
                   />
                   <q-input
                     type="text"
                     v-model="description"
                     label="Description"
                     lazy-rules
                     :rules="[val => val && val.length > 0 || 'Please insert a description']"
                   />
                   <div>
                     <q-btn label="Submit" type="submit" color="green-7" />
                     <q-btn label="Reset" type="reset" color="primary" flat class="q-ml-sm" />
                   </div>
                 </q-form>
               </q-card-section>
               <q-card-actions>
                 <q-btn label="Close" @click="dialog = false" color="red" flat />
               </q-card-actions>
             </q-card>
           </q-dialog>

            <q-tab-panels v-model="tab" animated>
         
                  
        </q-tab-panels>        
         </q-tab-panel>

        

       </q-tab-panels>


       <q-tab-panels v-model="tab" animated>
         
         <q-tab-panel name="Expense">
           <div class="text-h6 q-pa-md">
             <div class="row items-start q-gutter-md">
               <q-card class="my-card text-black bg-grey-1" style="width: 510px;">
                 <q-card-section>
                   <div class="text-h6">Income</div>
                 </q-card-section>
                 <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
               </q-card>

               <q-card class="my-card text-black bg-grey-1" style="width: 510px;">
                 <q-card-section>
                   <div class="text-h6">Expense</div>
                 </q-card-section>
                 <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
               </q-card>

               <q-card class="my-card text-black bg-grey-1" style="width: 510px;">
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
                 title="Transfert Records"
                 :rows="rows"
                 :columns="expenseColumns"
                 row-key="id"
                 :filter="filter"
                 :loading="loading"
               >
                 <template v-slot:top>
                   <q-btn color="green-8" :disable="loading" label="Add Transfer" @click="dialog = true" />
                   <q-btn v-if="rows.length !== 0" class="q-ml-sm" color="primary" :disable="loading" label="Remove Transfer" @click="removeRow" />
                   <q-space />
                   <q-input v-model="search" filled type="search" dense>
                     <template v-slot:append>
                       <q-icon name="search" />
                     </template>
                   </q-input>
                 </template>
               </q-table>
             </div>
           </div>

          
           <q-dialog v-model="dialog" persistent>
             <q-card>
               <q-card-section>
                 <q-form @submit.prevent="onSubmit" @reset="onReset" class="q-gutter-md">
                  <q-select v-model="category" 
                   :options="expenseOptions" 
                   label="Category"
                   :rules="[val => val && val.length > 0 || 'Please insert a category']" />
                   <q-input
                     type="text"
                     v-model="from"
                     label="From"
                     lazy-rules
                     :rules="[val => val && val.length > 0 || 'Please insert an account']"
                   />
                   <q-input
                     type="text"
                     v-model="note"
                     label="Note"
                     lazy-rules
                     :rules="[val => val && val.length > 0 || 'Please insert a note']"
                   />
                   <q-input
                     type="text"
                     v-model="description"
                     label="Description"
                     lazy-rules
                     :rules="[val => val && val.length > 0 || 'Please insert a description']"
                   />
                   <div>
                     <q-btn label="Submit" type="submit" color="green-7" />
                     <q-btn label="Reset" type="reset" color="primary" flat class="q-ml-sm" />
                   </div>
                 </q-form>
               </q-card-section>
               <q-card-actions>
                 <q-btn label="Close" @click="dialog = false" color="red" flat />
               </q-card-actions>
             </q-card>
           </q-dialog>
         </q-tab-panel>

        

       </q-tab-panels>
      </q-card>
    </div>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue';
import { useQuasar } from 'quasar';
import { api } from '@/boot/axios';

export default {
  setup() {
    const $q = useQuasar();
    const loading = ref(false);
    const filter = ref('');
    const rows = ref([]);
    const dialog = ref(false);

    // const selected = ref([]);

    const amount = ref(null);
    const from = ref('');
    const to = ref('');
    const category = ref('');
    const note = ref('');
    const description = ref('');

    const transferColumns = [
      { name: 'user', label: 'User', align: 'left', field: (row) => row.user.name },
      { name: 'amount', label: 'Amount', align: 'left', field: 'amount', sortable: true },
      { name: 'from', label: 'From', align: 'left', field: (row) => row.from.name, sortable: true },
      { name: 'to', label: 'To', align: 'left', field: (row) => row.to.name, sortable: true },
      { name: 'note', label: 'Note', align: 'left', field: 'note' },
      { name: 'transaction_category', label: 'Transaction Category', align: 'left', field: 'transaction_category', sortable: true },
      { name: 'actions', label: '', align: 'right', field: 'actions' },
    ];

    const incomeColumns = [
      { name: 'amount', label: 'Amount', align: 'left', field: 'amount', sortable: true },
      { name: 'category', label: 'Category', align: 'left', field: 'category', sortable: true },
      { name: 'account', label: 'Account', align: 'left', field: 'account', sortable: true },
      { name: 'note', label: 'Note', align: 'left', field: 'note' },
      { name: 'description', label: 'Description', align: 'left', field: 'description' },
    ];

    const expenseColumns = [
      { name: 'amount', label: 'Amount', align: 'left', field: 'amount', sortable: true },
      { name: 'category', label: 'Category', align: 'left', field: 'category', sortable: true },
      { name: 'account', label: 'Account', align: 'left', field: 'account', sortable: true },
      { name: 'note', label: 'Note', align: 'left', field: 'note' },
      { name: 'description', label: 'Description', align: 'left', field: 'description' },
    ];

    const addRow = () => {
      loading.value = true;

      const newRow = {
        amount: amount.value,
        from: from.value,
        to: to.value,
        category: category.value,
        note: note.value,
        description: description.value,
        category: category.value,
        type: 'transfer',
      };

      api.post('transaction', newRow).then((response) => {
        loading.value = false;
        
        $q.notify({
          type: 'positive',
          message: 'Success!',
          caption: 'Transfer added successfully.',
          position: 'bottom-right',
          timeout: 3000
        });

        rows.value.unshift(response.data.data);
        dialog.value = false;
        onReset();
      });
    };

    const removeRow = () => {
      // if (rows.value.length) {
      //   rows.value.pop(); 
      // }
      
      // $q.notify({
      //   type: 'positive',
      //   message: 'Success!',
      //   caption: 'Transfer deleted successfully.',
      //   position: 'bottom-right',
      //   timeout: 3000
      // });
    };

    const onSubmit = () => {
      if (amount.value && from.value && to.value && note.value && description.value) {
        addRow();
      }
    };

    const onReset = () => {
      amount.value = null;
      from.value = '';
      to.value = '';
      category.value = '';
      note.value = '';
      description.value = '';
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
    api.delete(`/transaction/${transaction.id}`).then(() => {
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
      tab: ref('Transfert'),
      transferColumns,
      incomeColumns,
      expenseColumns,
      rows,
      loading,
      filter,
      amount,
      from,
      to,
      category,
      note,
      description,
      dialog,
      search: ref(''),
      addRow,
      removeRow,
      deleteRow,
      editRow,
      onSubmit,
      onReset,
      incomeOptions: [
        'Salary', 'Pocket Money', 'Allowance', 'Bonus', 
      ],
      date: ref('today'),
      // selected,
      transactionOptions: [
        'Salary', 'Pocket Money', 'Allowance', 'Bonus', 
      ],
      expenseOptions: [
        'Salary', 'Pocket Money', 'Allowance', 'Bonus', 
      ],
    };
  },
};
</script>

