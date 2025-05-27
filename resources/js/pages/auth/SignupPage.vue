<template>
  <FullScreenLayout>
    <div class="relative p-6 bg-black z-1 sm:p-0">
      <div class="relative flex flex-col justify-center w-full min-h-screen lg:flex-row">
        <!-- Section de gauche (Formulaire de connexion) -->
        <div class="flex flex-col flex-1 w-full lg:w-1/2">
          <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto py-4">
            <div class="flex items-center justify-center">
              <img
                class="w-40"
                src="../../../../public/images//logo/logo.png"
                alt=""
                srcset=""
              >
            </div>
            <h1 class="mb-1 text-xl font-bold text-white sm:text-2xl">
              Sign Up
            </h1>
            <p class="text-sm text-gray-300">
              Enter your details to create a new account!
            </p>
            
            <!-- Form container with fixed height and scroll -->
            <div class="mt-4 flex flex-col h-[420px] overflow-y-auto scrollbar-thin pr-2">
              <form
                @submit.prevent="register"
                class="space-y-3"
              >
                <div>
                  <label
                    for="name"
                    class="block mb-1.5 text-base font-medium text-white"
                  >
                    Name<span class="text-red-500">*</span>
                  </label>
                  <input
                    dense
                    no-error-icon
                    type="text"
                    autofocus
                    label="Name"
                    autocomplete="name"
                    :error="'name' in form.errors"
                    :error-message="form.errors.name?.[0]"
                    hide-bottom-space
                    v-model="form.fields.name"
                    placeholder="Enter your name"
                    class="h-11 w-full rounded-lg border border-gray-600 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-400 focus:border-blue-500 focus:ring-3 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label
                    for="email"
                    class="block mb-1.5 text-base font-medium text-white"
                  >
                    Email<span class="text-red-500">*</span>
                  </label>
                  <input
                    dense
                    no-error-icon
                    type="email"
                    label="Email"
                    autocomplete="email"
                    :error="'email' in form.errors"
                    :error-message="form.errors.email?.[0]"
                    hide-bottom-space
                    v-model="form.fields.email"
                    placeholder="Enter your email"
                    class="h-11 w-full rounded-lg border border-gray-600 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-400 focus:border-blue-500 focus:ring-3 focus:ring-blue-500"
                  />
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
                      outlined
                      dens
                      :type="showPassword ? 'text' : 'password'"
                      name="password"
                      no-error-icon
                      label="Password"
                      :error="'password' in form.errors"
                      :error-message="form.errors.password?.[0]"
                      hide-bottom-space
                      v-model="form.fields.password"
                      autocomplete="new-password"
                      placeholder="Enter password"
                      class="h-11 w-full rounded-lg border border-gray-600 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-400 focus:border-blue-500 focus:ring-3 focus:ring-blue-500"
                    />
                    <span
                      @click="togglePasswordVisibility"
                      class="absolute text-gray-300 -translate-y-1/2 cursor-pointer right-4 mt-5"
                    >
                      <svg
                        v-if="!showPassword"
                        class="fill-current"
                        width="20"
                        height="20"
                        viewBox="0 0 20 20"
                      >
                        <path
                          d="M10 4C5 4 2 10 2 10s3 6 8 6 8-6 8-6-3-6-8-6zM10 14c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"
                        />
                      </svg>
                      <svg
                        v-else
                        class="fill-current"
                        width="20"
                        height="20"
                        viewBox="0 0 20 20"
                      >
                        <path
                          d="M10 4C5 4 2 10 2 10s3 6 8 6 8-6 8-6-3-6-8-6zM10 14c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"
                        />
                        <line x1="4" y1="16" x2="16" y2="4" stroke="currentColor" stroke-width="2"/>
                      </svg>
                    </span>
                  </div>
                </div>
                <div>
                  <label
                    for="password_confirmation"
                    class="block mb-1.5 text-base font-medium text-white"
                  >
                    Confirm Password<span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      outlined
                      dens
                      :type="showPassword ? 'text' : 'password'"
                      name="password_confirmation"
                      no-error-icon
                      label="Confirm Password"
                      :error="'password_confirmation' in form.errors"
                      :error-message="form.errors.password_confirmation?.[0]"
                      hide-bottom-space
                      v-model="form.fields.password_confirmation"
                      autocomplete="new-password"
                      placeholder="Confirm your password"
                      class="h-11 w-full rounded-lg border border-gray-600 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-400 focus:border-blue-500 focus:ring-3 focus:ring-blue-500"
                    />
                    <span
                      @click="togglePasswordVisibility"
                      class="absolute text-gray-300 -translate-y-1/2 cursor-pointer right-4 mt-5"
                      v-if="form.fields.password_confirmation"
                    >
                      <svg
                        v-if="!showPassword"
                        class="fill-current"
                        width="20"
                        height="20"
                        viewBox="0 0 20 20"
                      >
                        <path
                          d="M10 4C5 4 2 10 2 10s3 6 8 6 8-6 8-6-3-6-8-6zM10 14c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"
                        />
                      </svg>
                      <svg
                        v-else
                        class="fill-current"
                        width="20"
                        height="20"
                        viewBox="0 0 20 20"
                      >
                        <path
                          d="M10 4C5 4 2 10 2 10s3 6 8 6 8-6 8-6-3-6-8-6zM10 14c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"
                        />
                        <line x1="4" y1="16" x2="16" y2="4" stroke="currentColor" stroke-width="2"/>
                      </svg>
                    </span>
                  </div>
                </div>
              </form>
            </div>
            
            <!-- Submit button outside scrollable area to always be visible -->
            <div class="pt-4 pb-2">
              <!-- Error message display -->
              <div v-if="errorMessage" class="mb-3 p-3 bg-red-600 bg-opacity-30 border border-red-600 rounded text-white text-sm">
                {{ errorMessage }}
              </div>
              
              <!-- Success message display -->
              <div v-if="successMessage" class="mb-3 p-3 bg-green-600 bg-opacity-30 border border-green-600 rounded text-white text-sm">
                {{ successMessage }}
              </div>
              
              <button
                type="submit"
                @click="register"
                class="w-full px-4 py-3 text-base font-semibold text-black rounded-lg bg-white hover:bg-gray-200 transition-colors duration-200 border border-white relative"
                :disabled="loading"
              >
                <span v-if="!loading">Sign Up</span>
                <div v-else class="flex items-center justify-center">
                  <svg class="animate-spin h-5 w-5 mr-2 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Processing...
                </div>
              </button>
              
              <div class="text-sm mt-3">
                <span class="text-white dark:text-white">
                  Already have an account?
                </span>

                <router-link
                  :to="{ name: 'login' }"
                  class="text-white dark:text-white underline font-medium ml-1 hover:text-gray-300"
                >
                  Log in here
                </router-link>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Section de droite (Image) -->
        <div class="hidden w-1/2 lg:flex items-center justify-center">
          <img
            src="/backgroundgym.png"
            alt="Login Image"
            class="object-cover w-3/4 mx-auto h-3/4 rounded-lg shadow-lg"
          />
        </div>
      </div>
    </div>
  </FullScreenLayout>
