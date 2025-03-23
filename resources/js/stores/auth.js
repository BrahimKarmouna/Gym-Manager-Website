import { defineStore } from "pinia";
import { api } from "../boot/axios";
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useCartStore } from "./cart";

export const useAuthStore = defineStore("auth", () => {
  const user = ref(null);
  const assistant = ref(null);
  const authenticated = ref(user.value !== null || assistant.value !== null);
  const loadingPromise = ref(null);
  const logoutPromise = ref(null);
  const loggingOut = ref(false);

  const router = useRouter();

  // Computed property to check if user is admin
  const isAdmin = computed(() => {
    return user.value?.is_admin === true;
  });

  // Computed property to check if logged in as assistant
  const isAssistant = computed(() => {
    return assistant.value !== null;
  });

  async function fetchProfile(force = false) {
    // Already loaded.
    if ((user.value !== null || assistant.value !== null) && !force) return;

    // Loading in progress, wait for it to finish.
    if (loadingPromise.value !== null) return loadingPromise;

    try {
      loadingPromise.value = api.get("me");
      const resp = await loadingPromise.value;

      if (resp.data.authenticated_via === 'sanctum_token' && resp.data.assistant) {
        assistant.value = resp.data.assistant;
        user.value = null;
      } else {
        user.value = resp.data.data;
        assistant.value = null;
      }

      authenticated.value = true;
      
      // After successful login, try to merge any cart from localStorage
      if (authenticated.value && user.value) {
        const cartStore = useCartStore();
        await cartStore.mergeCartsAfterLogin();
      }
    } catch (err) {
      console.log({ err });
      user.value = null;
      assistant.value = null;
      authenticated.value = false;
    } finally {
      loadingPromise.value = null;
    }

    return user.value || assistant.value;
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
        
        // Redirect to appropriate dashboard based on user role
        if (user.value) {
          router.push({ name: "dashboard.index" });
        }
        
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

  const loginAsAssistant = async (credentials) => {
    try {
      // Get CSRF token
      await api.get("/sanctum/csrf-cookie");
      
      // Attempt assistant login
      const response = await api.post("/assistant/login", credentials);
      
      // If successful, fetch profile
      if (response.status === 200) {
        // Store the token
        const token = response.data.token;
        localStorage.setItem('assistant_token', token);
        
        // Set the token in axios defaults
        api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        
        await fetchProfile(true);
        
        // Redirect to assistant dashboard
        if (assistant.value) {
          router.push({ name: "assistant.dashboard" });
        }
        
        return { success: true };
      }
      
      return { success: false, error: "Authentication failed" };
    } catch (error) {
      console.error("Assistant login error:", error);
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

      const endpoint = assistant.value ? "/assistant/logout" : "/logout";
      
      logoutPromise.value = await api
        .post(endpoint)
        .then(() => {
          user.value = null;
          assistant.value = null;
          authenticated.value = false;
          
          // Remove assistant token if it exists
          localStorage.removeItem('assistant_token');
          delete api.defaults.headers.common['Authorization'];
          
          // Clear all cart data on logout (only for regular users)
          if (!assistant.value) {
            const cartStore = useCartStore();
            cartStore.clearCart();
          }
          
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
    assistant,
    loggingOut,
    authenticated,
    isAdmin,
    isAssistant,
    login,
    loginAsAssistant,
    logout,
    register,
    fetchProfile,
    hasPermission
  };
});
