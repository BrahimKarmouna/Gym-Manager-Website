import { useAuthStore } from "../stores/auth";

export async function GuestMiddleware({ store, to, next }) {
  const authStore = useAuthStore(store);

  try {
    await authStore.fetchProfile();

    if (authStore.authenticated) {
      console.log(`Redirecting authenticated user from ${to.path} to dashboard`);
      return next({ name: "dashboard.index" });
    }
    
    console.log(`Guest accessing ${to.path}`);
    return next();
  } catch (error) {
    console.error("Authentication error:", error);
    return next();
  }
}
