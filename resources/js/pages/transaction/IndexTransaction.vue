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
          <q-tab name="Expenses" label="Expenses" />
        </q-tabs>

        <q-separator />

        
        <q-tab-panels v-model="tab" animated>
         
          <q-tab-panel name="Transfert">
            <div class="text-h6 q-pa-md">
              <div class="row items-start q-gutter-md">
                <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
                  <q-card-section>
                    <div class="text-h6">Income</div>
                  </q-card-section>
                  <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
                </q-card>

                <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
                  <q-card-section>
                    <div class="text-h6">Expenses</div>
                  </q-card-section>
                  <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
                </q-card>

                <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
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
                  :columns="columns1"
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

        <q-tab-panels v-model="tab" animated>
         
         <q-tab-panel name="Income">
           <div class="text-h6 q-pa-md">
             <div class="row items-start q-gutter-md">
               <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
                 <q-card-section>
                   <div class="text-h6">Income</div>
                 </q-card-section>
                 <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
               </q-card>

               <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
                 <q-card-section>
                   <div class="text-h6">Expenses</div>
                 </q-card-section>
                 <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
               </q-card>

               <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
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
                 :columns="columns2"
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
                   <q-input
                     style="width: 430px;"
                     v-model="amount"
                     type="number"
                     label="Amount"
                     lazy-rules
                     :rules="[val => val && val.length > 0 || 'Please insert an amount']"
                   />
                   <q-select v-model="category" 
                   :options="options1" 
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
         
          <q-tab-panel name="Transfert">
            <div class="text-h6 q-pa-md">
              <div class="row items-start q-gutter-md">
                <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
                  <q-card-section>
                    <div class="text-h6">Income</div>
                  </q-card-section>
                  <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
                </q-card>

                <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
                  <q-card-section>
                    <div class="text-h6">Expenses</div>
                  </q-card-section>
                  <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
                </q-card>

                <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
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
                  :columns="columns3"
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
                    <q-input
                      style="width: 430px;"
                      v-model="amount"
                      type="number"
                      label="Amount"
                      lazy-rules
                      :rules="[val => val && val.length > 0 || 'Please insert an amount']"
                    />
                    <q-select v-model="category" 
                   :options="options2" 
                   label="Category"
                   :rules="[val => val && val.length > 0 || 'Please insert a category']" />
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
         </q-tab-panel>

        

       </q-tab-panels>


       <q-tab-panels v-model="tab" animated>
         
         <q-tab-panel name="Expenses">
           <div class="text-h6 q-pa-md">
             <div class="row items-start q-gutter-md">
               <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
                 <q-card-section>
                   <div class="text-h6">Income</div>
                 </q-card-section>
                 <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
               </q-card>

               <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
                 <q-card-section>
                   <div class="text-h6">Expenses</div>
                 </q-card-section>
                 <q-card-section class="q-pt-none">{{ 0.00 }}</q-card-section>
               </q-card>

               <q-card class="my-card text-white bg-blue-8" style="width: 510px;">
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
                 :columns="columns3"
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
                   <q-input
                     style="width: 430px;"
                     v-model="amount"
                     type="number"
                     label="Amount"
                     lazy-rules
                     :rules="[val => val && val.length > 0 || 'Please insert a category']"
                   />
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
import { ref } from 'vue';
import { useQuasar } from 'quasar';

export default {
  setup() {
    const $q = useQuasar();
    const loading = ref(false);
    const filter = ref('');
    const rowCount = ref(0);
    const rows = ref([]);
    const dialog = ref(false);

    const amount = ref(null);
    const from = ref('');
    const to = ref('');
    const category = ref('');
    const note = ref('');
    const description = ref('');

    const columns1 = [
      { name: 'amount', label: 'Amount', align: 'left', field: 'amount', sortable: true },
      { name: 'from', label: 'From', align: 'left', field: 'from', sortable: true },
      { name: 'to', label: 'To', align: 'left', field: 'to', sortable: true },
      { name: 'note', label: 'Note', align: 'left', field: 'note' },
      { name: 'description', label: 'Description', align: 'left', field: 'description' },
    ];
    const columns2 = [
      { name: 'amount', label: 'Amount', align: 'left', field: 'amount', sortable: true },
      { name: 'category', label: 'Category', align: 'left', field: 'category', sortable: true },
      { name: 'account', label: 'Account', align: 'left', field: 'account', sortable: true },
      { name: 'note', label: 'Note', align: 'left', field: 'note' },
      { name: 'description', label: 'Description', align: 'left', field: 'description' },
    ];
    const columns3 = [
      { name: 'amount', label: 'Amount', align: 'left', field: 'amount', sortable: true },
      { name: 'category', label: 'Category', align: 'left', field: 'category', sortable: true },
      { name: 'account', label: 'Account', align: 'left', field: 'account', sortable: true },
      { name: 'note', label: 'Note', align: 'left', field: 'note' },
      { name: 'description', label: 'Description', align: 'left', field: 'description' },
    ];

    const addRow = () => {
      loading.value = true;
      const newRow = {
        id: ++rowCount.value,
        amount: amount.value,
        from: from.value,
        to: to.value,
        category: category.value,
        note: note.value,
        description: description.value,
      };
      rows.value.push(newRow);
      loading.value = false;
      dialog.value = false;
      onReset();
    };

    const removeRow = () => {
      if (rows.value.length) {
        rows.value.pop(); 
      }
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

    return {
      tab: ref('Transfert'),
      columns1,
      columns2,
      columns3,
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
      onSubmit,
      onReset,
      options1: [
        'Salary', 'Pocket Money', 'Allowance', 'Bonus', 
      ],
      options2: [
        'Salary', 'Pocket Money', 'Allowance', 'Bonus', 
      ]
    };
  },
};
</script>

