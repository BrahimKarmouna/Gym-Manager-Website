import { useAuthStore } from "../stores/auth";

export async function VerifyMiddleware({ store, to, next }) {
  const authStore = useAuthStore(store);

  await authStore.fetchProfile();

  const user = authStore.user;

  if (user.email_verified_at === null) {
    return next({ name: "profile.index" });
  }

  return next();
}
