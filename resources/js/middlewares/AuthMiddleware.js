import { useAuthStore } from "../stores/auth";

export async function AuthMiddleware({ store, to, next }) {
  const authStore = useAuthStore(store);

  await authStore.fetchProfile();

  if (!authStore.authenticated) {
    return next({ name: "login" });
  }

  return next();
}
