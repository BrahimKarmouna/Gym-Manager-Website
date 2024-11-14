<template>
  <q-card class="max-w-sm mx-auto"
          flat>
    <q-card-section class="h-full p-6">
      <form @submit.prevent="submit"
            class="flex flex-col h-full">
        <div class="mb-5 text-center">
          <!-- Logo -->
          <img src="https://i.ibb.co/D7xPmJR/finance-coin-money-with-flying-wings-logo-3.png"
               alt="Logo"
               class="text-dark w-40 h-30 mx-auto mb-3" />

          <h3 class="text-gray-800 text-lg font-extrabold dark:text-white">
            Reset Password
          </h3>
        </div>

        <q-input outlined
                 label="Email address"
                 dense
                 no-error-icon
                 hide-bottom-space
                 :error="'email' in errors"
                 :error-message="errors.email?.[0]"
                 v-model="form.email"
                 class="mt-1"
                 type="email"
                 required
                 autofocus
                 autocomplete="username" />

        <div class="mt-4">
          <q-btn color="primary"
                 no-caps
                 type="submit"
                 unelevated
                 :loading="submitting"
                 class="w-full rounded-md bg-blue-400 ">
            Send password reset link
          </q-btn>
        </div>

        <div class="text-sm mt-4">
          <span class="text-gray-800 dark:text-white">
            Or
          </span>
          <router-link :to="{ name: 'login' }"
                       class="dark:text-gray-900 underline font-medium">
            return to login
          </router-link>
        </div>
      </form>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { useQuasar } from 'quasar';
import { api } from '../../boot/axios';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

defineProps({
  status: String,
});

const $q = useQuasar();

const router = useRouter();

const errors = ref({});

const submitting = ref(false);

const form = ref({
  email: '',
});

async function submit() {
  errors.value = {};
  submitting.value = true;

  try {
    // Get csrf token
    await api.get("sanctum/csrf-cookie");

    // Send a password reset link to the user's email address
    // using the `forgot-password` endpoint.
    const resp = await api.post('forgot-password', form.value);

    $q.notify({
      type: 'positive',
      message: 'Success!',
      caption: resp.data.message || 'Password reset link sent successfully.',
    });

    router.push({ name: 'login' });
  } catch (err) {
    if (err.response.status === 422) {
      errors.value = err.response.data.errors;
    }
  } finally {
    submitting.value = false;
  }
};

</script>
