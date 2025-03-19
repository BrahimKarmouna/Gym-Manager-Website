<template>
  <ShowModal v-model:visible="ShowClient" :clients="clients" />

  <!-- Stats Cards Section with Enhanced Design -->
  <div class="px-4 py-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Card 4 - New Clients -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 relative group"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
        <div class="p-6 relative">
          <div class="flex items-center justify-between mb-4">
            <div
              class="text-gray-800 dark:text-white text-lg font-bold flex items-center gap-2"
            >
              <div
                class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              New Clients
            </div>
            <div
              class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-sm font-semibold px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-full"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="m18 15-6-6-6 6" />
              </svg>
              +3.25%
            </div>
          </div>
          <div
            class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2 flex items-baseline"
          >
            {{ new_clients }}
            <span
              class="ml-2 text-sm text-gray-500 dark:text-gray-400 font-normal"
              >this month</span
            >
          </div>
          <div
            class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-3"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="14"
              height="14"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="mr-1"
            >
              <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
              <line x1="16" x2="16" y1="2" y2="6"></line>
              <line x1="8" x2="8" y1="2" y2="6"></line>
              <line x1="3" x2="21" y1="10" y2="10"></line>
            </svg>
            July 01 - July 31, 2024
          </div>
        </div>
      </div>

      <!-- Card 5 - Active Subscriptions -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 relative group"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500"></div>
        <div class="p-6 relative">
          <div class="flex items-center justify-between mb-4">
            <div
              class="text-gray-800 dark:text-white text-lg font-bold flex items-center gap-2"
            >
              <div
                class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg text-emerald-600 dark:text-emerald-400"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 22v-5"></path>
                  <path d="M9 8V2"></path>
                  <path d="M15 8V2"></path>
                  <path d="M12 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"></path>
                  <path d="M12 8v9"></path>
                  <circle cx="12" cy="20" r="2"></circle>
                </svg>
              </div>
              Active Subscriptions
            </div>
            <div
              class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-sm font-semibold px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-full"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="m18 15-6-6-6 6" />
              </svg>
              +1.50%
            </div>
          </div>
          <div
            class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2 flex items-baseline"
          >
            {{ active_subscriptions }}
            <span
              class="ml-2 text-sm text-gray-500 dark:text-gray-400 font-normal"
              >memberships</span
            >
          </div>
          <div
            class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-3"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="14"
              height="14"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="mr-1"
            >
              <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
              <line x1="16" x2="16" y1="2" y2="6"></line>
              <line x1="8" x2="8" y1="2" y2="6"></line>
              <line x1="3" x2="21" y1="10" y2="10"></line>
            </svg>
            As of July 31, 2024
          </div>
        </div>
      </div>

      <!-- Card 6 - Expired Subscriptions -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 relative group"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-rose-500"></div>
        <div class="p-6 relative">
          <div class="flex items-center justify-between mb-4">
            <div
              class="text-gray-800 dark:text-white text-lg font-bold flex items-center gap-2"
            >
              <div
                class="p-2 bg-rose-100 dark:bg-rose-900/30 rounded-lg text-rose-600 dark:text-rose-400"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M16.5 9.4 7.55 4.24"></path>
                  <path
                    d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"
                  ></path>
                  <polyline points="3.29 7 12 12 20.71 7"></polyline>
                  <line x1="12" x2="12" y1="22" y2="12"></line>
                </svg>
              </div>
              Expired Subscriptions
            </div>
            <div
              class="flex items-center gap-1 text-rose-600 dark:text-rose-400 text-sm font-semibold px-3 py-1.5 bg-rose-50 dark:bg-rose-900/30 rounded-full"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="m6 9 6 6 6-6" />
              </svg>
              -0.75%
            </div>
          </div>
          <div
            class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2 flex items-baseline"
          >
            {{ expired_subscriptions }}
            <span
              class="ml-2 text-sm text-gray-500 dark:text-gray-400 font-normal"
              >expired</span
            >
          </div>
          <div
            class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-3"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="14"
              height="14"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="mr-1"
            >
              <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
              <line x1="16" x2="16" y1="2" y2="6"></line>
              <line x1="8" x2="8" y1="2" y2="6"></line>
              <line x1="3" x2="21" y1="10" y2="10"></line>
            </svg>
            As of July 31, 2024
          </div>
        </div>
      </div>

      <!-- Card 0 - Total Clients -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 relative group"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-violet-500"></div>
        <div class="p-6 relative">
          <div class="flex items-center justify-between mb-4">
            <div
              class="text-gray-800 dark:text-white text-lg font-bold flex items-center gap-2"
            >
              <div
                class="p-2 bg-violet-100 dark:bg-violet-900/30 rounded-lg text-violet-600 dark:text-violet-400"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              Total Clients
            </div>
            <div
              class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-sm font-semibold px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-full"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="m18 15-6-6-6 6" />
              </svg>
              +1.75%
            </div>
          </div>
          <div
            class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2 flex items-baseline"
          >
            {{ total_clients }}
            <span
              class="ml-2 text-sm text-gray-500 dark:text-gray-400 font-normal"
              >registered</span
            >
          </div>
          <div
            class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-3"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="14"
              height="14"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="mr-1"
            >
              <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
              <line x1="16" x2="16" y1="2" y2="6"></line>
              <line x1="8" x2="8" y1="2" y2="6"></line>
              <line x1="3" x2="21" y1="10" y2="10"></line>
            </svg>
            July 01 - July 31, 2024
          </div>
        </div>
      </div>

      <!-- Card 1 - Income This Month -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 relative group"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-amber-500"></div>
        <div class="p-6 relative">
          <div class="flex items-center justify-between mb-4">
            <div
              class="text-gray-800 dark:text-white text-lg font-bold flex items-center gap-2"
            >
              <div
                class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg text-amber-600 dark:text-amber-400"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <circle cx="12" cy="12" r="10"></circle>
                  <path d="M16 8h-6.5a2 2 0 1 0 0 4h3a2 2 0 1 1 0 4H6"></path>
                  <path d="M12 18V6"></path>
                </svg>
              </div>
              Income This Month
            </div>
            <div
              class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-sm font-semibold px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-full"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="m18 15-6-6-6 6" />
              </svg>
              +2.15%
            </div>
          </div>
          <div
            class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2 flex items-baseline"
          >
            {{ revenue.total_revenue_current_month }}
            <span
              class="ml-2 text-xl font-bold text-amber-600 dark:text-amber-400"
              >MAD</span
            >
          </div>
          <div
            class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-3"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="14"
              height="14"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="mr-1"
            >
              <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
              <line x1="16" x2="16" y1="2" y2="6"></line>
              <line x1="8" x2="8" y1="2" y2="6"></line>
              <line x1="3" x2="21" y1="10" y2="10"></line>
            </svg>
            Jan 01 - Mar 30, 2024
          </div>
        </div>
      </div>

      <!-- Card 2 - Income This Year -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 relative group"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-red-500"></div>
        <div class="p-6 relative">
          <div class="flex items-center justify-between mb-4">
            <div
              class="text-gray-800 dark:text-white text-lg font-bold flex items-center gap-2"
            >
              <div
                class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg text-red-600 dark:text-red-400"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path
                    d="M12 8c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4Z"
                  ></path>
                  <path d="M12 2v2"></path>
                  <path d="M12 20v2"></path>
                  <path d="m4.9 4.9 1.4 1.4"></path>
                  <path d="m17.7 17.7 1.4 1.4"></path>
                  <path d="M2 12h2"></path>
                  <path d="M20 12h2"></path>
                  <path d="m6.3 17.7-1.4 1.4"></path>
                  <path d="m19.1 4.9-1.4 1.4"></path>
                </svg>
              </div>
              Income This Year
            </div>
            <div
              class="flex items-center gap-1 text-rose-600 dark:text-rose-400 text-sm font-semibold px-3 py-1.5 bg-rose-50 dark:bg-rose-900/30 rounded-full"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="m6 9 6 6 6-6" />
              </svg>
              -2.15%
            </div>
          </div>
          <div
            class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2 flex items-baseline"
          >
            {{ revenue.total_revenue_current_year }}
            <span class="ml-2 text-xl font-bold text-red-600 dark:text-red-400"
              >MAD</span
            >
          </div>
          <div
            class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-3"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="14"
              height="14"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="mr-1"
            >
              <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
              <line x1="16" x2="16" y1="2" y2="6"></line>
              <line x1="8" x2="8" y1="2" y2="6"></line>
              <line x1="3" x2="21" y1="10" y2="10"></line>
            </svg>
            Jan 01 - Mar 30, 2024
          </div>
        </div>
      </div>

      <!-- Card 3 - Income Last Year -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 relative group"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-cyan-500"></div>
        <div class="p-6 relative">
          <div class="flex items-center justify-between mb-4">
            <div
              class="text-gray-800 dark:text-white text-lg font-bold flex items-center gap-2"
            >
              <div
                class="p-2 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg text-cyan-600 dark:text-cyan-400"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M2 20h.01"></path>
                  <path d="M7 20v-4"></path>
                  <path d="M12 20v-8"></path>
                  <path d="M17 20V8"></path>
                  <path d="M22 4v16"></path>
                </svg>
              </div>
              Income Last Year
            </div>
            <div
              class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-sm font-semibold px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-full"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="m18 15-6-6-6 6" />
              </svg>
              +2.15%
            </div>
          </div>
          <div
            class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2 flex items-baseline"
          >
            {{ revenue.total_revenue_last_year }}
            <span
              class="ml-2 text-xl font-bold text-cyan-600 dark:text-cyan-400"
              >MAD</span
            >
          </div>
          <div
            class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-3"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="14"
              height="14"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="mr-1"
            >
              <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
              <line x1="16" x2="16" y1="2" y2="6"></line>
              <line x1="8" x2="8" y1="2" y2="6"></line>
              <line x1="3" x2="21" y1="10" y2="10"></line>
            </svg>
            Jan 01 - Dec 31, 2023
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Dashboard Main Content -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 px-4 py-6">
    <!-- Client Table -->
    <div class="lg:col-span-2">
      <div
        class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700"
      >
        <div
          class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center"
        >
          <h3 class="font-bold text-lg text-gray-800 dark:text-white">
            Active Clients
          </h3>
          <button
            @click="fetchClients"
            class="flex items-center gap-2 px-3 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors duration-200 text-sm font-medium"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M21 2v6h-6"></path>
              <path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path>
              <path d="M3 22v-6h6"></path>
              <path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path>
            </svg>
            Refresh
          </button>
        </div>
        <div class="overflow-x-auto max-h-[500px]">
          <table class="w-full text-sm">
            <thead
              class="text-xs uppercase bg-gray-50 dark:bg-gray-800 sticky top-0"
            >
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold"
                >
                  ID
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold"
                >
                  Name
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold"
                >
                  Assurance
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold"
                >
                  Payment
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold"
                >
                  Action
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="client in clients"
                :key="client.id"
                class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors duration-150"
              >
                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                  #{{ client.id }}
                </td>
                <th
                  scope="row"
                  class="flex items-center px-6 py-4 whitespace-nowrap"
                >
                  <div class="relative w-10 h-10">
                    <img
                      :src="'storage/' + client.client_picture"
                      alt="Client Image"
                      class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-700"
                    />
                    <span
                      class="absolute bottom-0 right-0 transform translate-x-1/4 -translate-y-1/4 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-900"
                    ></span>
                  </div>
                  <div class="ps-3">
                    <div
                      class="text-base font-semibold text-gray-900 dark:text-white"
                    >
                      <button
                        @click="showClient(client)"
                        class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                      >
                        {{ client.Full_name }}
                      </button>
                    </div>
                    <div
                      class="text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                      {{ client.name }}
                    </div>
                    <div class="font-normal text-gray-500 dark:text-gray-500">
                      {{ client.email }}
                    </div>
                  </div>
                </th>
                <td class="px-6 py-4">
                  <div
                    v-if="new Date(client.assurance_expired_date) >= new Date()"
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400"
                  >
                    <div
                      class="h-1.5 w-1.5 rounded-full bg-green-500 me-1.5"
                    ></div>
                    Assuré
                  </div>
                  <div
                    v-else
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400"
                  >
                    <div
                      class="h-1.5 w-1.5 rounded-full bg-red-500 me-1.5"
                    ></div>
                    Non assuré
                  </div>
                </td>

                <td class="px-6 py-4">
                  <div
                    v-if="
                      new Date(client.subscription_expired_date) >= new Date()
                    "
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400"
                  >
                    <div
                      class="h-1.5 w-1.5 rounded-full bg-green-500 me-1.5"
                    ></div>
                    Payé
                  </div>
                  <div
                    v-else
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400"
                  >
                    <div
                      class="h-1.5 w-1.5 rounded-full bg-red-500 me-1.5"
                    ></div>
                    Non payé
                  </div>
                </td>

                <td class="px-6 py-4">
                  <q-btn-dropdown
                    flat
                    dense
                    class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                  >
                    <q-list
                      class="bg-white dark:bg-gray-900 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700"
                    >
                      <q-item
                        clickable
                        v-close-popup
                        @click="openInsuranceModal(client)"
                        :disable="
                          new Date(client.assurance_expired_date) >= new Date()
                        "
                        class="hover:bg-gray-50 dark:hover:bg-gray-800"
                      >
                        <q-item-section>
                          <q-item-label
                            class="flex items-center text-gray-700 dark:text-gray-200"
                          >
                            Créer une assurance
                            <q-icon
                              name="shield"
                              size="sm"
                              class="ms-2 text-indigo-500"
                            ></q-icon>
                          </q-item-label>
                        </q-item-section>
                      </q-item>

                      <q-item
                        clickable
                        v-close-popup
                        @click="openPaymentModal(client)"
                        :disable="
                          new Date(client.subscription_expired_date) >=
                          new Date()
                        "
                        class="hover:bg-gray-50 dark:hover:bg-gray-800"
                      >
                        <q-item-section>
                          <q-item-label
                            class="flex items-center text-gray-700 dark:text-gray-200"
                          >
                            Créer un paiement
                            <q-icon
                              name="attach_money"
                              size="sm"
                              class="ms-2 text-emerald-500"
                            ></q-icon>
                          </q-item-label>
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-btn-dropdown>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Chart Container -->
    <div class="lg:col-span-1">
      <div
        class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg overflow-hidden h-full border border-gray-100 dark:border-gray-700"
      >
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
          <h3 class="font-bold text-lg text-gray-800 dark:text-white">
            Monthly Revenue
          </h3>
        </div>
        <div class="p-4">
          <div id="chart">
            <apexchart
              type="bar"
              height="350"
              :options="chartOptions"
              :series="series"
            ></apexchart>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- charts -->
  <!-- Revenue Overview Charts -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 px-4 py-6">
    <!-- Monthly Revenue Trend -->
    <div
      class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700"
    >
      <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="font-bold text-lg text-gray-800 dark:text-white">
          Monthly Revenue Trend
        </h3>
      </div>
      <div class="p-4">
        <apexchart
          type="area"
          height="350"
          :options="{
            chart: {
              type: 'area',
              toolbar: {
                show: false,
              },
            },
            stroke: {
              curve: 'smooth',
              width: 2,
            },
            colors: ['#6366f1'],
            fill: {
              type: 'gradient',
              gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
              },
            },
            xaxis: {
              categories:
                revenue.monthly_revenue?.map((item) => item.month) || [],
              labels: {
                rotate: -45,
                style: {
                  colors: '#64748b',
                },
              },
            },
            yaxis: {
              labels: {
                formatter: (value) => `${value} MAD`,
              },
            },
            tooltip: {
              y: {
                formatter: (value) => `${value} MAD`,
              },
            },
          }"
          :series="[
            {
              name: 'Revenue',
              data: revenue.monthly_revenue?.map((item) => item.revenue) || [],
            },
          ]"
        />
      </div>
    </div>

    <!-- Revenue Comparison - Compact Version -->
    <div
      class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700"
    >
      <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="font-bold text-lg text-gray-800 dark:text-white">
          Year Over Year Comparison
        </h3>
      </div>
      <div class="p-4">
        <apexchart
          type="bar"
          height="350"
          :options="{
            chart: {
              type: 'bar',
              toolbar: {
                show: false,
              },
            },
            plotOptions: {
              bar: {
                borderRadius: 4,
                horizontal: true,
                barHeight: '50%',
              },
            },
            colors: ['#6366f1'],
            xaxis: {
              categories: ['Current Year', 'Last Year'],
              labels: {
                style: {
                  colors: '#64748b',
                },
              },
            },
            yaxis: {
              labels: {
                formatter: (value) => `${value} MAD`,
              },
            },
            tooltip: {
              y: {
                formatter: (value) => `${value} MAD`,
              },
            },
          }"
          :series="[
            {
              name: 'Revenue',
              data: [
                revenue.total_revenue_current_year || 0,
                revenue.total_revenue_last_year || 0,
              ],
            },
          ]"
        />
      </div>
    </div>
  </div>
  <!-- Modals -->
  <InsuranceForm
    v-model:visible="isInsuranceModalVisible"
    :client="selectedClient"
    @insuranceAdded="refreshPayments"
  />

  <PaymentForm
    v-model:visible="isPaymentModalVisible"
    :client="selectedClient"
    @new_payment="refreshPayments"
  />
