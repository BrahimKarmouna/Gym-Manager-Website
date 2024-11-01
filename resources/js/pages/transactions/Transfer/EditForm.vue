<template>
    edit
</template>
<script setup>
function editRow(transaction) {
  createForm.fields.amount = transaction.amount;
  createForm.fields.source_account_id = transaction.source_account_id;
  createForm.fields.destination_account_id = transaction.destination_account_id;
  createForm.fields.transaction_type = transaction.transaction_type;
  createForm.fields.id = transaction.id; 
  isEditing.value = true;
  showCreateDialog.value = true;
}

</script>