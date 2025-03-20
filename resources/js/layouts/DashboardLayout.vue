<template>
  <q-layout view="hHh LpR lFf">
    <!-- Modern Header -->
    <q-header reveal bordered class="bg-white/90 backdrop-blur-lg shadow-sm border-b border-gray-100">
      <q-toolbar class="h-[70px] px-6">
        <!-- Menu Button - Only Show When Authenticated -->
        <q-btn
          v-if="authStore.authenticated"
          dense
          flat
          round
          icon="menu"
          size="md"
          @click="toggleLeftDrawer"
          class="text-gray-700 hover:text-primary transition-transform hover:scale-110"
        />

        <!-- Brand Logo -->
        <router-link :to="authStore.authenticated ? { name: 'dashboard.index' } : { name: 'products' }" class="flex items-center hover:opacity-80 transition-opacity">
          <img src="/images/logo/logoblack.png" class="w-10 h-auto transition-transform hover:scale-105" alt="Logo">
        </router-link>

        <q-space />

        <!-- Guest Navigation Links - Only Show When NOT Authenticated -->
        <div v-if="!authStore.authenticated" class="flex items-center gap-4">
          <router-link :to="{ name: 'products' }" class="text-gray-700 hover:text-primary transition-all font-medium">
            Products
          </router-link>
          <router-link :to="{ name: 'login' }" class="text-gray-700 hover:text-primary transition-all font-medium">
            Login
          </router-link>
          <router-link :to="{ name: 'register' }" class="text-gray-700 hover:text-primary transition-all font-medium">
            Register
          </router-link>
        </div>

        <!-- Dark Mode Toggle -->
        <q-toggle
          :model-value="isDarkActive"
          @update:model-value="toggleDarkMode"
          checked-icon="dark_mode"
          unchecked-icon="light_mode"
          class="mr-4"
          color="primary"
        />

        <!-- Profile Button - Only Show When Authenticated -->
        <q-btn v-if="authStore.authenticated" flat round class="relative overflow-hidden transition-all">
          <q-avatar size="40px" class="ring-2 ring-white shadow-lg">
            <img :src="authStore.user.profile_photo_url" alt="Profile">
          </q-avatar>
          <q-menu auto-close class="rounded-xl shadow-xl w-56">
            <q-list dense>
              <!-- Profile Header -->
              <q-item class="bg-gradient-to-r from-blue-500 to-purple-500 p-4">
                <q-item-section>
                  <div class="text-white font-bold text-base">{{ authStore.user.name }}</div>
                  <div class="text-white/80 text-xs mt-1">
                    Signed in as {{ authStore.user.isAdmin ? 'Admin' : 'User' }}
                  </div>
                </q-item-section>
              </q-item>
              
              <q-separator />
              
              <!-- Menu Items -->
              <q-item clickable :to="{ name: 'profile.index' }" class="p-3 hover:bg-gray-50">
                <q-item-section avatar>
                  <q-icon name="person" class="text-gray-600" />
                </q-item-section>
                <q-item-section>Your Profile</q-item-section>
              </q-item>
              
              <q-item clickable :to="{ name: 'user.orders.index' }" class="p-3 hover:bg-gray-50">
                <q-item-section avatar>
                  <q-icon name="shopping_bag" class="text-gray-600" />
                </q-item-section>
                <q-item-section>My Orders</q-item-section>
              </q-item>
              
              <q-item clickable @click="confirmLogout" class="p-3 hover:bg-gray-50">
                <q-item-section avatar>
                  <q-icon name="logout" class="text-gray-600" />
                </q-item-section>
                <q-item-section>Sign Out</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <!-- Sidebar - Only Show When Authenticated -->
    <q-drawer
      v-if="authStore.authenticated"
      show-if-above
      v-model="layoutStore.sidebar.opened"
      side="left"
      :width="280"
      bordered
      class="bg-gradient-to-br from-white to-gray-50 border-r border-gray-100"
    >
      <!-- Sidebar Header -->
      <div class="h-[70px] px-6 flex items-center gap-3 border-b border-gray-100">
      <img src="/images/logo/logoblack.png" class="w-9 h-auto transform transition-all duration-300 hover:scale-110" alt="Logo">
      <div class="text-lg font-bold bg-gradient-to-r from-blue-600 to-indigo-500 bg-clip-text text-transparent">
        Fitness Redefined
      </div>
      </div>
      
      <!-- Navigation Links -->
      <div class="p-4">
      <q-list padding class="rounded-2xl bg-white shadow-sm">
        <EssentialLink
        v-for="link in essentialLinks"
        :key="link.title"
        v-bind="link"
        class="my-1 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:text-blue-600 hover:translate-x-1"
        />
      </q-list>
      
      <!-- Admin Section - Only Show for Admin Users -->
      <div v-if="authStore.user && authStore.user.isAdmin" class="mt-8 p-4 rounded-2xl bg-gradient-to-br from-blue-500/10 to-indigo-500/10 backdrop-blur-sm">
        <div class="text-xs font-bold uppercase text-gray-500 mb-3 px-2">Administration</div>
        <q-item clickable v-ripple class="rounded-xl mb-1 hover:bg-white/70 transition-all duration-200">
        <q-item-section avatar>
          <q-icon name="settings" color="blue-grey-7" />
        </q-item-section>
        <q-item-section>Settings</q-item-section>
        </q-item>
        <q-item clickable v-ripple class="rounded-xl hover:bg-white/70 transition-all duration-200">
        <q-item-section avatar>
          <q-icon name="help" color="blue-grey-7" />
        </q-item-section>
        <q-item-section>Help Center</q-item-section>
        </q-item>
      </div>
      </div>
    </q-drawer>

    <!-- Main Content -->
    <q-page-container class="bg-gray-50">
      <Suspense>
        <template #fallback>
          <q-page class="flex flex-col items-center justify-center h-screen bg-gradient-to-br from-gray-50 to-gray-100">
            <q-spinner-dots color="primary" size="60px" />
            <div class="mt-4 text-gray-600">Loading...</div>
          </q-page>
        </template>
        <RouterView />
      </Suspense>
    </q-page-container>
  </q-layout>
</template>

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
