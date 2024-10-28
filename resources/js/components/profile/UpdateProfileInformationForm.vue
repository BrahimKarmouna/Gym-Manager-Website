<template>
  <ProfileSection @submit="save">
    <template #title>
      Profile Information
    </template>

    <template #description>
      Update your account's profile information and email address.
    </template>

    <template #content>
      <form @submit.prevent="save">
        <q-card class="shadow bg-white dark:bg-gray-800">
          <q-card-section>
            <div class="grid grid-cols-6 gap-3">
              <!-- Profile Photo -->
              <div v-if="true"
                   class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input id="photo"
                       ref="photoInput"
                       type="file"
                       class="hidden"
                       @change="updatePhotoPreview">

                <label class="font-medium text-gray-800 dark:text-white text-sm block mb-1">
                  Photo
                </label>

                <!-- Current Profile Photo -->
                <div v-show="!photoPreview"
                     class="mt-2">
                  <q-img :src="user.profile_photo_url"
                         :alt="user.name"
                         class="rounded-full h-20 w-20 object-cover" />
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview"
                     class="mt-2">
                  <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + photoPreview + '\');'" />
                </div>

                <q-btn class="mt-2 me-2"
                       label="Select A New Photo"
                       outline
                       @click="selectNewPhoto" />

                <q-btn v-if="user.profile_photo_url"
                       class="mt-2"
                       outline
                       label="Remove Photo"
                       :loading="deletingPhoto"
                       @click="deletePhoto" />

                <!-- Show Errors -->
                <div v-if="'photo' in errors"
                     class="mt-2 text-xs text-red-600 dark:text-red-400">
                  {{ errors.photo?.[0] }}
                </div>
              </div>

              <!-- Name -->
              <div class="col-span-6 sm:col-span-4">
                <q-input v-model="payload.name"
                         outlined
                         dense
                         hide-bottom-space
                         :error="'name' in errors"
                         :error-message="errors.name?.[0]"
                         label="Name" />
              </div>

              <!-- Email -->
              <div class="col-span-6 sm:col-span-4">
                <q-input v-model="payload.email"
                         outlined
                         dense
                         hide-bottom-space
                         :error="'email' in errors"
                         :error-message="errors.email?.[0]"
                         label="Email" />

                <div v-if="user.email_verified_at === null">
                  <p class="text-sm mt-2 dark:text-white">
                    Your email address is unverified.

                    <q-btn :loading="sendingEmailVerification"
                           flat
                           @click.prevent="sendEmailVerification">
                      <span class="underline">
                        Click here to re-send the verification email.
                      </span>
                    </q-btn>
                  </p>
                </div>
              </div>
            </div>
          </q-card-section>

          <q-card-actions align="right"
                          class="bg-gray-50 dark:bg-gray-700">
            <q-btn class="q-ml-md"
                   color="primary"
                   label="Save"
                   padding="sm md"
                   :loading="submitting"
                   unelevated
                   type="submit" />
          </q-card-actions>
        </q-card>
      </form>
    </template>
  </ProfileSection>
</template>

<script setup>
import ProfileSection from './ProfileSection.vue';

import { useQuasar } from "quasar";
import { api } from "@/boot/axios";
import { ref } from "vue";

const user = defineModel('user', { type: Object, required: true });

const $q = useQuasar();

const errors = ref({});

const photoPreview = ref(null);
const photoInput = ref(null);

const submitting = ref(false);

const payload = ref({
  _method: "PUT",
  name: user.value.name,
  email: user.value.email,
  photo: null,
});

// Update Profile Information
async function save() {
  errors.value = {};
  submitting.value = true;

  if (photoInput.value) {
    payload.value.photo = photoInput.value.files[0];
  }

  try {
    const resp = await api.postForm(
      "user/profile-information",
      payload.value
    );

    if (user.value.email !== payload.value.email) {
      user.value.email_verified_at = null;
    }

    clearPhotoFileInput();

    $q.notify({
      type: "positive",
      message: "Success",
      caption: "Profile information updated successfully.",
    });

    return resp;
  } catch (err) {
    console.log(err);


    if (err.response.status === 422) {
      return (errors.value = err.response.data.errors);
    } else {
      $q.notify({
        type: "negative",
        message: "Error",
        caption:
          err.response.data.message ||
          "An error occurred while updating the profile information.",
      });
    }
  } finally {
    submitting.value = false;
  }
}

// Send Email Verification
const sendingEmailVerification = ref(false);

async function sendEmailVerification() {
  sendingEmailVerification.value = true;

  try {
    await api.post("email/verification-notification");

    $q.notify({
      type: "positive",
      message: "Success",
      caption: "A new verification link has been sent to your email address.",
    });
  } catch (err) {
    $q.notify({
      type: "negative",
      message: "Error",
      caption:
        err.response.data.message ||
        "An error occurred while sending the verification email.",
    });
  } finally {
    sendingEmailVerification.value = false;
  }
}

function selectNewPhoto() {
  photoInput.value.click();
}

function updatePhotoPreview() {
  const photo = photoInput.value.files[0];

  if (!photo) return;

  const reader = new FileReader();

  reader.onload = (e) => {
    photoPreview.value = e.target.result;
  };

  reader.readAsDataURL(photo);
}

// Delete Photo
const deletingPhoto = ref(false);

async function deletePhoto() {
  deletingPhoto.value = true;

  try {
    const resp = await api.delete("user/profile-photo");

    photoPreview.value = null;

    user.value = resp.data.user;

    clearPhotoFileInput();
  } catch (err) {
    console.error(err);
  } finally {
    deletingPhoto.value = false;
  }
}

function clearPhotoFileInput() {
  if (photoInput.value?.value) {
    photoInput.value.value = null;
  }
}
</script>
