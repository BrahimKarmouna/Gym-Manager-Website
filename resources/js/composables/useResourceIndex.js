import { api } from "../boot/axios";
import { ref, toValue, watchEffect } from "vue";

export function useResourceIndex(resource) {
  const data = ref(null);

  const loading = ref(false);

  const pagination = ref({
    rowsPerPage: 10,
    rowsNumber: 0,
    page: 1,
  });

  const filter = ref("");

  async function fetch() {
    loading.value = true;

    return await api
      .get(toValue(resource))
      .then((response) => {
        data.value = response.data.data;

        if (!response.data.meta) {
          return;
        }

        pagination.value.rowsNumber = response.data.meta.total;
        pagination.value.page = response.data.meta.current_page;
        pagination.value.rowsPerPage = response.data.meta.per_page;
      })
      .catch((error) => {
        throw error;
      })
      .finally(() => {
        loading.value = false;
      });
  }

  watchEffect(() => {
    const _ = toValue(resource);

    data.value = null;
  });

  return {
    data,
    pagination,
    filter,
    loading,
    fetch,
  };
}
