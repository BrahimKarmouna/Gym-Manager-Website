<template>
  <q-page>
    <q-dialog v-model="showCreateModal">
      <q-card style="min-width: 350px; width: 500px;">
        <q-card-section>
          <div class="text-h6">Create Post</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div class="mb-3">
            <q-input v-model="form.title" label="Title" dense outlined />
          </div>

          <div>
            <q-input v-model="form.content" label="Content" dense type="textarea" outlined />
          </div>
        </q-card-section>

        <q-card-section class="q-pt-none" align="right">
          <q-btn flat label="Cancel" color="primary" class="me-2" v-close-popup />
          <q-btn unelevated label="Save" color="primary" @click="createPost" :loading="saving" />
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="showEditModal">
      <q-card style="min-width: 350px; width: 500px;">
        <q-card-section>
          <div class="text-h6">Edit Post</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div class="mb-3">
            <q-input v-model="form.title" label="Title" dense outlined />
          </div>

          <div>
            <q-input v-model="form.content" label="Content" dense type="textarea" outlined />
          </div>
        </q-card-section>

        <q-card-section class="q-pt-none" align="right">
          <q-btn flat label="Cancel" color="primary" class="me-2" v-close-popup />
          <q-btn unelevated label="Save" color="primary" @click="savePost" :loading="saving" />
        </q-card-section>
      </q-card>
    </q-dialog>

    <div class="p-6">

      <div class="flex justify-between items-center">

        <div class="text-h6 mb-5">Posts</div>

        <q-btn color="primary" icon="add" label="Create" unelevated @click="openCreateModal" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <div v-for="post in posts" :key="post.id">
          <q-card class="h-full">
            <q-card-section>
              <div class="flex items-center mb-5">
                <RouterLink :to="{ name: 'posts.show', params: { id: post.id } }">
                  <div class="text-h6 text-wrap">
                    {{ post.title }}
                  </div>
                </RouterLink>

                <q-space />

                <span class="text-caption text-gray-500">
                  {{ post.created_at }}
                </span>
              </div>
              <div class="text-subtitle2">
                <div class="flex items-center">
                  <img :src="post.user.photo_url" class="rounded-full w-8 h-8 me-2" />
                  <span>
                    {{ post.user.name }}
                  </span>

                  <q-btn flat padding="sm" size="xs" class="ms-auto" color="primary" icon="edit" @click="openEditModal(post)" />
                  <q-btn flat padding="sm" size="xs" color="negative" icon="delete" @click="deleteItem(post)" />
                </div>
              </div>
            </q-card-section>

            <q-card-section class="q-pt-none">
              <p>
                {{ post.content }}
              </p>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { api } from '@/boot/axios';
import { useQuasar } from 'quasar';
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';

const posts = ref([]);

const $q = useQuasar();

const form = ref({
  title: '',
  content: '',
});

const saving = ref(false);
const deleting = ref(false);

const showCreateModal = ref(false);
const showEditModal = ref(false);

onMounted(() => {
  api.get('posts').then(response => {
    posts.value = response.data.data;
  });
});

function openCreateModal() {
  showCreateModal.value = true;
}

function openEditModal(post) {
  form.value = {
    id: post.id,
    title: post.title,
    content: post.content,
  };

  showEditModal.value = true;
}

function createPost() {
  saving.value = true;

  api.post('posts', form.value).then(() => {
    showCreateModal.value = false;

    form.value = {
      title: '',
      content: '',
    };

    api.get('posts').then(response => {
      posts.value = response.data.data;
    });

    saving.value = false;
  });
}

function savePost() {
  saving.value = true;

  api.put(`posts/${form.value.id}`, form.value).then(() => {
    showEditModal.value = false;

    form.value = {
      title: '',
      content: '',
    };

    api.get('posts').then(response => {
      posts.value = response.data.data;
    });

    saving.value = false;
  }).catch(error => {
    saving.value = false;
  });
}


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

      api.get('posts').then(response => {
        posts.value = response.data.data;
      });
    });
  });
}
</script>

<style scoped>
p {
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
