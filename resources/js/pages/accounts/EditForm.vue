<template>

  <q-dialog v-model="visible"
            persistent>
    <q-card style="width: 500px;">
      <q-card-section>
        <div class="flex justify-between items-center">
          <div class="text-h6">Edit Account</div>

          <q-btn icon="close"
                 flat
                 dense
                 v-close-popup />
        </div>
      </q-card-section>

      <q-card-section class="pt-0">
        <q-form @submit.prevent="onSubmit"
                @reset="onReset"
                class="space-y-4">

          <div class="row q-col-gutter-md">
            <q-input class="col-6"
                     dense
                     label="Account Name"
                     outlined
                     v-model="form.fields.name"
                     autofocus
                     :error="'name' in form.errors"
                     :error-message="form.errors.name?.[0]"
                     hide-bottom-space />
            <q-input class="col-6"
                     dense
                     label="Account Balance"
                     outlined
                     type="number"
                     v-model="form.fields.balance"
                     :error="'balance' in form.errors"
                     :error-message="form.errors.balance?.[0]"
                     hide-bottom-space />
            <q-input class="col-12"
                     dense
                     label="Account RIB"
                     maxlength="24"
                     outlined
                     type="text"
                     v-model="form.fields.rib"
                     :error="'rib' in form.errors"
                     :error-message="form.errors.rib?.[0]"
                     hide-bottom-space />
            <div class="col-12">
              {{ console.log("Fields: ", form.fields) }}
              <q-select v-model="form.fields.account_type"
                        :options="accountTypes"
                        label="Account Type"
                        :error="'account_type' in form.errors"
                        :error-message="form.errors.account_type?.[0]"
                        outlined
                        dense
                        hide-bottom-space />
            </div>

            <div class="col-12 text-end">
              <q-btn label="Reset"
                     type="reset"
                     color="primary dark:text-gray-400"
                     flat
                     class="me-3" />

              <q-btn label="Submit"
                     type="submit"
                     class="bg-blue-900 dark:bg-blue-00 text-white" />
            </div>
          </div>

        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { useResourceIndex } from '@/composables/useResourceIndex';
import { onMounted, watch } from 'vue';
import { useQuasar } from 'quasar';
import { useForm } from '@/composables/useForm';
import { useResourceShow } from '@/composables/useResourceShow';

const emit = defineEmits(['updated']);

const visible = defineModel('visible', { type: Boolean, default: false });

const props = defineProps({
  id: {
    type: Number,
  },
})

const $q = useQuasar();

const { fetch: fetchAccount, record } = useResourceShow('accounts');

const form = useForm(() => record.value)

const accountTypes = [
  { label: "Savings", value: "savings" },
  { label: "Investment", value: "investment" },
  { label: "Credit", value: "credit" },
  { label: "Cash", value: "cash" },
  { label: "Retirement", value: "retirement" },
];

async function onSubmit() {
  await form.put(`accounts/${props.id}`, {}, {
    onSuccess: () => {
      $q.notify({
        type: 'positive',
        message: 'Success!',
        caption: 'Account updated successfully.',
        position: 'bottom-right',
        timeout: 3000
      });

      visible.value = false;
      onReset();
      emit('updated');
    }
  });

  router.push({ name: 'accounts.index' });
}

const onReset = () => {
  form.reset();
  form.clearErrors();
};

onMounted(() => {
  fetchAccount();
});

watch([() => props.id, visible], async ([newId, visible]) => {
  if (visible) {
    await fetchAccount(newId);
  }
}, { immediate: true })
</script>
