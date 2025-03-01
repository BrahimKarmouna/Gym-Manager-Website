<template>
  <!-- Log Out Other Devices Confirmation Modal -->
  <q-dialog v-model="confirmingLogout" @hide="closeModal">
    <q-card style="width: 450px; max-width: 90vw">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-subtitle1 text-weight-medium">
          Log Out Other Browser Sessions
        </div>

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
          Please enter your password to confirm you would like to log out of
          your other browser sessions across all of your devices.
        </p>

        <div class="mt-4">
          <q-password
            ref="passwordInput"
            autofocus
            v-model="form.password"
            dense
            class="mt-1 block w-3/4"
            placeholder="Password"
            autocomplete="current-password"
            outlined
            hide-bottom-space
            :error="'password' in errors"
            :error-message="errors.password?.[0]"
            @keyup.enter="logoutOtherBrowserSessions"
          />
        </div>
      </q-card-section>

      <q-card-section align="right">
        <q-btn v-close-popup class="me-2" flat padding="sm md"> Cancel </q-btn>

        <q-btn
          unelevated
          color="primary"
          padding="sm md"
          :loading="processing"
          @click="logoutOtherBrowserSessions"
        >
          Log Out Other Browser Sessions
        </q-btn>
      </q-card-section>
    </q-card>
  </q-dialog>

  <ProfileSection v-bind="$attrs">
    <template #title> Browser Sessions </template>

    <template #description>
      Manage and log out your active sessions on other browsers and devices.
    </template>

    <template #content>
      <q-card class="shadow bg-white dark:bg-black-800">
        <q-card-section class="q-pb-none">
          <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
            If necessary, you may log out of all of your other browser sessions
            across all of your devices. Some of your recent sessions are listed
            below; however, this list may not be exhaustive. If you feel your
            account has been compromised, you should also update your password.
          </div>

          <!-- Other Browser Sessions -->
          <div v-if="sessions.length > 0" class="mt-5 space-y-6">
            <div
              v-for="(session, i) in sessions"
              :key="i"
              class="flex items-center"
            >
              <q-icon
                v-if="session.agent.is_desktop"
                name="sym_r_desktop_windows"
                size="sm"
                class="text-gray-500"
              />

              <q-icon
                v-else
                name="sym_r_phone_android"
                size="sm"
                class="text-gray-500"
              />

              <div class="ms-3">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                  {{
                    session.agent.platform ? session.agent.platform : "Unknown"
                  }}
                  -
                  {{
                    session.agent.browser ? session.agent.browser : "Unknown"
                  }}
                </div>

                <div>
                  <div class="text-xs text-gray-500">
                    {{ session.ip_address }},

                    <span
                      v-if="session.is_current_device"
                      class="text-green-500 font-semibold"
                      >This device</span
                    >
                    <span v-else>Last active {{ session.last_active }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </q-card-section>

        <q-card-section>
          <q-btn
            @click="confirmLogout"
            unelevated
            padding="sm md"
            color="green-500 dark:bg-blue-900"
          >
            Log Out Other Browser Sessions
          </q-btn>
        </q-card-section>
      </q-card>
    </template>
  </ProfileSection>
</template>

<script setup>
import ProfileSection from "./ProfileSection.vue";
import { api } from "@/boot/axios";
import { ref } from "vue";
import { useQuasar } from "quasar";

defineProps({
  sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = ref({
  password: "",
});

const errors = ref({});

const confirmLogout = () => {
  confirmingLogout.value = true;
};

const $q = useQuasar();

const processing = ref(false);

async function logoutOtherBrowserSessions() {
  errors.value = {};
  processing.value = true;

  try {
    await api.delete("/user/other-browser-sessions", {
      data: form.value,
    });

    form.value.password = "";

    $q.notify({
      type: "positive",
      message: "Success!",
      caption: "Your other browser sessions have been logged out.",
    });

    closeModal();
  } catch (err) {
    errors.value = err.response.data.errors;

    if (errors.value.password) {
      passwordInput.value.focus();
    }
  } finally {
    processing.value = false;
  }
}

const closeModal = () => {
  confirmingLogout.value = false;

  form.value.password = "";

  errors.value = {};
};
</script>
