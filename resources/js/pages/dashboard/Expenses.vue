<template>
  <div class="w-[49%]">
    <!-- Best Sellers -->
    <q-card class="my-shadow w-full">
      <q-card-section>
        <div class="text-start mb-3 flex items-center justify-between">
          <span class="text-xl q-ml-sm">Expenses</span>
          <!-- Filter By Year -->
          <q-select v-model="selectedYear"
                    dense
                    options-dense
                    :options="years"
                    outlined />
        </div>
        <apexchart v-if="expenseSeries.length > 0"
                   height="255"
                   type="pie"
                   :options="productOptions"
                   :series="expenseSeries" />
        <div v-else
             class="text-center h-[219px] pt-20">
          <q-icon name="fa-solid fa-exclamation-circle"
                  size="lg"
                  color="red" />
          <span class="text-red-500 text-lg pl-3">No data available</span>
        </div>
      </q-card-section>
      <q-inner-loading :showing="loading" />
    </q-card>
  </div>
</template>

<script setup>
import { useFetch } from "@/composables/useFetch";
import { computed, ref, watch } from "vue";

const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);
const years = Array.from({ length: 10 }, (_, k) => currentYear - k);

const expenseSeries = ref([]);
const expenseLabels = ref([]);

const { execute: fetchExpenses, loading } = useFetch({
  config: {
    url: "get-expenses",
  },
  onSuccess: (response) => {
    const data = Object.values(response.data); // Convert the object into an array of category objects

    if (Array.isArray(data) && data.length > 0) {
      // Map to percentages and labels
      expenseSeries.value = data.map((item) => item.percentage);
      expenseLabels.value = data.map((item) => item.label);
    } else {
      console.error('Invalid or empty response data:', response.data);
    }
  },
  onError: (err) => {
    console.log({ err });
  },
});

watch(
  selectedYear,
  () => {
    fetchExpenses({
      config: {
        params: {
          year: selectedYear.value,
        },
      },
    });
  },
  { immediate: true }
);

// Best Sellers (Product) Chart
const productOptions = computed(() => ({
  chart: {
    width: 500,
    type: "pie",
  },
  legend: {
    position: "bottom",
  },
  dataLabels: {
    enabled: true,
    formatter: (val) => `${Math.round(val)}%`,
    style: {
      fontSize: "12px",
      fontFamily: "Helvetica, Arial, sans-serif",
      fontWeight: "bold",
    },
  },
  labels: expenseLabels.value,
}));
</script>