</template>
<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import PaymentForm from "../Payments/createForm.vue";
import InsuranceForm from "../Insurance/createForm.vue";
const isInsuranceModalVisible = ref(false);
const isPaymentModalVisible = ref(false);
const selectedClient = ref(null);

const revenue = ref({});
const loading = ref(true);
const error = ref(null);

const backendData = ref({
  monthly_revenue: [],
});

import { computed } from "vue";

const series = computed(() => [
  {
    name: "Actual",
    data: backendData.value.monthly_revenue.map((item, index) => ({
      x: item.month,
      y: item.revenue,
      goals: [
        {
          name: "Expected",
          value:
            index === 0 ? backendData.value.total_revenue_current_year : null,
          strokeHeight: 5,
          strokeColor: "#775DD0",
        },
      ],
    })),
  },
]);

const chartOptions = {
  chart: {
    height: 350,
    type: "bar",
  },
  plotOptions: {
    bar: {
      columnWidth: "60%",
    },
  },
  colors: ["#00E396"],
  dataLabels: {
    enabled: false,
  },
  legend: {
    show: true,
    showForSingleSeries: true,
    customLegendItems: ["Actual", "Expected"],
    markers: {
      fillColors: ["#00E396", "#775DD0"],
    },
  },
};

const fetchRevenue = async () => {
  try {
    const response = await axios.get(
      "http://127.0.0.1:8000/api/revenue/monthly"
    ); // Ajuste l'URL si nécessaire
    revenue.value = response.data;
    backendData.value = response.data;
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
};

// Open the Insurance Modal
const openInsuranceModal = (client) => {
  selectedClient.value = client;
  isInsuranceModalVisible.value = true;
};

const refreshPayments = () => {
  fetchClients();
};

// Open the Payment Modal
const openPaymentModal = (client) => {
  selectedClient.value = client;
  isPaymentModalVisible.value = true;
};
const clients = ref([]);
const new_clients = ref({});
const active_subscriptions = ref({});
const expired_subscriptions = ref({});
let total_clients = ref([]);
const ShowClient = ref(false);
function showClient() {
  ShowClient.value = true;
}
// console.log(clients);
const fetchClients = () => {
  axios
    .get("/api/clients")

    .then(function (response) {
      console.log(response.data);
      clients.value = response.data.clients;
      new_clients.value = response.data.dashboard_stats.new_clients_this_month;
      active_subscriptions.value =
        response.data.dashboard_stats.active_subscriptions;
      expired_subscriptions.value =
        response.data.dashboard_stats.expired_subscriptions;
      total_clients.value = response.data.total_clients;

      // handle success
      // console.log(response);
    })
    .catch(function (_error) {
      // Error handling is commented out
    })
    .then(function () {
      // always executed
    });
};
onMounted(() => {
  fetchClients();
  fetchRevenue();
});
</script>
