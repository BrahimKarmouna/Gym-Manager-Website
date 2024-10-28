import { api } from "../boot/axios";
import { ref, toValue } from "vue";

export function useResourceShow(resource) {
  const loading = ref(false);

  const record = ref({});

  async function fetch(id) {
    loading.value = true;

    id = toValue(id);

    const url = id ? `${toValue(resource)}/${id}` : toValue(resource);

    return await api
      .get(url)
      .then((response) => {
        record.value = response.data.data;

        return response;
      })
      .finally(() => {
        loading.value = false;
      });
  }

  return {
    loading,
    record,
    fetch,
  };
}
