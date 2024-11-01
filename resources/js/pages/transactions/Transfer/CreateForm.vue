<template>
            <q-tab-panels v-model="tab" animated>    
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
           
          </q-tab-panels>
        
           <q-dialog v-model="visible" persistent>
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
                     <!-- Select  From Account -->
                     <q-select v-model="model"  :options="accounts" option-value="id"  option-label="name"  label="From" />
                   <!-- Select To Account -->
                   <q-select v-model="model" :options="accounts" option-value="id"  option-label="name"  label="To" />
                   <!-- Select a category -->
                   <q-select v-model="model" :options="transaction_categories" option-value="id" option-label="name" label="Category" />

                  
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
       

        

       
</template>
<script setup>

const emit = defineEmits(['created']);

const visible = defineModel('visible', { type: Boolean, default: false });	

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
  
        api.post('transactions', newRow).then((response) => {
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


      const onSubmit = () => {
        
        addRow();
      };
  
      const onReset = () => {
        amount.value = null;
        from.value = '';
        to.value = '';
        category.value = '';
        note.value = '';
        description.value = '';
      };

      const { data: accounts, fetch: fetchAccount  } = useResourceIndex('accounts');
      const { data: transaction_categories, fetch: fetchTransactionCategories } = useResourceIndex('transaction_categories');

// return{
//     addRow
// }

</script>