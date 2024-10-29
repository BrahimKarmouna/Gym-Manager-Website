<template>
    <div class="q-pa-md">
      <div class="row items-start q-gutter-md ">
        <q-card class="my-card text-white bg-blue-8" 
        style="width:530px;"
       >
       <q-icon name="" />
          <q-card-section >
            <div class="text-h6">Income</div>
          </q-card-section>
          
          
          <q-card-section class="q-pt-none" >
            {{ 0.00 }}
          </q-card-section>
        </q-card>

        <q-card class="my-card text-white bg-blue-8"
        style="width:530px;">
          <q-card-section>
            <div class="text-h6">Expenses</div>
          </q-card-section>
          <q-card-section class="q-pt-none">
            {{ 0.00 }}
          </q-card-section>
        </q-card>

        <q-card class="my-card text-white bg-blue-8"
        style="width:530px;"s>
          <q-card-section>
            <div class="text-h6">Total</div>
          </q-card-section>
          <q-card-section class="q-pt-none">
            {{ 0.00 }}
          </q-card-section>
        </q-card>
      </div>
  
      <div class="q-pa-md">
        <q-table
          flat
          bordered
          title="Treats"
          :rows="rows"
          :columns="columns"
          row-key="id"
          :filter="filter"
          :loading="loading"
        >
          <template v-slot:top>
            <q-btn color="green-8" :disable="loading" label="Add Transfert" @click="dialog = true" />
            <q-btn v-if="rows.length !== 0" class="q-ml-sm" color="primary" :disable="loading" label="Remove Transfert" @click="removeRow" />
            <q-space />
            <q-input borderless dense debounce="300" color="primary" v-model="filter">
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
          </template>
        </q-table>
      </div>
  
      <div class="q-pa-md">
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
                  v-model="category"
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
      </div>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue'
  import { useQuasar } from 'quasar'
  
  const columns = [
    { name: 'amount', required: true, label: 'Amount', align: 'left', field: row => row.amount, sortable: true },
    { name: 'from', align: 'center', label: 'From', field: 'from', sortable: true },
    { name: 'to', label: 'To', field: 'to', sortable: true },
    { name: 'category', label: 'Category', field: 'category' },
    { name: 'note', label: 'Note', field: 'note' },
    { name: 'description', label: 'Description', field: 'description' },
  ]
  
  const originalRows = []
  
  export default {
    setup() {
      const $q = useQuasar()
      const loading = ref(false)
      const filter = ref('')
      const rowCount = ref(0)
      const rows = ref([...originalRows])
      const dialog = ref(false)
  
      const amount = ref(null)
      const from = ref('')
      const to = ref('')
      const category = ref('')
      const note = ref('')
      const description = ref('')
      const lorem = ref('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.')
  
      const addRow = () => {
        loading.value = true
        const newRow = {
          id: ++rowCount.value,
          amount: amount.value,
          from: from.value,
          to: to.value,
          category: category.value,
          note: note.value,
          description: description.value,
        }
        rows.value.push(newRow)
        loading.value = false
  
        dialog.value = false 
        onReset(); 
      }
  
      const removeRow = () => {
        loading.value = true
        setTimeout(() => {
          const index = Math.floor(Math.random() * rows.value.length)
          rows.value = [ ...rows.value.slice(0, index), ...rows.value.slice(index + 1) ]
          loading.value = false
        }, 500)
      }
  
      const onSubmit = () => {
        if (amount.value && from.value && to.value && category.value && note.value && description.value) {
          addRow(); 
        }
      }
  
      const onReset = () => {
        amount.value = null
        from.value = ''
        to.value = ''
        category.value = ''
        note.value = ''
        description.value = ''
      }
  
      return {
        columns,
        rows,
        loading,
        filter,
        rowCount,
        amount,
        from,
        to,
        category,
        note,
        description,
        addRow,
        removeRow,
        onSubmit,
        onReset,
        dialog,
        lorem,
      }
    }
  }
  </script>
  