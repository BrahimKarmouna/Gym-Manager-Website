<template>
  <q-card class="max-w-sm mx-auto"
          flat>
    <q-card-section class="h-full p-6">
      <form @submit.prevent="login"
            class="flex flex-col h-full">
        <div class="mb-5 text-center">
          <!-- Logo -->
          <img src="https://i.ibb.co/D7xPmJR/finance-coin-money-with-flying-wings-logo-3.png"
               alt="Logo"
               class="text-dark w-40 h-30 mx-auto mb-3" />
          <h3 class="text-gray-800 text-lg font-extrabold dark:text-white">
            Sign in
          </h3>
        </div>

        <q-input outlined
                 class="mb-4"
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
                 placeholder="Enter user name">
          <template #append>
            <q-icon name="sym_r_person"
                    size="18px" />
          </template>
        </q-input>

        <q-password outlined
                    dense
                    class="mb-3"
                    no-error-icon
                    label="Password"
                    :error="'password' in form.errors"
                    :error-message="form.errors.password?.[0]"
                    hide-bottom-space
                    v-model="form.fields.password"
                    autocomplete="current-password"
                    placeholder="Enter password" />

        <q-checkbox label="Remember me"
                    v-model="form.fields.remember"
                    class="inline-flex"
                    size="xs" />

        <div class="mt-4">
          <q-btn class=" w-full rounded-md text-white bg-blue-400 dark:bg-blue-500 dark:text-white "
                 no-caps
                 type="submit"
                 unelevated
                 :loading="form.processing">
            Continue
          </q-btn>
        </div>

        <!-- Forgot password -->
        <div class="text-sm mt-4">
          <span class="text-gray-800 dark:text-white">
            Forgot your password?
          </span>

          <router-link :to="{ name: 'password.reset' }"
                       class="dark:text-white underline font-medium">
            Reset it here
          </router-link><br>
          <router-link class=" text-blue-800 underline my-1 "
                       :to="{ name: 'register' }">
            You dont have an account?
          </router-link>
        </div>
      </form>
    </q-card-section>
  </q-card>
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
