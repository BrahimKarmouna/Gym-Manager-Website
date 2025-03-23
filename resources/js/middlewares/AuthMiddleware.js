import { useAuthStore } from "../stores/auth";

export async function AuthMiddleware({ store, to, next }) {
  const authStore = useAuthStore(store);

  await authStore.fetchProfile();

  if (!authStore.authenticated) {
    return next({ name: "login" });
  }

  // Check if user has necessary role to access the route
  if (to.meta && to.meta.requiredRole && !authStore.hasRole(to.meta.requiredRole)) {
    return next({ name: "unauthorized" });
  }

  return next();
}
