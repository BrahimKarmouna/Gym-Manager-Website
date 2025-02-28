import { useAuthStore } from "../stores/auth";

export async function VerifyMiddleware({ store, to, next }) {
  const authStore = useAuthStore(store);

  await authStore.fetchProfile();

  const user = authStore.user;

  // If there is no user, redirect to the registration page
  if (!user) {
    return next({ name: "register" }); // Adjust route name based on your setup
  }

  // If the user's email is not verified, redirect to profile
  if (user?.email_verified_at === null) {
    return next({ name: "profile.index" });
  }

  return next();
}
