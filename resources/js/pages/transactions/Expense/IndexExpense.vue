<template>

  <CreateExpense v-model:visible="showCreateDialog"
              @created="handleCreated" />

  <EditExpense v-model:visible="showEditDialog"
            @updated="handleUpdated"
            :id="itemToEdit?.id" />

  <!-- expense  -->
  <!-- Copy table properties from q-table -->
  <q-table
          flat
           title="Expense Records"
           :rows="data ?? []"
           :columns="expenseColumns"
           row-key="id"
           ref="tableRef"
           :pagination="{ rowsPerPage: 8 }"
           :loading="loadingTransfer"
           @request="onRequest"
           :color="$q.dark.isActive ? 'grey-3' : 'grey-9'"
           class="rounded-lg"
           :rows-per-page-options="[8, 10, 20, 30, 40]">

    <template v-slot:header-selection="scope">
      <q-checkbox v-model="scope.selected" />
    </template>

    <template v-slot:top>
      <q-btn color="green-500 dark:bg-blue-900"
             :disable="loading"
             label="Add Expense"
             @click="showCreateDialog = true" />

      <q-space />

      <!-- Copy this input -->
      <q-input v-model="options.search"
               @change="
                $refs.tableRef.requestServerInteraction({
                  pagination: { page: 1 },
                })
                "
               placeholder="Search..."
               outlined
               dense>
        <template #append>
          <q-icon name="sym_r_search" />
        </template>
      </q-input>
    </template>

    <template v-slot:body-cell-actions="props">
      <q-td v-bind:props="props"
            class="q-gutter-x-xs">
        <q-btn flat
               size="sm"
               padding="sm"
               color="primary dark:text-gray-400"
               icon="edit"
               @click.stop="editRow(props.row)" />

        <q-btn flat
               size="sm"
               padding="sm"
               color="red dark:text-red-800"
               icon="delete"
               @click.stop="deleteRow(props.row)" />
      </q-td>
    </template>
  </q-table>
</template>

<script setup>
import { onMounted, ref, toRaw, nextTick } from 'vue';
import { useQuasar } from 'quasar';
import { api } from '@/boot/axios';
import { useResourceIndex } from '@/composables/useResourceIndex';
import EditExpense from './EditExpense.vue';
import CreateExpense from './CreateExpense.vue';

const $q = useQuasar();
const loading = ref(false);
const filter = ref('');

//! Add this
const tableRef = ref(null);

const showCreateDialog = ref(false);

const amount = ref(null);
const from = ref('');
const to = ref('');
const category = ref('');
const note = ref('');

const expenseColumns = [
  { name: 'user', label: 'User', align: 'left', field: (row) => row.user.name ?? 'N/A' },
  { name: 'amount', label: 'Amount', align: 'left', field: 'amount', sortable: true },
  { name: 'date', label: 'Date', align: 'left', field: 'date', sortable: true },
  { name: 'from', label: 'From', align: 'left', field: (row) => row.sourceAccount?.name ?? 'N/A', sortable: true },
  // { name: 'to', label: 'To', align: 'left', field: (row) => row.destinationAccount?.name ?? "N/A", sortable: true },
  { name: 'note', label: 'Note', align: 'left', field: 'note' },
  { name: 'transaction_category', label: 'Transaction Category', align: 'center', field: (row) => row.category?.name ?? 'N/A', sortable: false },
  // { name: "created_at", label: "Created At", field: "created_at", sortable: true },
  // { name: "updated_at", label: "Updated At", field: "updated_at", sortable: true },
  { name: 'actions', label: '', align: 'right', field: 'actions' },
];



const { data, fetch, loading: loadingTransfer, onRequest, options } = useResourceIndex('transactions?transaction_type=expense');

onMounted(() => {
  fetch();
  tableRef.value.requestServerInteraction()
})

const handleCreated = () => {
  fetch();
}

const handleUpdated = () => {
  fetch();
}

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

const itemToEdit = ref(null);
const showEditDialog = ref(false);

function editRow(transaction) {
  itemToEdit.value = transaction;
  console.log(itemToEdit.value);
  showEditDialog.value = true;
}
</script>
