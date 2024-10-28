import { defineStore } from "pinia";
import { api } from "../boot/axios";
import { ref } from "vue";
import { useRouter } from "vue-router";

export const useAuthStore = defineStore("auth", () => {
  const user = ref(null);

  const authenticated = ref(user.value !== null);

  const loadingPromise = ref(null);
  const logoutPromise = ref(null);
  const loggingOut = ref(false);

  const router = useRouter();

  async function fetchProfile(force = false) {
    // Already loaded.
    if (user.value !== null && !force) return;

    // Loading in progress, wait for it to finish.
    if (loadingPromise.value !== null) return loadingPromise;

    try {
      loadingPromise.value = api.get("me");

      const resp = await loadingPromise.value;

      user.value = resp.data.data;

      // permissions.value = resp.data.data.permissions;

      authenticated.value = true;
    } catch (err) {
      console.log({ err });
    } finally {
      loadingPromise.value = null;
    }

    return user.value;
  }

  const logout = async () => {
    if (logoutPromise.value) await logoutPromise.value;

    try {
      // Get CSRF token.
      await api.get("/sanctum/csrf-cookie");

      logoutPromise.value = await api
        .post("/logout")
        .then(async (resp) => {
          authenticated.value = false;

          await router.replace({ name: "login" });

          user.value = null;
        })
        .finally(() => {
          loggingOut.value = false;
        });
    } catch (err) {
      console.log({ err });
    } finally {
      logoutPromise.value = null;
    }

    return await logoutPromise.value;
  };

  return {
    user,
    // home,
    loggingOut,
    authenticated,
    // can,
    // cannot,
    logout,
    fetchProfile,
  };
});
