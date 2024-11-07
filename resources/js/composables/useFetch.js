import { api } from "../boot/axios";
import { ref } from "vue";
import { mergeDeep } from "./utils/deep-merge";

/**
 *
 * @param {{
 *    onSuccess: (resp: any) => void,
 *    onError: (err: any) => void,
 *    onFinally: () => void,
 *    config: import("axios").AxiosRequestConfig,
 * }} param
 * @returns
 */
export function useFetch({ onSuccess, onError, onFinally, config } = {}) {
  /**
   * @type {import("vue").Ref<boolean>} The loading state of the request.
   */
  const loading = ref(false);

  /**
   * @type {import("vue").Ref<boolean>} The loading state of the request.
   */
  const initialLoading = ref(false);

  /**
   * @type {boolean} Whether the request has been loaded.
   */
  let loaded = false;

  /**
   * @type {import("vue").Ref<any>} The validation errors
   */
  const validation = ref({});

  const globalConfig = config;

  const globalOnSuccess = onSuccess;
  const globalOnError = onError;
  const globalOnFinally = onFinally;

  /**
   * Execute a post request to the given url with the given data and config.
   *
   * @param {{
   *  onSuccess: (resp: any) => void,
   *  onError: (err: any) => void,
   *  onFinally: () => void,
   *  config: import("axios").AxiosRequestConfig,
   * }} param
   *
   * @returns
   */
  const execute = async (
    { onSuccess, onError, onFinally, config } = { config: {} }
  ) => {
    if (!loaded) {
      initialLoading.value = false;
    }

    loaded = true;
    loading.value = true;
    validation.value = {};

    config = mergeDeep(structuredClone(globalConfig || {}), config || {});

    return api(config)
      .then(async (resp) => {
        globalOnSuccess && (await Promise.resolve(globalOnSuccess(resp)));
        onSuccess && (await Promise.resolve(onSuccess(resp)));

        return resp;
      })
      .catch(async (err) => {
        if (err.response && err.response.status === 422) {
          validation.value = err.response.data.errors;
        }

        globalOnError && (await Promise.resolve(globalOnError(err)));
        onError && (await Promise.resolve(onError(err)));

        throw err;
      })
      .finally(async () => {
        loading.value = false;
        initialLoading.value = false;

        globalOnFinally && (await Promise.resolve(globalOnFinally()));
        onFinally && (await Promise.resolve(onFinally()));
      });
  };

  return {
    validation,
    loading,
    initialLoading,

    execute,
  };
}
