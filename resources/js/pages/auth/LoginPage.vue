<template>
  <FullScreenLayout>
    <div class="relative p-6  bg-black z-1 sm:p-0 ">
      <div class="relative flex flex-col justify-center w-full h-screen lg:flex-row ">
        <!-- Section de gauche (Formulaire de connexion) -->
        <div class="flex flex-col flex-1 w-full lg:w-1/2 mb-24">
          <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
            <div class="flex items-center justify-center ">
              <img
                class="w-60"
                src="../../../../public/images//logo/logo.png"
                alt=""
                srcset=""
              >

            </div>
            <h1 class="mb-2 text-2xl font-bold text-white sm:text-3xl">
              Sign In
            </h1>
            <p class="text-base text-gray-300">
              Enter your email and password to sign in!
            </p>
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
                  dense
                  no-error-icon
                  type="email"
                  autofocus
                  label="Email"
                  autocomplete="username"
                  :error="'email' in form.errors"
                  :error-message="form.errors.email?.[0]"
                  hide-bottom-space
                  v-model="form.fields.email"
                  placeholder="Enter user name"
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
                    type="password"
                    name="password"
                    no-error-icon
                    label="Password"
                    :error="'password' in form.errors"
                    :error-message="form.errors.password?.[0]"
                    hide-bottom-space
                    v-model="form.fields.password"
                    autocomplete="current-password"
                    placeholder="Enter password"
                    class="h-11 w-full  border border-gray-600 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-400 focus:border-blue-500 focus:ring-3 focus:ring-blue-500"
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
                  </span>
                  <q-checkbox
                    label="Remember me"
                    v-model="form.fields.remember"
                    class="inline-flex text-white"
                    size="xs"
                  />

                </div>
              </div>
              <button
                type="submit"
                class="w-full  border-2-white px-4 py-3 text-base font-semibold text-white rounded-lg  bg-whiterounded-lg hover:bg-gray-400"
              >
                Sign In
              </button>

              <div class="text-white text-center mt-4">
                <p>
                  <router-link to="/assistant-login" class="text-blue-400 hover:underline">
                    Assistant Login
                  </router-link>
                </p>
              </div>
              <!-- Forgot password -->
              <div class="text-sm mt-4">
                <span class="text-white dark:text-white">
                  Forgot your password?
                </span>

                <router-link
                  :to="{ name: 'password.reset' }"
                  class=" text-white dark:text-white underline font-medium"
                >
                  Reset it here
                </router-link><br>
                <router-link
                  class=" text-white underline my-1 "
                  :to="{ name: 'register' }"
                >
                  You dont have an account?
                </router-link>
              </div>
            </form>
          </div>

        </div>
        <!-- Section de droite (Image) -->
        <div class="hidden w-1/2 lg:flex items-center justify-center">
          <img
            src="/backgroundgym.png"
            alt="Login Image"
            class="object-cover w-full mr-14 h-full rounded-lg shadow-lg"
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

const router = useRouter();

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

function handleRedirect(response) {
  // Check if the user has two-factor authentication enabled
  const to = router.currentRoute.value.redirectedFrom?.fullPath ??
    router.resolve({ name: 'dashboard.index' }).fullPath;

  if (response.data.two_factor) {
    router.replace({ name: "auth.two-factor", query: { next: to } });
  } else {
    router.push(to);
  }
}

async function login() {
  await form.submit(async (fields) => {
    await api.get("sanctum/csrf-cookie");

    const response = await api.post("login", fields);

    handleRedirect(response);

    return response;
  });
}
</script>
