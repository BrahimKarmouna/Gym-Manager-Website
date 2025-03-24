/**
 * Permissions utility for frontend permission checks
 */

import { useAuthStore } from '../stores/auth';

/**
 * Check if the current user has a specific permission
 * 
 * @param {string} permission - The permission to check
 * @returns {boolean} - Whether the user has the permission
 */
export function hasPermission(permission) {
  const authStore = useAuthStore();
  
  // Admin users have all permissions (role ID 1)
  if (authStore.hasRole(1)) {
    return true;
  }
  
  // Check if the user has the specific permission
  if (authStore.user && authStore.user.permissions) {
    return authStore.user.permissions.some(p => p.name === permission);
  }
  
  return false;
}

/**
 * Check if the current user has any of the specified permissions
 * 
 * @param {Array} permissions - Array of permissions to check
 * @returns {boolean} - Whether the user has any of the permissions
 */
export function hasAnyPermission(permissions) {
  const authStore = useAuthStore();
  
  // Admin users have all permissions (role ID 1)
  if (authStore.hasRole(1)) {
    return true;
  }
  
  // Check if the user has any of the specified permissions
  if (authStore.user && authStore.user.permissions) {
    return authStore.user.permissions.some(p => permissions.includes(p.name));
  }
  
  return false;
}

/**
 * Check if the current user has a specific role
 * 
 * @param {string|number} role - The role name or ID to check
 * @returns {boolean} - Whether the user has the role
 */
export function hasRole(role) {
  const authStore = useAuthStore();
  return authStore.hasRole(role);
}

/**
 * Check if the current user is an admin
 * 
 * @returns {boolean} - Whether the user is an admin
 */
export function isAdmin() {
  const authStore = useAuthStore();
  return authStore.hasRole(1);
}
