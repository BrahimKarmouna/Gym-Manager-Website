import { useQuasar } from "quasar";
import { reactive, ref, toValue } from "vue";
import { useFetch } from "./useFetch";

export function useResourceIndex(
  resource,
  initOptions = {},
  { onSuccess, onError, onFinally, config } = {}
) {
  const $q = useQuasar();

  const options = reactive({
    search: null,
    pagination: {
      sortBy: null,
      descending: false,
      page: 1,
      rowsNumber: 0,
      rowsPerPage: initOptions.pagination?.rowsPerPage ?? 10,
      from: 0,
      to: 0,
    },

    filters: initOptions.filters ?? {},

    meta: {},
  });

  const data = ref(null);

  const { loading, initialLoading, execute } = useFetch({
    config,

    onSuccess(resp) {
      data.value = resp.data.data;

      if (onSuccess) onSuccess(resp);

      if (!resp.data.meta) return;

      // Extract pagination
      options.pagination.rowsPerPage = resp.data.meta.per_page;
      options.pagination.page = resp.data.meta.current_page;
      options.pagination.from = resp.data.meta.from;
      options.pagination.to = resp.data.meta.to;
      options.pagination.rowsNumber = resp.data.meta.total;

      // Extract meta
      options.meta = resp.data.meta;
    },

    onError(err) {
      $q.notify({
        type: "negative",
        message: "Error",
        caption:
          err.response.data.message ??
          "Something went wrong. Please try again.",
        closeBtn: true,
        timeout: 3000,
      });

      if (onError) onError(err);
    },

    onFinally() {
      if (onFinally) onFinally();
    },
  });

  const getSortBy = () => {
    if (!options.pagination.sortBy) return null;

    return `${options.pagination.descending ? "-" : ""}${
      options.pagination.sortBy
    }`;
  };

  const fetch = async () => {
    return await execute({
      config: {
        url: toValue(resource),
        params: {
          page: options.pagination.page,
          search: options.search,
          per_page: options.pagination.rowsPerPage,
          sort: getSortBy(),
          filter: options.filters,
        },
      },
    });
  };

  const onRequest = ({ pagination, filter }) => {
    options.filters = filter;
    options.pagination.page = pagination.page;
    options.pagination.rowsPerPage = pagination.rowsPerPage;
    options.pagination.sortBy = pagination.sortBy;
    options.pagination.descending = pagination.descending;

    fetch();
  };

  return {
    options,
    loading,
    initialLoading,
    data,
    onRequest,
    fetch,
  };
}
