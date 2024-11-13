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
                 placeholder="Enter email"
                 hide-bottom-space
                 :error="'email' in errors"
                 :error-message="errors.email?.[0]"
                 v-model="form.email"
                 class="mb-4"
                 type="email"
                 required
                 autocomplete="username" />

        <q-password outlined
                    label="Password"
                    dense
                    no-error-icon
                    placeholder="Enter a new password"
                    hide-bottom-space
                    :error="'password' in errors"
                    :error-message="errors.password?.[0]"
                    v-model="form.password"
                    class="mb-4"
                    required
                    autofocus
                    autocomplete="new-password" />

        <q-password outlined
                    label="Confirm Password"
                    dense
                    no-error-icon
                    placeholder="Confirm your new password"
                    hide-bottom-space
                    :error="'password_confirmation' in errors"
                    :error-message="errors.password_confirmation?.[0]"
                    v-model="form.password_confirmation"
                    class="mb-4"
                    required
                    autocomplete="new-password" />

        <div class="mt-2">
          <q-btn color="primary"
                 no-caps
                 type="submit"
                 unelevated
                 :loading="submitting"
                 class="w-full rounded-md">
            Reset Password
          </q-btn>
        </div>
      </form>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { useQuasar } from 'quasar';
import { api } from '@/boot/axios';
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  email: String,
  token: String,
});

const $q = useQuasar();

const router = useRouter();

const errors = ref({});

const submitting = ref(false);

const form = ref({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

async function submit() {
  errors.value = {};
  submitting.value = true;

  try {

    // Get csrf token
    await api.get("sanctum/csrf-cookie");

    const resp = await api.post('reset-password', form.value);

    $q.notify({
      type: 'positive',
      message: 'Success!',
      caption: resp.data.message || 'Your password has been reset.',
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

watch(() => router.currentRoute.value.query.email, (newEmail) => {
  form.value.email = newEmail;
}, { immediate: true });
</script>
