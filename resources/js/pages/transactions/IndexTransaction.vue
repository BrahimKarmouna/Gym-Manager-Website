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
        <q-route-tab name="Transfer"
                                 :to="{ name: 'transaction.transfer' }"
                                 label="Transfer">
                        
                    </q-route-tab>
          <q-route-tab name="Income"
                                 :to="{ name: 'transfer.income' }"
                                 label="Income">
                        
                    </q-route-tab>
                    <q-route-tab name="Expense"
                                 :to="{ name: 'transfer.expense' }"
                                 label="Expense">
                        
                    </q-route-tab> 
        </q-tabs>
        <q-separator />

        <RouterView />
       
      </q-card>
    </div>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue';
import { useQuasar } from 'quasar';

export default {
  setup() {
    const $q = useQuasar();
    const loading = ref(false);
    const filter = ref('');
    const rows = ref([]);
    const dialog = ref(false);

    // call Account, transaction_categories
    return {
      tab: ref('Transfer'),
    }
  },
};


</script>

