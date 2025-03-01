<template>
  <ProfileSection>
    <template #title> Two Factor Authentication </template>

    <template #description>
      Add additional security to your account using two factor authentication.
    </template>

    <template #content>
      <q-card class="shadow bg-white dark:bg-black-800">
        <q-card-section class="q-pb-none">
          <h3
            v-if="twoFactorEnabled && !confirming"
            class="text-lg font-medium text-gray-900 dark:text-gray-100"
          >
            You have enabled two factor authentication.
          </h3>

          <h3
            v-else-if="twoFactorEnabled && confirming"
            class="text-lg font-medium text-gray-900 dark:text-gray-100"
          >
            Finish enabling two factor authentication.
          </h3>

          <h3
            v-else
            class="text-lg font-medium text-gray-900 dark:text-gray-100"
          >
            You have not enabled two factor authentication.
          </h3>

          <div class="mt-3 max-w-xl text-sm text-gray-600 dark:text-gray-400">
            <p>
              When two factor authentication is enabled, you will be prompted
              for a secure, random token during authentication. You may retrieve
              this token from your phone's Google Authenticator application.
            </p>
          </div>

          <div v-if="twoFactorEnabled">
            <div v-if="qrCode">
              <div
                class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400"
              >
                <p v-if="confirming" class="font-semibold">
                  To finish enabling two factor authentication, scan the
                  following QR code using your phone's authenticator application
                  or enter the setup key and provide the generated OTP code.
                </p>

                <p v-else>
                  Two factor authentication is now enabled. Scan the following
                  QR code using your phone's authenticator application or enter
                  the setup key.
                </p>
              </div>

              <div class="mt-4 p-2 inline-block bg-white" v-html="qrCode"></div>

              <div
                v-if="setupKey"
                class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400"
              >
                <p class="font-semibold">
                  Setup Key: <span v-html="setupKey"></span>
                </p>
              </div>

              <div v-if="confirming" class="mt-4">
                <q-input
                  v-model="confirmationForm.code"
                  outlined
                  dense
                  hide-bottom-space
                  label="Code"
                  class="mt-1 block w-3/4"
                  inputmode="numeric"
                  autofocus
                  autocomplete="one-time-code"
                  :error="'code' in errors"
                  :error-message="errors.code?.[0]"
                  @keyup.enter="confirmTwoFactorAuthentication"
                />
              </div>
            </div>
            <div v-if="recoveryCodes.length > 0 && !confirming">
              <div
                class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400"
              >
                <p class="font-semibold">
                  Store these recovery codes in a secure password manager. They
                  can be used to recover access to your account if your two
                  factor authentication device is lost.
                </p>
              </div>

              <div
                class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-black dark:text-gray-100 rounded-lg"
              >
                <div v-for="code in recoveryCodes" :key="code">
                  {{ code }}
                </div>
              </div>
            </div>
          </div>
        </q-card-section>

        <q-card-section>
          <ConfirmsPassword
            @confirmed="enableTwoFactorAuthentication"
            v-if="!twoFactorEnabled"
          >
            <template #default="{ startConfirmingPassword }">
              <q-btn
                type="button"
                class="me-3"
                unelevated
                color="green-500 dark:bg-blue-900"
                @click="startConfirmingPassword"
                :disable="enabling"
              >
                Enable
              </q-btn>
            </template>
          </ConfirmsPassword>

          <template v-else>
            <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
              <template #default="{ startConfirmingPassword }">
                <q-btn
                  v-if="confirming"
                  @click="startConfirmingPassword"
                  class="me-3"
                  unelevated
                  color="primary"
                  :disable="enabling"
                >
                  Confirm
                </q-btn>
              </template>
            </ConfirmsPassword>

            <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
              <template #default="{ startConfirmingPassword }">
                <q-btn
                  v-if="recoveryCodes.length > 0 && !confirming"
                  @click="startConfirmingPassword"
                  class="me-3"
                  unelevated
                  outline
                  :disable="enabling"
                >
                  Regenerate Recovery Codes
                </q-btn>
              </template>
            </ConfirmsPassword>

            <ConfirmsPassword @confirmed="showRecoveryCodes">
              <template #default="{ startConfirmingPassword }">
                <q-btn
                  v-if="recoveryCodes.length === 0 && !confirming"
                  @click="startConfirmingPassword"
                  unelevated
                  outline
                  class="me-3"
                >
                  Show Recovery Codes
                </q-btn>
              </template>
            </ConfirmsPassword>

            <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
              <template #default="{ startConfirmingPassword }">
                <q-btn
                  v-if="confirming"
                  @click="startConfirmingPassword"
                  class="me-3"
                  outline
                  :disabled="disabling"
                >
                  Cancel
                </q-btn>
              </template>
            </ConfirmsPassword>

            <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
              <template #default="{ startConfirmingPassword }">
                <q-btn
                  v-if="!confirming"
                  @click="startConfirmingPassword"
                  unelevated
                  color="negative"
                  :disable="disabling"
                >
                  Disable
                </q-btn>
              </template>
            </ConfirmsPassword>
          </template>
        </q-card-section>
      </q-card>
    </template>
  </ProfileSection>
</template>

<script setup>
import { api } from "@/boot/axios";
import { useAuthStore } from "@/stores/auth";
import { ref, computed, watch } from "vue";
import ConfirmsPassword from "./ConfirmsPassword.vue";
import ProfileSection from "./ProfileSection.vue";

const props = defineProps({
  requiresConfirmation: Boolean,
});

const auth = useAuthStore();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = ref({
  code: "",
});

const errors = ref({});

const twoFactorEnabled = computed(
  () => !enabling.value && auth.user?.two_factor_enabled
);

function enableTwoFactorAuthentication() {
  enabling.value = true;

  api
    .post("/user/two-factor-authentication", {})
    .then(() => {
      showQrCode();
      showSetupKey();
      showRecoveryCodes();
    })
    .finally(() => {
      enabling.value = false;
      auth.user.two_factor_enabled = true;
      confirming.value = props.requiresConfirmation;
    });
}

async function showQrCode() {
  return api.get("/user/two-factor-qr-code").then((response) => {
    qrCode.value = response.data.svg;
  });
}

async function showSetupKey() {
  return api.get("/user/two-factor-secret-key").then((response) => {
    setupKey.value = response.data.secretKey;
  });
}

async function showRecoveryCodes() {
  return api.get("/user/two-factor-recovery-codes").then((response) => {
    recoveryCodes.value = response.data;
  });
}

function confirmTwoFactorAuthentication() {
  errors.value = {};

  api
    .post("user/confirmed-two-factor-authentication", confirmationForm.value)
    .then(() => {
      confirming.value = false;
      qrCode.value = null;
      setupKey.value = null;
    })
    .catch((error) => {
      errors.value = error.response.data.errors;
    });
}

function regenerateRecoveryCodes() {
  api.post("/user/two-factor-recovery-codes").then(() => showRecoveryCodes());
}

function disableTwoFactorAuthentication() {
  disabling.value = true;

  api.delete("/user/two-factor-authentication").then(() => {
    disabling.value = false;
    confirming.value = false;
    auth.user.two_factor_enabled = false;
  });
}

watch(twoFactorEnabled, () => {
  if (!twoFactorEnabled.value) {
    errors.value = {};
    confirmationForm.value.code = "";
  }
});
</script>
