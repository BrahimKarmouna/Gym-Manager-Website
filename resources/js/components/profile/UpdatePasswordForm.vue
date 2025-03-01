<template>
  <ProfileSection @submit="save">
    <template #title> Update Password </template>

    <template #description>
      Ensure your account is using a long, random password to stay secure.
    </template>

    <template #content>
      <form @submit.prevent="save">
        <q-card class="shadow bg-white dark:bg-black-800">
          <q-card-section>
            <div class="grid grid-cols-6 gap-3">
              <div class="col-span-6 sm:col-span-4">
                <q-password
                  v-model="payload.current_password"
                  outlined
                  dense
                  hide-bottom-space
                  :error="'current_password' in errors"
                  :error-message="errors.current_password?.[0]"
                  label="Current Password"
                />
              </div>

              <div class="col-span-6 sm:col-span-4">
                <q-password
                  v-model="payload.password"
                  outlined
                  dense
                  hide-bottom-space
                  :error="'password' in errors"
                  :error-message="errors.password?.[0]"
                  label="New Password"
                />
              </div>

              <div class="col-span-6 sm:col-span-4">
                <q-password
                  v-model="payload.password_confirmation"
                  outlined
                  dense
                  hide-bottom-space
                  :error="'password_confirmation' in errors"
                  :error-message="errors.password_confirmation?.[0]"
                  label="Confirm Password"
                />
              </div>
            </div>
          </q-card-section>

          <q-card-actions align="right" class="bg-gray-50 dark:bg-black-800">
            <q-btn
              class="q-ml-md"
              color="green-500 dark:bg-blue-900"
              label="Save"
              padding="sm md"
              :loading="submitting"
              unelevated
              type="submit"
            />
          </q-card-actions>
        </q-card>
      </form>
    </template>
  </ProfileSection>
</template>

<script setup>
import ProfileSection from "./ProfileSection.vue";
import { useQuasar } from "quasar";
import { api } from "@/boot/axios";
import { ref } from "vue";

const $q = useQuasar();

const errors = ref({});

const submitting = ref(false);

const payload = ref({
  current_password: "",
  password: "",
  password_confirmation: "",
});

// Update Password
async function save() {
  errors.value = {};
  submitting.value = true;

  try {
    const resp = await api.put("user/password", payload.value);

    $q.notify({
      type: "positive",
      message: "Success",
      caption: "Password updated successfully.",
    });

    // Reset the form
    payload.value = {
      current_password: "",
      password: "",
      password_confirmation: "",
    };

    return resp;
  } catch (err) {
    if (err.response.status === 422) {
      return (errors.value = err.response.data.errors);
    } else {
      $q.notify({
        type: "negative",
        message: "Error",
        caption:
          err.response.data.message ||
          "An error occurred while updating the password.",
      });
    }
  } finally {
    submitting.value = false;
  }
}
</script>
