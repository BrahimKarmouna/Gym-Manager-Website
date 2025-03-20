<template>
  <q-page padding>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      Profile
    </h2>

    <div>
      <div class="max-w-7xl mx-auto py-10">
        <!-- User Orders Link -->
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mb-6">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Order History</h3>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                View and track your past orders.
              </p>
            </div>
            <router-link 
              :to="{ name: 'user.orders.index' }" 
              class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-dark focus:outline-none focus:bg-primary-dark active:bg-primary-dark transition"
            >
              View Orders
            </router-link>
          </div>
        </div>

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
