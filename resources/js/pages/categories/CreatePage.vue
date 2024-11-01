<template>
  <form @submit.prevent="submitForm">

    <q-input outlined dense v-model="form.fields.name" :error="'name' in form.errors"
      :error-message="form.errors.name?.[0]" label="Category Name" />

    <!-- <q-select
      outlined
      dense
      v-model="form.fields.icon"
      :options="iconOptions"
      label="Select an Icon"
      :error="'icon' in form.errors"
      :error-message="form.errors.icon?.[0]"
    /> -->

    <q-input outlined dense v-model="form.fields.emoji" label="Select an Emoji">
      <template v-slot:prepend>
        <q-icon name="sym_r_person" />
      </template>

      <template v-slot:append>
        <q-icon name="event" class="cursor-pointer">
          <q-popup-proxy cover :breakpoint="600">
            <EmojiPicker :native="false" @select="form.fields.emoji = $event.u" />
          </q-popup-proxy>
        </q-icon>
      </template>
    </q-input>


    <img :src="`https://cdn.jsdelivr.net/npm/emoji-datasource-apple@6.0.1/img/apple/64/${form.fields.emoji}.png`" alt="" style="width: 32px;">

    <q-btn color="primary" no-caps type="submit" unelevated class="w-full" :loading="form.processing">
      Save
    </q-btn>
  </form>
</template>

<script setup>
import { useForm } from '@/composables/useForm';
import { useRouter } from 'vue-router';
import EmojiPicker from 'vue3-emoji-picker'

import 'vue3-emoji-picker/css'

const router = useRouter();

const form = useForm({
  name: '',
  emoji: '',
});

async function submitForm() {
  await form.post('transaction-categories');

  router.push({ name: 'transaction-categories.index' });
}
</script>
