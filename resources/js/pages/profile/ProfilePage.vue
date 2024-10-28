<template>
  <q-page padding>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      Profile
    </h2>

    <div>
      <div class="max-w-7xl mx-auto py-10">

        <div>
          <UpdateProfileInformationForm v-model:user="auth.user" />

          <q-separator class="my-10" />
        </div>

        <div>
          <UpdatePasswordForm />

          <q-separator class="my-10" />
        </div>

        <div>
          <LogoutOtherBrowserSessionsForm class="mt-10 sm:mt-0"
                                          :sessions="sessions" />

          <q-separator class="my-10" />
        </div>

        <div>
          <TwoFactorAuthenticationForm class="mt-10 sm:mt-0"
                                       :requires-confirmation="confirmsTwoFactorAuthentication" />

          <q-separator class="my-10" />
        </div>

        <div>
          <DeleteUserForm class="mt-10 sm:mt-0" />
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { api } from '@/boot/axios';
import DeleteUserForm from '@/components/profile/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/components/profile/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/components/profile/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/components/profile/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/components/profile/UpdateProfileInformationForm.vue';
import { useAuthStore } from '@/stores/auth';
import { ref } from 'vue';

export default {
  name: 'ProfilePage',

  components: {
    UpdatePasswordForm,
    UpdateProfileInformationForm,
    LogoutOtherBrowserSessionsForm,
    TwoFactorAuthenticationForm,
    DeleteUserForm,
  },

  async beforeRouteEnter(to, from, next) {
    const { data } = await api.get('user/profile');

    next((vm) => {
      vm.sessions = data.sessions;
      vm.confirmsTwoFactorAuthentication = data.confirmsTwoFactorAuthentication;
    });
  },

  setup() {
    const auth = useAuthStore();

    const sessions = ref([]);
    const confirmsTwoFactorAuthentication = ref(false);

    return {
      sessions,
      confirmsTwoFactorAuthentication,
      auth
    }
  },
}
</script>
