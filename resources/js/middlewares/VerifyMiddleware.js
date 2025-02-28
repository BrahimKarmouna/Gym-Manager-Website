import { useAuthStore } from "../stores/auth";

export async function VerifyMiddleware({ store, to, next }) {
  const authStore = useAuthStore(store);

  await authStore.fetchProfile();

  const user = authStore.user;

  // If the user does not exist, redirect to the register page
  if (!user) {
    return next({ name: "register" });
  }

  // If the user's email is not verified, redirect to profile
  if (user?.email_verified_at === null) {
    return next({ name: "profile.index" });
  }

  return next();
}
