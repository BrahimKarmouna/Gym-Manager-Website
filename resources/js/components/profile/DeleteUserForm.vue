<template>
  <q-dialog v-model="confirmingUserDeletion" @hide="closeModal">
    <q-card style="width: 450px; max-width: 90vw">
      <q-card-section class="row items-center pb-0">
        <div class="text-subtitle1 text-weight-medium">Delete Account</div>

        <q-space />

        <q-btn
          icon="sym_r_close"
          flat
          dense
          size="sm"
          padding="xs"
          v-close-popup
        />
      </q-card-section>

      <q-card-section class="q-pb-none">
        <p>
          Are you sure you want to delete your account? Once your account is
          deleted, all of its resources and data will be permanently deleted.
          Please enter your password to confirm you would like to permanently
          delete your account.
        </p>

        <div class="mt-4">
          <q-password
            autofocus
            v-model="form.password"
            class="mt-1 block w-3/4"
            placeholder="Password"
            autocomplete="current-password"
            @keyup.enter="deleteUser"
            outlined
            dense
            hide-bottom-space
            :error="'password' in errors"
            :error-message="errors.password?.[0]"
          />
        </div>
      </q-card-section>

      <q-card-section align="right">
        <q-btn @click="closeModal" unelevated class="me-2"> Cancel </q-btn>

        <q-btn
          color="negative"
          unelevated
          :loading="processing"
          @click="deleteUser"
        >
          Delete Account
        </q-btn>
      </q-card-section>
    </q-card>
  </q-dialog>

  <ProfileSection v-bind="$attrs">
    <template #title> Delete Account </template>
    <template #description> Permanently delete your account. </template>
    <template #content>
      <q-card class="shadow bg-white dark:bg-black-800">
        <q-card-section>
          <p class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
            Once your account is deleted, all of its resources and data will be
            permanently deleted. Before deleting your account, please download
            any data or information that you wish to retain.
          </p>
        </q-card-section>

        <q-card-section>
          <q-btn
            color="negative"
            unelevated
            label="Delete Account"
            @click="confirmUserDeletion"
          />
        </q-card-section>
      </q-card>
    </template>
  </ProfileSection>
</template>

<script setup>
import { useQuasar } from "quasar";
import ProfileSection from "./ProfileSection.vue";

import { ref } from "vue";
import { api } from "@/boot/axios";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const confirmingUserDeletion = ref(false);

const router = useRouter();

const $q = useQuasar();

const errors = ref({});

const processing = ref(false);

const authStore = useAuthStore();

const form = ref({
  password: "",
});

function confirmUserDeletion() {
  confirmingUserDeletion.value = true;
}

async function deleteUser() {
  errors.value = {};
  processing.value = true;

  try {
    await api.delete("/user", {
      data: form.value,
    });

    $q.notify({
      type: "positive",
      message: "Success!",
      caption: "Your account has been deleted.",
    });

    await authStore.logout();

    router.push({ name: "login" });
  } catch (err) {
    if (err.response.status === 422) {
      errors.value = err.response.data.errors;
    } else {
      $q.notify({
        type: "negative",
        message: "Error!",
        caption: "An error occurred. Please try again.",
      });
    }
  } finally {
    processing.value = false;
  }
}

function closeModal() {
  confirmingUserDeletion.value = false;

  form.value.reset();
}
</script>
