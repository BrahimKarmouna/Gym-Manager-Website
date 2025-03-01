<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container>
      <q-page padding>
        <div class="bg-white dark:bg-black row fullscreen">
          <div class="col-12 flex content-center justify-center">
            <div class="w-full">
              <router-view />
            </div>
          </div>
        </div>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup></script>
