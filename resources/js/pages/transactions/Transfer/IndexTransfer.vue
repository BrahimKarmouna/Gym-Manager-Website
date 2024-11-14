<template>

  <CreateForm v-model:visible="showCreateDialog"
              @created="handleCreated" />

  <EditForm v-model:visible="showEditDialog"
            @updated="handleUpdated"
            :id="itemToEdit?.id" />

  <!-- transfer  -->
  <q-table flat
           title="Transfer Records"
           :rows="data ?? []"
           :columns="transferColumns"
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
      <q-btn color="blue-900 dark:bg-blue-400 "
             :disable="loading"
             label="Add Transfer"
             @click="showCreateDialog = true" />

      <q-space />

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
               color="blue dark:text-blue-600"
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
import { onMounted, ref } from 'vue';
import { useQuasar } from 'quasar';
import { api } from '@/boot/axios';
import { useResourceIndex } from '@/composables/useResourceIndex';
import CreateForm from './CreateForm.vue';
import EditForm from './EditForm.vue';

const $q = useQuasar();
const loading = ref(false);
const tableRef = ref(null);
// const formatter = useMoney('MAD', 'fr-FR');

const showCreateDialog = ref(false);

const transferColumns = [
  { name: 'user', label: 'User', align: 'left', field: (row) => row.user.name ?? 'N/A' },
  { name: 'amount', label: 'Amount', align: 'left', field: (row) => `${row.amount ? row.amount.toFixed(2) + ' MAD' : 'N/A'}`, sortable: true },
  { name: 'date', label: 'Date', align: 'left', field: 'date', sortable: true },
  { name: 'from', label: 'From', align: 'left', field: (row) => row.source_account?.name ?? 'N/A', sortable: true },
  { name: 'to', label: 'To', align: 'left', field: (row) => row.destination_account?.name ?? "N/A", sortable: true },
  { name: 'note', label: 'Note', align: 'left', field: 'note', field: (row) => row.note ?? "N/A", sortable: true },
  { name: "created_at", label: "Created At", field: "created_at", sortable: true },
  { name: "updated_at", label: "Updated At", field: "updated_at", sortable: true },
  { name: 'actions', label: '', align: 'right', field: 'actions' },
];

const { data, fetch, loading: loadingTransfer, onRequest, options } = useResourceIndex('transactions?transaction_type=transfer');

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
