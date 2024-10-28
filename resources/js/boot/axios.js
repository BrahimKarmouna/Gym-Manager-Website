import { boot } from "quasar/wrappers";
import axios from "axios";
import { AuthMiddleware } from "../middlewares/AuthMiddleware";

// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: true,
  withXSRFToken: true,

  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

export default boot(({ app, router, store }) => {
  // for use inside Vue files (Options API) through this.$axios and this.$api

  app.config.globalProperties.$axios = axios;
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file

  app.config.globalProperties.$api = api;
  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API

  api.interceptors.response.use(
    (response) => {
      return response;
    },
    async (error) => {
      if (error.response.status === 401) {
        // auto logout if 401 response returned from api
        store.state.value.auth.authenticated = false;

        store.state.value.auth.user = null;

        // Check if the route has AuthMiddleware and redirect to login.
        const middlewares = router.currentRoute.value.matched
          .map((route) => route.meta.middleware)
          .filter(Boolean)
          .flat();

        if (middlewares.includes(AuthMiddleware)) {
          await router.replace({ name: "login" });
        }
      }
      return Promise.reject(error);
    }
  );
});

export { api };
