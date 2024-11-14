<template>
  <q-layout view="hHh LpR lFf">

    <q-header reveal
              bordered
              class="bg-white text-dark dark:bg-gray-900 dark:text-white h-16 ">
      <q-toolbar>
        <q-btn dense
               flat
               round
               icon="sym_r_reorder"
               @click="toggleLeftDrawer"
               class=" text-blue-900 dark:text-blue-400 mr-1 ms-5 py-5" />
        <img src="https://i.ibb.co/D7xPmJR/finance-coin-money-with-flying-wings-logo-3.png"
             alt="Logo"
             class="text-dark w-20 h-10 mx-auto mb-1 ml-1" />
        <q-toolbar-title class="flex items-center">
          <span class="text-blue-900 dark:text-blue-400  text-2xl mr-1  "
                style="font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;">
            Money Manager
          </span>


        </q-toolbar-title>


        <!-- Dark mode toggler -->
        <q-toggle :model-value="isDarkActive"
                  @update:model-value="toggleDarkMode"
                  checked-icon="sym_r_dark_mode"
                  unchecked-icon="sym_r_light_mode" />
        <AiFillDashboard />
        <q-btn flat
               round
               class="ms-2"
               no-wrap>
          <q-avatar size="26px">
            <img :src="authStore.user.profile_photo_url">
          </q-avatar>

          <q-menu auto-close>
            <q-list dense>
              <q-item>
                <q-item-section>
                  <div class="text-nowrap ellipsis">
                    Signed in as <strong class="ellipsis">{{ authStore.user.name }}</strong>
                  </div>
                </q-item-section>
              </q-item>
              <q-separator />

              <q-item clickable
                      class="dark:text-white"
                      :to="{ name: 'profile.index' }">
                <q-item-section>Your profile</q-item-section>
              </q-item>
              <q-item clickable
                      @click="confirmLogout">
                <q-item-section>Sign out</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <q-drawer show-if-above
              v-model="layoutStore.sidebar.opened"
              side="left"
              :width="250"
              :mini="layoutStore.sidebar.mini"
              bordered>
      <q-list class="q-pa-sm q-gutter-xs py-6 ">

        <!-- <q-item-label header>
          <span class="text-bold"
                style="font-family:  'Lucida Sans'">Navigation</span>

        </q-item-label> -->

        <EssentialLink v-for="link in essentialLinks"
                       :key="link.title"
                       v-bind="link"
                       class="mb-5test@ text-blue-900 dark:text-blue-400 text-bold "
                       style="font-family: sans-serif" />

      </q-list>
    </q-drawer>

    <q-page-container>
      <Suspense>
        <template #fallback>
          <q-page :style-fn="(offset) => ({ height: `calc(100vh - ${offset}px)` })">
            <div class="flex items-center justify-center fit ">
              <q-inner-loading :showing="true"
                               label="Loading..." />
            </div>
          </q-page>
        </template>

        <RouterView />
      </Suspense>
    </q-page-container>

  </q-layout>
</template>
<style></style>
<script>
import EssentialLink from '@/components/EssentialLink.vue'
import { computed, defineComponent } from 'vue'
import { useQuasar } from 'quasar';
import { useLayoutStore } from '@/stores/layout';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';


const linksList = [
  {
    title: 'Dashboad',
    icon: 'sym_r_dashboard',
    to: { name: 'dashboard.index' },
    exact: true
  },
  {
    title: 'Account',
    icon: 'sym_r_person',
    to: { name: 'account.index-account' }
  },
  {
    title: 'Transactions',
    icon: 'sym_r_move_down',
    to: { name: 'transaction.index' }
  },
  {
    title: 'Categories',
    icon: 'sym_r_category',
    children: [

      {
        title: 'Income',
        to: { name: 'incomes.index' }
      },
      {
        title: 'Expense',
        to: { name: 'expenses.index' }
      }
    ]
  },
]

export default defineComponent({
  name: 'MainLayout',

  components: {
    EssentialLink
  },

  setup() {
    const layoutStore = useLayoutStore();

    const authStore = useAuthStore();

    const $q = useQuasar();

    const router = useRouter();


    function confirmLogout() {
      $q.dialog({
        title: 'Confirm',
        message: 'Are you sure you want to sign out?',
        persistent: true,
        ok: {
          label: 'Yes',
          color: 'green-500 dark:bg-blue-900',
          unelevated: true,
        },

        cancel: {
          label: 'No',
          flat: true,
          color: 'red-500 ',
          handler: () => {
            // Do nothing
          }
        },
      }).onOk(() => {
        authStore.logout();
      });
    }

    function toggleDarkMode(value) {
      $q.dark.set(value);
    }

    const isDarkActive = computed(() => $q.dark.isActive);

    return {
      essentialLinks: linksList,
      layoutStore,
      authStore,
      isDarkActive,
      toggleDarkMode,
      confirmLogout,
      toggleLeftDrawer() {
        layoutStore.sidebar.opened = !layoutStore.sidebar.opened;
      },

    }
  }
});
</script>
