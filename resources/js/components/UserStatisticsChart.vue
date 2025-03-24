<template>
  <div class="chart-container">
    <Bar
      v-if="loaded"
      :data="chartData"
      :options="chartOptions"
    />
  </div>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
);

export default defineComponent({
  name: 'UserStatisticsChart',
  components: { Bar },
  props: {
    users: {
      type: Array,
      required: true
    }
  },
  setup(props) {
    const loaded = ref(true);

    const chartData = computed(() => {
      const roleStats = {};
      props.users.forEach(user => {
        const roleName = user.role?.name || 'User';
        roleStats[roleName] = (roleStats[roleName] || 0) + 1;
      });

      return {
        labels: Object.keys(roleStats),
        datasets: [
          {
            label: 'Users by Role',
            data: Object.values(roleStats),
            backgroundColor: [
              'rgba(54, 162, 235, 0.5)',
              'rgba(75, 192, 192, 0.5)',
              'rgba(153, 102, 255, 0.5)',
              'rgba(255, 159, 64, 0.5)'
            ],
            borderColor: [
              'rgba(54, 162, 235, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
          }
        ]
      };
    });

    const chartOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        },
        title: {
          display: true,
          text: 'User Distribution by Role'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      }
    };

    return {
      loaded,
      chartData,
      chartOptions
    };
  }
});
</script>

<style scoped>
.chart-container {
  position: relative;
  height: 300px;
  width: 100%;
}
</style>
