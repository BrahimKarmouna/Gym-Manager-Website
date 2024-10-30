<template>
  <q-layout view="hHh LpR lFf">

    <q-header reveal
              bordered
              class="bg-white text-dark dark:bg-gray-900 dark:text-white">
      <q-toolbar>
        <q-btn dense
               flat
               round
               icon="sym_r_menu"
               @click="toggleLeftDrawer" />

        <q-toolbar-title>
          <q-avatar>
            <q-icon name="svguse:/icons.svg#logo"
                    size="1.8em"
                    class="text-dark dark:text-white" />
          </q-avatar>
          Adsglory Installer
        </q-toolbar-title>

        <!-- Dark mode toggler -->
        <q-toggle :model-value="isDarkActive"
                  @update:model-value="toggleDarkMode"
                  checked-icon="sym_r_dark_mode"
                  unchecked-icon="sym_r_light_mode" />

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
      <q-list class="q-pa-sm q-gutter-xs">

        <q-item-label header>
          Navigation
        </q-item-label>


        <EssentialLink v-for="link in essentialLinks"
                       :key="link.title"
                       v-bind="link" />
      </q-list>
    </q-drawer>

    <q-page-container>
      <Suspense>
        <template #fallback>
          <q-page :style-fn="(offset) => ({ height: `calc(100vh - ${offset}px)` })">
            <div class="flex items-center justify-center fit">
              <q-inner-loading :showing="true"
                               label="Loading..." />
            </div>
          </q-page>
        </template>

        <RouterView  />
      </Suspense>
    </q-page-container>

  </q-layout>
</template>

<script>
import EssentialLink from '@/components/EssentialLink.vue'
import { computed, defineComponent } from 'vue'
import { useQuasar } from 'quasar';
import { useLayoutStore } from '@/stores/layout';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const linksList = [
  {
    title: 'Transaction Categories',
    icon: 'sym_r_category',
    to: { name: 'transaction-categories.index' }
  },
  {
    title: 'Transferts',
    icon: 'sym_r_category',
    to: { name: 'transaction.index' }
  },
  {
    title: 'Income',
    icon: 'sym_r_category',
    to: { name: 'transaction.index' }
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
          color: 'primary',
          unelevated: true,
        },

        cancel: {
          label: 'No',
          flat: true,
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
