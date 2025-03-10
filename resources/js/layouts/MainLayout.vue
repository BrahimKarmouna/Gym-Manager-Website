<template>
  <q-layout view="hHh LpR lFf">
    <q-header
      reveal
      bordered
      class="glassmorphism-header"
    >
      <!-- <q-toolbar>
        <q-btn
          dense
          flat
          round
          icon="menu"
          @click="toggleLeftDrawer"
          class="text-primary"
        />

        <router-link
          class="brand-logo"
          :to="{ name: 'dashboard.index' }"
        >
          <span>Gym Manager</span>
        </router-link>

        <q-toolbar-title class="flex items-center">
          <router-link
            class="text-lg text-bold"
            :to="{ name: 'register' }"
          >
            Fitness Redefined
          </router-link>
        </q-toolbar-title>

        <q-toggle
          :model-value="isDarkActive"
          @update:model-value="toggleDarkMode"
          checked-icon="dark_mode"
          unchecked-icon="light_mode"
        />

        <q-btn
          flat
          round
          class="profile-btn"
        >
          <q-avatar rounded>
            <img src="https://cdn.quasar.dev/img/avatar.png">
          </q-avatar>
          <q-menu auto-close>
            <q-list dense>
              <q-item>
                <q-item-section>
                  <div class="text-nowrap">Signed in as <strong>{{ authStore.user.name }}</strong></div>
                </q-item-section>
              </q-item>
              <q-separator />
              <q-item
                clickable
                :to="{ name: 'profile.index' }"
              >
                <q-item-section>Your Profile</q-item-section>
              </q-item>
              <q-item
                clickable
                @click="confirmLogout"
              >
                <q-item-section>Sign Out</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar> -->

      <q-toolbar class="bg-white text-black shadow-2 h-20 ">
        <!-- Menu Button -->
        <q-btn
          dense
          flat
          round
          icon="list"
          size="24px"
          @click="toggleLeftDrawer"
          class="text-black mr-10"
        />

        <!-- Brand Logo -->
        <router-link
          class="text-h6 text-bold text-black"
          :to="{ name: 'dashboard.index' }"
        >
          <img
            src="/images/logo/logoblack.png"
            class="w-16"
            alt="Logo"
          >

        </router-link>

        <!-- Title with Fitness Redefined -->
        <q-toolbar-title class="flex items-center justify-center text-lg text-bold">
          <!-- <router-link
            class="text-black"
            :to="{ name: 'register' }"
          >
            Fitness Redefined
          </router-link> -->
        </q-toolbar-title>

        <!-- Dark Mode Toggle -->
        <q-toggle
          :model-value="isDarkActive"
          @update:model-value="toggleDarkMode"
          checked-icon="dark_mode"
          unchecked-icon="light_mode"
          class="text-black"
        />

        <!-- Profile Button and Menu -->
        <q-btn
          flat
          round
          class="text-white profile-btn"
        >
          <q-avatar
            size="32px"
            rounded
          >
            <img
              :src="authStore.user.profile_photo_url"
              alt="Profile Image"
            >
          </q-avatar>
          <q-menu auto-close>
            <q-list
              dense
              class="bg-grey-1"
            >
              <q-item>
                <q-item-section>
                  <div class="text-nowrap">Signed in as <strong>{{ authStore.user.name }}</strong></div>
                </q-item-section>
              </q-item>
              <q-separator />
              <q-item
                clickable
                :to="{ name: 'profile.index' }"
              >
                <q-item-section>Your Profile</q-item-section>
              </q-item>
              <q-item
                clickable
                @click="confirmLogout"
              >
                <q-item-section>Sign Out</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>

    </q-header>

    <q-drawer
      show-if-above
      v-model="layoutStore.sidebar.opened"
      side="left"
      :width="250"
      bordered
      class="sidebar-menu mt-1"
    >
      <q-list>
        <EssentialLink
          v-for="link in essentialLinks"
          :key="link.title"
          v-bind="link"
          class="nav-item"
        />
      </q-list>
    </q-drawer>

    <q-page-container>
      <Suspense>
        <template #fallback>
          <q-page class="loading-screen">
            <q-inner-loading
              :showing="true"
              label="Loading..."
            />
          </q-page>
        </template>
        <RouterView />
      </Suspense>
    </q-page-container>
  </q-layout>
</template>

<style scoped>
.glassmorphism-header {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.brand-logo {
  font-size: 1.5rem;
  font-weight: bold;
  text-transform: uppercase;
  background: linear-gradient(to right, #4facfe, #00f2fe);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.profile-btn:hover {
  transform: scale(1.05);
  transition: 0.3s;
}

.sidebar-menu {
  background: #f8f9fc;
  padding: 20px 0;
}

.nav-item {
  padding: 12px 20px;
  font-size: 1rem;
  font-weight: 500;
  color: #333;
  border-radius: 8px;
  transition: background 0.3s;
}

.nav-item:hover {
  background: rgba(0, 0, 0, 0.05);
}

.loading-screen {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
</style>

<script>
import EssentialLink from "@/components/EssentialLink.vue";
import { computed, defineComponent } from "vue";
import { useQuasar } from "quasar";
import { useLayoutStore } from "@/stores/layout";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const linksList = [
  { title: "Dashboard", icon: "sym_r_dashboard", to: { name: "dashboard.index" } },
  { title: "Clients", icon: "sym_r_person", to: { name: "account.index-account" } },
  { title: "Payments", icon: "sym_r_payments", to: { name: "transaction.index" } },
  { title: "Insurance", icon: "sym_r_shield", to: { name: "expenses.index" } },
  {
    title: "Plans", icon: "sym_r_category", children: [
      { title: "Payment Plans", to: { name: "incomes.index" } },
      { title: "Insurance Plans", to: { name: "expenses_plan.index" } }
    ]
  },
  {
    title: "Products", icon: "sym_r_box", children: [
      { title: "My products", icon: "sym_r_package", to: { name: "my_products" } },
      { title: "Orders", icon: "sym_r_inventory_2", to: { name: "my_products" } },
      { title: "Sells", icon: "sym_r_sell", to: { name: "sells" } },
    ]
  },


  { title: "Bills", icon: "inventory", to: { name: "bills.index" } },
  { title: "Assistants", icon: "group_add", to: { name: "assistants.index" } },
];

export default defineComponent({
  name: "MainLayout",
  components: { EssentialLink },
  setup() {
    const layoutStore = useLayoutStore();
    const authStore = useAuthStore();
    const $q = useQuasar();
    const router = useRouter();

    function confirmLogout() {
      $q.dialog({
        title: "Confirm", message: "Are you sure you want to sign out?",
        persistent: true,
        ok: { label: "Yes", color: "blue-900" },
        cancel: { label: "No", color: "red-500" }
      }).onOk(() => authStore.logout());
    }

    function toggleDarkMode(value) {
      $q.dark.set(value);
    }

    return {
      essentialLinks: linksList,
      layoutStore,
      authStore,
      isDarkActive: computed(() => $q.dark.isActive),
      toggleDarkMode,
      confirmLogout,
      toggleLeftDrawer() {
        layoutStore.sidebar.opened = !layoutStore.sidebar.opened;
      }
    };
  }
});
</script>
