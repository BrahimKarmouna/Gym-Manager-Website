<template>
  <q-dialog v-model="visible" persistent>
    <q-card style="width: 500px">
      <q-card-section>
        <div class="flex justify-between items-center">
          <div class="text-h6">Edit Income</div>

          <q-btn icon="close" flat dense v-close-popup />
        </div>
      </q-card-section>
      <q-card-section class="pt-0">
        <q-form @submit.prevent="onSubmit" @reset="onReset" class="space-y-4">
          <div class="row q-col-gutter-md">
            <div class="col-12">
              <q-input
                label="Date"
                outlined
                dense
                v-model="form.fields.date"
                hide-bottom-space
                :error="'date' in form.errors"
                :error-message="form.errors.date?.[0]"
                mask="date"
              >
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy
                      cover
                      transition-show="scale"
                      transition-hide="scale"
                    >
                      <q-date v-model="form.fields.date">
                        <div class="row items-center justify-end">
                          <q-btn
                            v-close-popup
                            label="Close"
                            color="primary"
                            flat
                          />
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </div>

            <div class="col-12">
              <q-input
                v-model="form.fields.amount"
                type="number"
                label="Amount"
                outlined
                dense
                :error="'amount' in form.errors"
                :error-message="form.errors.amount?.[0]"
                lazy-rules
                hide-bottom-space
              />
            </div>

            <!-- Select  From Account -->
            <div class="col-md-6">
              <q-select
                v-model="form.fields.source_account"
                :options="accounts"
                option-value="id"
                outlined
                dense
                option-label="name"
                label="To"
                :error="'source_account_id' in form.errors"
                :error-message="form.errors.source_account_id?.[0]"
                hide-bottom-space
              />
            </div>

            <!-- Select a category -->
            <div class="col-6">
              <q-select
                v-model="form.fields.category"
                :options="transaction_categories"
                option-value="id"
                outlined
                dense
                option-label="name"
                label="Category"
                :error="'category_id' in form.errors"
                :error-message="form.errors.category_id?.[0]"
                hide-bottom-space
              />
            </div>

            <div class="col-12">
              <q-input
                v-model="form.fields.note"
                label="Note"
                type="textarea"
                outlined
                dense
                lazy-rules
                :error="'note' in form.errors"
                :error-message="form.errors.note?.[0]"
                hide-bottom-space
              />
            </div>

            <div class="col-12 text-end">
              <q-btn
                label="Reset"
                type="reset"
                color="primary dark:text-gray-400"
                flat
                class="me-3"
              />

              <q-btn
                label="Submit"
                type="submit"
                class="bg-blue-900 dark:bg-blue-400 text-white"
              />
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { useResourceIndex } from "@/composables/useResourceIndex";
import { onMounted, watch } from "vue";
import { useQuasar } from "quasar";
import { useForm } from "@/composables/useForm";
import { useResourceShow } from "@/composables/useResourceShow";

const emit = defineEmits(["updated"]);

const visible = defineModel("visible", { type: Boolean, default: false });

const props = defineProps({
  id: {
    type: Number,
  },
});

const $q = useQuasar();

const { fetch: fetchTransaction, record } = useResourceShow("transactions");
const { data: accounts, fetch: fetchAccount } = useResourceIndex("accounts");
const {
  data: transaction_categories,
  fetch: fetchTransactionCategories,
  loading: transaction_categories_loading,
} = useResourceIndex("categories?transaction_type=income");

const form = useForm(() => record.value);

async function onSubmit() {
  await form.put(
    `transactions/${props.id}`,
    {},
    {
      onSuccess: () => {
        $q.notify({
          type: "positive",
          message: "Success!",
          caption: "Income updated successfully.",
          position: "bottom-right",
          timeout: 3000,
        });

        visible.value = false;
        onReset();
        emit("updated");
      },
    }
  );

  router.push({ name: "transaction.index" });
}

const onReset = () => {
  form.reset();
  form.clearErrors();
};

onMounted(() => {
  fetchAccount();
  fetchTransactionCategories();
});

watch(
  [() => props.id, visible],
  async ([newId, visible]) => {
    if (visible) {
      await fetchTransaction(newId);
    }
  },
  { immediate: true }
);
</script>
