import { useAuthStore } from "../stores/auth";

export async function GuestMiddleware({ store, to, next }) {
  const authStore = useAuthStore(store);

  await authStore.fetchProfile();

  if (authStore.authenticated) {
    return next({ name: "dashboard" });
  }

  return next();
}
