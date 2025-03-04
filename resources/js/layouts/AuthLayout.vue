<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container>
      <q-page>
        <div class="col-12 flex content-center justify-center overflow-hidden">
          <div class="w-full-6xl w-full mx-auto"> <!-- Restrict max width -->

            <div class="w-full">
              <header>

                <nav class="bg-black border-gray-900 py-2  ">
                  <div class=" flex flex-wrap items-center justify-between mx-auto p-2 h-16">
                    <!-- Logo -->
                    <a
                      href="#"
                      class="flex items-center space-x-3"
                    >
                      <img
                        src="../../../public/images/logo/logo.png"
                        class="h-24 max-w-full"
                        alt="Flowbite Logo"
                      />
                    </a>

                    <!-- Liens au centre -->
                    <div class="hidden md:flex space-x-8 text-white text-lg font-medium">
                      <router-link
                        class="hover:text-gray-400"
                        to="/"
                      >Home</router-link>
                      <router-link
                        class="hover:text-gray-400"
                        to="/products"
                      >Products</router-link>
                      <a
                        href="#"
                        class="hover:text-gray-400"
                      >Pricing</a>
                      <a
                        href="#"
                        class="hover:text-gray-400"
                      >Contact</a>
                    </div>

                    <!-- Bouton de langue à droite -->
                    <div class="flex items-center space-x-4">
                      <button
                        type="button"
                        data-dropdown-toggle="language-dropdown-menu"
                        class="inline-flex items-center font-medium justify-center px-4 py-2 text-sm text-white rounded-lg
               cursor-pointer hover:bg-gray-700"
                      >
                        <svg
                          class="w-5 h-5 rounded-full me-3"
                          aria-hidden="true"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 3900 3900"
                        >
                          <path
                            fill="#b22234"
                            d="M0 0h7410v3900H0z"
                          />
                          <path
                            d="M0 450h7410m0 600H0m0 600h7410m0 600H0m0 600h7410m0 600H0"
                            stroke="#fff"
                            stroke-width="300"
                          />
                          <path
                            fill="#3c3b6e"
                            d="M0 0h2964v2100H0z"
                          />
                        </svg>
                        English (US)
                      </button>
                    </div>
                  </div>
                </nav>

              </header>
              <router-view />
            </div>
          </div>
        </div>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup>
</script>
