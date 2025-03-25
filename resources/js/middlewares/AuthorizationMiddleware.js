import { useAuthStore } from "@/stores/auth";

export function AuthorizationMiddleware(permission) {
  return ({ store, to, next }) => {
    const { user } = useAuthStore();
    if (!user.permissions.includes(permission)) {
      return next({ name: "unauthorized" });
    }

    return next();
  };
}
