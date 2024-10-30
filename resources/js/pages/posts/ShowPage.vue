<template>
  <q-page>
    <div class="m-6 w-1/2 mx-auto bg-white p-6 rounded-md dark:bg-gray-800">

      <div class="flex items-center mb-5">
        <div class="text-h6">
          {{ post.title }}
        </div>

        <q-space />

        <span class="text-caption text-gray-500">
          {{ post.created_at }}
        </span>
      </div>

      <div class="text-subtitle2">
        <div class="inline-flex items-center">
          <img :src="post.user.photo_url" class="rounded-full w-8 h-8 me-2" />
          {{ post.user.name }}
        </div>
      </div>

      <p>
        {{ post.content }}
      </p>

      <div class="flex items-center justify-end mt-8 gap-3">
        <q-btn label="Edit" color="primary" flat icon="edit" />
        <q-btn label="Delete" color="red" flat icon="delete" @click="deleteItem(post)" :loading="deleting" />
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { api } from '@/boot/axios';
import { useQuasar } from 'quasar';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  id: {
    type: String
  }
});

const post = ref({});

const $q = useQuasar();

const router = useRouter();

const deleting = ref(false);

onMounted(() => {
  api.get(`posts/${props.id}`).then(response => {
    post.value = response.data.data;
  });
});

function deleteItem(post) {
  $q.dialog({
    title: 'Confirm',
    message: `Are you sure you want to delete ${post.title}?`,
    cancel: true,
    persistent: true,
  }).onOk(() => {
    deleting.value = true;

    api.delete(`/posts/${post.id}`).then(() => {
      deleting.value = false;

      $q.notify({
        type: 'positive',
        message: 'Success!',
        caption: 'Post deleted successfully.',
        position: 'bottom-right',
        timeout: 3000
      });

      router.replace({ name: 'posts.index' });
    });
  });
}
</script>
