import { useAuthStore } from "@/stores/auth";

export const usePermissions = () => {
  function hasPermission(requiredPermission) {
    const { user } = useAuthStore();
    
    // If user has admin role, allow everything
    if (user?.is_admin) {
      return true;
    }
    
    // For specific permission checks
    if (user?.permissions) {
      // Normalize the permission name (handle both formats: with spaces and with hyphens)
      const normalizedRequired = requiredPermission.replace(/[-\s]/g, '').toLowerCase();
      
      return user.permissions.some(p => {
        const permName = typeof p === 'string' ? p : (p.name || '');
        const normalizedPerm = permName.replace(/[-\s]/g, '').toLowerCase();
        return normalizedPerm === normalizedRequired;
      });
    }

    return false;
  };

  return {
    hasPermission
  };
}