</template>

<script setup>
import { api } from "@/boot/axios";
import { useForm } from "@/composables/useForm";
import { useRouter } from "vue-router";
import FullScreenLayout from '../../components/layout/FullScreenLayout.vue'
import { ref } from 'vue';

const router = useRouter();
const showPassword = ref(false);
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

function togglePasswordVisibility() {
  showPassword.value = !showPassword.value;
}

function handleRedirect(response) {
  // Show success message before redirect
  successMessage.value = 'Registration successful! Redirecting...';
  
  // Check if the user has two-factor authentication enabled
  const to = router.currentRoute.value.redirectedFrom?.fullPath ??
    router.resolve({ name: 'dashboard.index' }).fullPath;

  // Small delay for user to see success message
  setTimeout(() => {
    if (response.data.two_factor) {
      router.replace({ name: "auth.two-factor", query: { next: to } });
    } else {
      router.push(to);
    }
  }, 1000);
}

async function register() {
  // Clear previous messages
  errorMessage.value = '';
  successMessage.value = '';
  
  // Set loading state
  loading.value = true;
  
  await form.submit(async (fields) => {
    try {
      await api.get("sanctum/csrf-cookie");
      const response = await api.post("register", fields);
      handleRedirect(response);
      return response;
    } catch (error) {
      // Display error message
      if (error.response && error.response.data && error.response.data.message) {
        errorMessage.value = error.response.data.message;
      } else {
        errorMessage.value = 'An error occurred during registration. Please try again.';
      }
      
      // Handle duplicate email error
      if (error.response && error.response.data && error.response.data.errors && error.response.data.errors.email) {
        form.errors.email = error.response.data.errors.email;
      }
      throw error;
    } finally {
      // Reset loading state
      loading.value = false;
    }
  });
}
</script>

<style scoped>
/* Custom scrollbar styling */
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
  background: #1a1a1a;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: #4a4a4a;
  border-radius: 20px;
}
</style>
