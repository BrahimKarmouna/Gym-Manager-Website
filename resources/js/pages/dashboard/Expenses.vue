<template>
  <div class="w-[49%]">
    <!-- Best Sellers -->
    <q-card class=" dark:bg-gray-800">
      <q-card-section>
        <div class="text-start mb-2 flex items-center justify-between">
          <span class="text-xl q-ml-sm">Expenses</span>
          <!-- Filter By Year -->
          <q-select v-model="selectedYear"
                    dense
                    options-dense
                    :options="years"
                    outlined />
        </div>
        <apexchart v-if="totalExpenses > 0"
                   height="255"
                   type="pie"
                   ref="chartRef"
                   :series="series"
                   :options="productOptions" />
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
import { useQuasar } from "quasar";
import { computed, ref, watch } from "vue";

const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);
const years = Array.from({ length: 10 }, (_, k) => currentYear - k);

const series = ref([]);
const labels = ref([]);
const chartRef = ref();
const totalExpenses = ref([]);

const { execute: fetchExpenses, loading } = useFetch({
  config: {
    url: "get-expenses",
  },
  onSuccess: (response) => {

    // Map to percentages and labels
    series.value = response.data.chart_data.map((item) => item.percentage);
    labels.value = response.data.chart_data.map((item) => item.label);

    totalExpenses.value = response.data.total_expenses;
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

const $q = useQuasar();

// Best Sellers (Product) Chart
const productOptions = computed(() => ({
  theme: {
    mode: $q.dark.isActive ? 'dark' : 'light',
    // palette: 'palette1',
  },
  colors: [
    '#ff0000',
    '#8A2BE2',
    '#FF7F50',
    '#556B2F',
    '#FF8C00',
    '#FFE4E1',
    '#000080',
    '#8B4513',
    '#800080',
    '#7FFFD4',
    '#00FFFF',
    '#2F4F4F',
    '#FF69B4',
  ],

  chart: {
    width: 500,
    type: "pie",
    background: 'transparent'
  },
  legend: {
    position: "left",

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
  labels: labels.value,

}));
</script>
