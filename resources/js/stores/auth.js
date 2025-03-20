import { defineStore } from "pinia";
import { api } from "../boot/axios";
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useCartStore } from "./cart";

export const useAuthStore = defineStore("auth", () => {
  const user = ref(null);
  const authenticated = ref(user.value !== null);
  const loadingPromise = ref(null);
  const logoutPromise = ref(null);
  const loggingOut = ref(false);

  const router = useRouter();

  // Computed property to check if user is admin
  const isAdmin = computed(() => {
    return user.value?.is_admin === true;
  });

  async function fetchProfile(force = false) {
    // Already loaded.
    if (user.value !== null && !force) return;

    // Loading in progress, wait for it to finish.
    if (loadingPromise.value !== null) return loadingPromise;

    try {
      loadingPromise.value = api.get("me");

      const resp = await loadingPromise.value;

      user.value = resp.data.data;

      // permissions.value = resp.data.data.permissions;

      authenticated.value = true;
      
      // After successful login, try to merge any cart from localStorage
      if (authenticated.value) {
        const cartStore = useCartStore();
        await cartStore.mergeCartsAfterLogin();
      }
    } catch (err) {
      console.log({ err });
    } finally {
      loadingPromise.value = null;
    }

    return user.value;
  }

  const login = async (credentials) => {
    try {
      // Get CSRF token
      await api.get("/sanctum/csrf-cookie");
      
      // Attempt login
      const response = await api.post("/login", credentials);
      
      // If successful, fetch user profile
      if (response.status === 200) {
        await fetchProfile(true);
        
        // After successful login, merge any guest cart with user cart
        const cartStore = useCartStore();
        await cartStore.mergeCartsAfterLogin();
        
        return { success: true };
      }
      
      return { success: false, error: "Authentication failed" };
    } catch (error) {
      console.error("Login error:", error);
      return { 
        success: false, 
        error: error.response?.data?.message || "Authentication failed"
      };
    }
  };

  const logout = async () => {
    if (logoutPromise.value) await logoutPromise.value;

    try {
      // Get CSRF token.
      await api.get("/sanctum/csrf-cookie");

      logoutPromise.value = await api
        .post("/logout")
        .then(() => {
          user.value = null;
          authenticated.value = false;
          
          // Clear all cart data on logout
          const cartStore = useCartStore();
          cartStore.clearCart();
          
          // Redirect to login page
          router.push({ name: "login" });
        })
        .catch((err) => {
          console.log({ err });
        })
        .finally(() => {
          loggingOut.value = false;
        });
    } catch (err) {
      console.log({ err });
    } finally {
      logoutPromise.value = null;
    }

    return await logoutPromise.value;
  };

  // Register a new user
  const register = async (userData) => {
    try {
      // Get CSRF token
      await api.get("/sanctum/csrf-cookie");
      
      // Attempt registration
      const response = await api.post("/register", userData);
      
      // If successful, fetch user profile
      if (response.status === 201) {
        await fetchProfile(true);
        
        // After successful registration, merge any guest cart with user cart
        const cartStore = useCartStore();
        await cartStore.mergeCartsAfterLogin();
        
        return { success: true };
      }
      
      return { success: false, error: "Registration failed" };
    } catch (error) {
      console.error("Registration error:", error);
      return { 
        success: false, 
        error: error.response?.data?.message || "Registration failed"
      };
    }
  };

  // Check if user has required permissions for a page
  const hasPermission = (requiredPermission) => {
    // For admin users, grant all permissions
    if (isAdmin.value) return true;
    
    // For specific permission checks
    if (user.value?.permissions?.includes(requiredPermission)) {
      return true;
    }
    
    return false;
  };

  return {
    user,
    loggingOut,
    authenticated,
    isAdmin,
    logout,
    login,
    register,
    fetchProfile,
    hasPermission
  };
});
