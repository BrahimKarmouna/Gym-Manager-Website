<template>
  <div
    class="flex min-h-screen w-full bg-gray-900 px-4 py-14"
  >
    <div class="relative p-6 sm:p-0 w-full">
      <div class="relative flex flex-col justify-center w-full h-screen">
        <!-- Login Form -->
        <div
          class="flex flex-col items-center justify-center w-full p-5 md:p-8"
        >
          <div class="max-w-md w-full p-4 md:p-6 sm:p-8 bg-gray-800 rounded-lg shadow-lg">
            <div class="text-center mb-6">
              <h1 class="text-2xl font-semibold text-white mt-4">
                Assistant Login
              </h1>
              <p class="text-sm text-gray-400 mt-2">
                Enter your assistant email and password to sign in!
              </p>
            </div>
            <form
              @submit.prevent="login"
              class="mt-6 space-y-5"
            >
              <div>
                <label
                  for="email"
                  class="block mb-1.5 text-base font-medium text-white"
                >
                  Email<span class="text-red-500">*</span>
                </label>
                <input
                  id="email"
                  name="email"
                  type="email"
                  v-model="email"
                  placeholder="Enter assistant email"
                  class="h-11 w-full rounded-lg border border-gray-600 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-400 focus:border-blue-500 focus:ring-3 focus:ring-blue-500"
                />
                <div v-if="errors.email" class="text-red-500 text-sm mt-1">
                  {{ errors.email }}
                </div>
              </div>
              <div>
                <label
                  for="password"
                  class="block mb-1.5 text-base font-medium text-white"
                >
                  Password<span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    v-model="password"
                    placeholder="Enter password"
                    class="h-11 w-full border border-gray-600 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-400 focus:border-blue-500 focus:ring-3 focus:ring-blue-500"
                  />
                  <button
                    type="button"
                    @click="togglePasswordVisibility"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                  >
                    {{ showPassword ? 'Hide' : 'Show' }}
                  </button>
                </div>
                <div v-if="errors.password" class="text-red-500 text-sm mt-1">
                  {{ errors.password }}
                </div>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="remember"
                      name="remember"
                      type="checkbox"
                      v-model="remember"
                      class="w-4 h-4 border border-gray-600 rounded bg-gray-900 focus:ring-3 focus:ring-primary-300 focus:ring-offset-gray-800"
                    />
                  </div>
                  <span class="ml-2 text-sm text-white">
                    Remember me
                  </span>
                </div>
              </div>
              <button
                type="submit"
                class="w-full border-2-white px-4 py-3 text-base font-semibold text-white rounded-lg bg-blue-600 hover:bg-blue-700"
                :disabled="isSubmitting"
              >
                <span v-if="isSubmitting">Signing in...</span>
                <span v-else>Sign In</span>
              </button>

              <div class="text-sm font-medium text-gray-300 text-center">
                <a
                  href="/login"
                  class="text-blue-600 hover:underline"
                >
                  Go to regular login
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";
import { api } from "../../boot/axios";

const router = useRouter();
const authStore = useAuthStore();

// Simple form state
const email = ref('test.assistant@example.com');
const password = ref('password123');
const remember = ref(false);
const showPassword = ref(false);
const isSubmitting = ref(false);
const errors = ref({});

function handleRedirect(response) {
  const data = response.data;
  
  console.log("Login response:", data);
  
  // Store assistant data in localStorage
  localStorage.setItem("assistant", JSON.stringify(data.assistant));
  localStorage.setItem("assistant_token", data.token);
  
  // Update auth store
  authStore.user = data.assistant;
  authStore.authenticated = true;
  
  // Redirect to dashboard
  router.push("/dashboard");
}

async function login() {
  try {
    // Reset errors
    errors.value = {};
    isSubmitting.value = true;
    
    // Get a CSRF cookie first
    await api.get("sanctum/csrf-cookie");
    
    // Build simple login payload
    const loginData = {
      email: email.value,
      password: password.value,
      remember: remember.value
    };
    
    console.log('Attempting assistant login with:', loginData);
    
    const response = await api.post("/assistant/login", loginData);
    console.log('Login response:', response.data);

    // Store the token in localStorage for API requests
    if (response.data && response.data.token) {
      localStorage.setItem('assistant_token', response.data.token);
      handleRedirect(response);
    }
    
    return response;
  } catch (error) {
    console.error('Login error:', error.response?.data || error.message);
    
    // Handle validation errors
    if (error.response && error.response.status === 422 && error.response.data.errors) {
      errors.value = error.response.data.errors;
    } else if (error.response && error.response.data.message) {
      // General error message
      errors.value.email = error.response.data.message;
    } else {
      errors.value.email = 'An error occurred during login';
    }
  } finally {
    isSubmitting.value = false;
  }
}

function togglePasswordVisibility() {
  showPassword.value = !showPassword.value;
}
</script>
