<template>
  <slot :startConfirmingPassword="startConfirmingPassword" />

  <q-dialog v-model="confirmingPassword"
            @hide="closeModal">
    <q-card style="width: 450px; max-width: 90vw;">
      <q-card-section class="row items-center pb-0">
        <div class="text-subtitle1 text-weight-medium">
          {{ title }}
        </div>

        <q-space />

        <q-btn icon="sym_r_close"
               flat
               dense
               size="sm"
               padding="xs"
               v-close-popup />
      </q-card-section>

      <q-card-section>
        {{ content }}

        <div class="mt-4">
          <q-password autofocus
                      v-model="form.password"
                      outlined
                      dense
                      hide-bottom-space
                      :error="!!form.error"
                      :error-message="form.error"
                      class="mt-1 block w-3/4"
                      placeholder="Password"
                      autocomplete="current-password"
                      @keyup.enter="confirmPassword" />
        </div>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn @click="closeModal"
               outline
               padding="sm md">
          Cancel
        </q-btn>

        <q-btn class="ms-3"
               color="primary"
               unelevated
               padding="sm md"
               :loading="form.processing"
               @click="confirmPassword">
          {{ button }}
        </q-btn>
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, reactive, nextTick } from 'vue';
import { api } from '@/boot/axios';

const emit = defineEmits(['confirmed']);

defineProps({
  title: {
    type: String,
    default: 'Confirm Password',
  },

  content: {
    type: String,
    default: 'For your security, please confirm your password to continue.',
  },

  button: {
    type: String,
    default: 'Confirm',
  },
});

const confirmingPassword = ref(false);

const form = reactive({
  password: '',
  error: '',
  processing: false,
});

async function startConfirmingPassword() {
  api.get('user/confirmed-password-status').then(async response => {
    if (response.data.confirmed) {
      emit('confirmed');
    } else {
      confirmingPassword.value = true;
    }
  });
};

const confirmPassword = () => {
  form.processing = true;

  api.post('user/confirm-password', {
    password: form.password,
  }).then(() => {
    form.processing = false;

    closeModal();

    nextTick(() => {
      emit('confirmed');
    });

  }).catch(error => {
    form.processing = false;
    form.error = error.response.data.errors.password[0];
  });
};

const closeModal = () => {
  confirmingPassword.value = false;
  form.password = '';
  form.error = '';
};
</script>
