import { useAuthStore } from "@/stores/auth";

export const usePermissions = () => {
  function hasPermission(requiredPermission) {

    const { user } = useAuthStore();
    console.log(user);
    // For specific permission checks
    if (user?.permissions) {
      return user.permissions.some(p =>
        typeof p === 'string'
          ? p === requiredPermission
          : p.name === requiredPermission
      );
    }

    return false;
  };

  return {
    hasPermission
  };
}

// const hasAnyPermission = (requiredPermissions) => {
//   // user has *
//   if (user.value?.permissions?.some(p => p.name === '*')) return true;

//   // For specific permission checks
//   if (user.value?.permissions) {
//     return user.value.permissions.some(p =>
//       typeof p === 'string'
//         ? p === requiredPermissions
//         : p.name === requiredPermission
//     );
//   }

//   return false;
// };