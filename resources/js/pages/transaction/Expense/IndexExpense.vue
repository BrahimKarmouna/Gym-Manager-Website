<template>
<q-card>
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
            title="Expense Records"
            :rows="rows"
            :columns="expenseColumns"
            row-key="id"
            :filter="filter"
            :loading="loading"
          >
            <template v-slot:top>
              <q-btn color="green-8" :disable="loading" label="Add Expense" @click="dialog = true" />
              <q-btn v-if="rows.length !== 0" class="q-ml-sm" color="primary" :disable="loading" label="Remove expense" @click="removeRow" />
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
</template>



<script>

</script>