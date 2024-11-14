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
            Sign up
          </h3>
        </div>

        <q-input outlined
                 class="mb-4"
                 dense
                 no-error-icon
                 type="name"
                 autofocus
                 label="Name"
                 autocomplete="username"
                 :error="'name' in form.errors"
                 :error-message="form.errors.name?.[0]"
                 hide-bottom-space
                 v-model="form.fields.name"
                 placeholder="Enter user name">
          <template #append>
            <q-icon name="sym_r_person"
                    size="18px" />
          </template>
        </q-input>

        <q-input outlined
                 class="mb-4"
                 dense
                 no-error-icon
                 type="email"
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
                    autocomplete="new-password"
                    placeholder="Enter password" />

        <q-password outlined
                    dense
                    class="mb-3"
                    no-error-icon
                    label="Password confirmation"
                    :error="'password_confirmation' in form.errors"
                    :error-message="form.errors.password_confirmation?.[0]"
                    hide-bottom-space
                    v-model="form.fields.password_confirmation"
                    placeholder="Enter password" />

        <div class="mt-4">
          <q-btn no-caps
                 type="submit"
                 unelevated
                 :loading="form.processing"
                 class="w-full rounded-md bg-blue-400 dark:bg-blue-500 text-white">
            Continue
          </q-btn>
        </div>

        <!-- Forgot password -->
        <div class="text-sm mt-4">
          <span class="text-gray-800 dark:text-white">
            Already have an account?
          </span>
          <router-link :to="{ name: 'login' }"
                       class="dark:text-gray-200 underline font-medium">
            Sign in
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
  name: "",
  password: "",
  password_confirmation: "",

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

    const response = await api.post("register", fields);

    handleRedirect(response);

    return response;
  });
}
</script>
