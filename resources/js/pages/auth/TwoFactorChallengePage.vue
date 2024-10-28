<template>
  <q-card class="max-w-sm mx-auto"
          flat>
    <q-card-section class="h-full p-6">
      <form @submit.prevent="submit"
            class="flex flex-col h-full">
        <div class="mb-5 text-center">
          <!-- Logo -->
          <img src="~assets/logo.svg"
               alt="Logo"
               class="text-dark w-10 h-10 mx-auto mb-3" />

          <h3 class="text-gray-800 text-lg font-extrabold dark:text-white">
            Authentication Code
          </h3>
        </div>

        <template v-if="recovery">
          <q-input outlined
                   label="Recovery Code"
                   dense
                   no-error-icon
                   placeholder="Enter recovery code"
                   hide-bottom-space
                   :error="'recovery_code' in errors"
                   :error-message="errors.recovery_code?.[0]"
                   v-model="form.recovery_code"
                   type="text"
                   class="mt-1"
                   autofocus
                   autocomplete="one-time-code" />
        </template>

        <template v-else>

          <q-input outlined
                   dense
                   label="Code"
                   no-error-icon
                   placeholder="Enter code"
                   hide-bottom-space
                   :error="'code' in errors"
                   :error-message="errors.code?.[0]"
                   v-model="form.code"
                   type="text"
                   inputmode="numeric"
                   class="mt-1"
                   autofocus
                   autocomplete="one-time-code" />
        </template>

        <div class="mt-4">
          <q-btn color="dark"
                 no-caps
                 type="submit"
                 unelevated
                 :loading="submitting"
                 class="w-full rounded-md">
            Continue
          </q-btn>
        </div>

        <!-- Forgot password -->
        <div class="text-sm mt-4">
          <span class="text-gray-800 dark:text-white">
            Or you can
          </span>

          <a href="#"
             @click="toggleRecovery"
             class="dark:text-gray-900 underline font-medium">
            <template v-if="!recovery">
              login using a recovery code
            </template>

            <template v-else>
              login using an authentication code
            </template>
          </a>
        </div>
      </form>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { api } from 'src/boot/axios';
import { ref, nextTick } from 'vue'
import { useRouter } from 'vue-router';

const recovery = ref(false);

const form = ref({
  code: '',
  recovery_code: '',
});

const errors = ref({});

const submitting = ref(false);

const router = useRouter();

const toggleRecovery = async () => {
  recovery.value ^= true;

  await nextTick();

  if (recovery.value) {
    form.value.code = undefined;
  } else {
    form.value.recovery_code = undefined;
  }
};

function submit() {
  submitting.value = true;

  api
    .post('/two-factor-challenge', form.value)
    .then(() => {
      // Redirect to the dashboard
      const to = router.currentRoute.value.query.next ?? { name: 'dashboard' };

      router.push(to);
    })
    .catch((err) => {
      if (err.response.status === 422) {
        errors.value = err.response.data.errors;
      }
    }).finally(() => {
      submitting.value = false;
    });
};
</script>
