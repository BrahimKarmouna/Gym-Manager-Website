<template>

  <CreateExpense v-model:visible="showCreateDialog"
              @created="handleCreated" />

  <EditExpense v-model:visible="showEditDialog"
            @updated="handleUpdated"
            :id="itemToEdit?.id" />

  <!-- income  -->
  <q-table flat
           title="Expense Records"
           :rows="data ?? []"
           :columns="expenseColumns"
           row-key="id"
           :filter="filter"
           :pagination="{ rowsPerPage: 15 }"
           :loading="loadingTransfer"
           @request="onRequest"
          >

     <template v-slot:header-selection="scope">
              <q-checkbox v-model="scope.selected" />
     </template>

    <template v-slot:top>
      <q-btn color="green-8 dark:bg-green-900"
      class=" dark:text-gray-200"
             :disable="loading"
             label="Add Expense"
             @click="showCreateDialog = true" />

      <q-space />

      <q-input v-model="search"

               type="search"
               dense>
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </template>

    <template v-slot:body-cell-actions="props">
      <q-td v-bind:props="props"
            class="q-gutter-x-xs">
        <q-btn flat
               size="sm"
               padding="sm"
               color="primary"
               icon="edit"
               @click.stop="editRow(props.row)" />

        <q-btn flat
               size="sm"
               padding="sm"
               color="red "
               icon="delete"
               @click.stop="deleteRow(props.row)" />
      </q-td>
    </template>
  </q-table>
</template>

<script>
import { onMounted, ref } from 'vue';
import { useQuasar } from 'quasar';
import { api } from '@/boot/axios';
import { useResourceIndex } from '@/composables/useResourceIndex';
import CreateExpense from './CreateExpense.vue';
import EditExpense from './EditExpense.vue';

export default {
  components: {
    CreateExpense,
    EditExpense
  },
  setup() {

    const $q = useQuasar();
    const loading = ref(false);
    const filter = ref('');


    const showCreateDialog = ref(false);

    const amount = ref(null);
    const from = ref('');
    // const to = ref('');
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



    const { data, fetch, loading: loadingTransfer } = useResourceIndex('transactions?transaction_type=expense');

    onMounted(() => {
      fetch();
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

    return {
      expenseColumns,
      loading,
      filter,
      amount,
      from,
      // to,
      category,
      note,
      showCreateDialog,
      showEditDialog,
      search: ref(''),
      data,
      deleteRow,
      editRow,
      handleCreated,
      handleUpdated,
      itemToEdit,
      text: ref(''),
      ph: ref(''),
      dense: ref(false),
      date: ref(''),
      // selected,

    };
  },
};
</script>
