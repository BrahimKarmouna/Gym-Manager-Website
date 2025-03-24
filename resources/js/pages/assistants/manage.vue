<template>
  <q-page class="q-pa-md">
    <div class="flex justify-between items-center q-mb-lg">
      <h1 class="text-2xl text-gray-800 font-bold">Assistant Management</h1>
      <div class="flex items-center">
        <router-link :to="{ name: 'user-management' }" class="q-mr-md text-primary">
          Manage User Accounts
        </router-link>
        <q-btn color="primary" icon="add" label="Add Assistant" @click="openCreateModal" />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center q-pa-xl">
      <q-spinner size="3em" color="primary" />
    </div>

    <!-- Assistants Table -->
    <q-card v-else>
      <q-table
        :rows="assistants"
        :columns="columns"
        row-key="id"
        :pagination="{ rowsPerPage: 10 }"
        :loading="loading"
      >
        <template v-slot:body="props">
          <q-tr :props="props">
            <q-td key="name" :props="props">
              <div class="flex items-center">
                <q-avatar v-if="props.row.photo" size="40px">
                  <img :src="`/storage/${props.row.photo}`" />
                </q-avatar>
                <q-avatar v-else size="40px" color="grey-3" text-color="primary">
                  {{ props.row.name.charAt(0).toUpperCase() }}
                </q-avatar>
                <div class="q-ml-sm">
                  <div class="text-weight-medium">{{ props.row.name }}</div>
                  <div class="text-caption text-grey-7">{{ props.row.email }}</div>
                </div>
              </div>
            </q-td>
            <q-td key="role" :props="props">
              <q-badge :color="getRoleBadgeColor(props.row.role)">
                {{ props.row.role }}
              </q-badge>
            </q-td>
            <q-td key="gym" :props="props">
              {{ props.row.gym ? props.row.gym.name : 'Not assigned' }}
            </q-td>
            <q-td key="user_account" :props="props">
              <div v-if="props.row.user_account_id">
                <q-btn flat dense size="sm" color="primary" label="Linked to user" @click="viewUserAccount(props.row.user_account_id)" />
              </div>
              <div v-else>
                <q-btn flat dense size="sm" color="primary" label="Create user account" @click="linkUserAccount(props.row)" />
              </div>
            </q-td>
            <q-td key="status" :props="props">
              <q-badge :color="props.row.active ? 'positive' : 'negative'">
                {{ props.row.active ? 'Active' : 'Inactive' }}
              </q-badge>
            </q-td>
            <q-td key="actions" :props="props">
              <q-btn flat round size="sm" color="primary" icon="edit" @click="editAssistant(props.row)" />
              <q-btn flat round size="sm" color="negative" icon="delete" @click="confirmDeleteAssistant(props.row)" />
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </q-card>

    <!-- Create/Edit Assistant Modal -->
    <q-dialog v-model="assistantModalOpen" persistent>
      <q-card style="min-width: 500px">
        <q-card-section class="row items-center">
          <div class="text-h6">{{ editMode ? 'Edit Assistant' : 'Create Assistant' }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup @click="assistantModalOpen = false" />
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit="saveAssistant">
            <q-input
              v-model="form.name"
              label="Name"
              :error="errors.name !== undefined"
              :error-message="errors.name"
              filled
              class="q-mb-md"
            />

            <q-input
              v-model="form.email"
              label="Email"
              type="email"
              :error="errors.email !== undefined"
              :error-message="errors.email"
              filled
              class="q-mb-md"
            />

            <q-input
              v-model="form.phone"
              label="Phone"
              :error="errors.phone !== undefined"
              :error-message="errors.phone"
              filled
              class="q-mb-md"
            />

            <q-select
              v-model="form.role"
              :options="[
                { label: 'Manager', value: 'manager' },
                { label: 'Trainer', value: 'trainer' },
                { label: 'Receptionist', value: 'receptionist' }
              ]"
              label="Role"
              filled
              class="q-mb-md"
            />

            <q-select
              v-model="form.gym_id"
              :options="gyms.map(gym => ({ label: gym.name, value: gym.id }))"
              label="Gym"
              filled
              class="q-mb-md"
            />

            <q-select
              v-model="form.user_account_id"
              :options="[{ label: 'Not linked to a user account', value: null }, ...availableUsers.map(user => ({ label: `${user.name} (${user.email})`, value: user.id }))]"
              label="User Account"
              filled
              class="q-mb-md"
            />

            <q-file
              @update:model-value="handlePhotoUpload"
              label="Photo"
              filled
              accept="image/*"
              class="q-mb-md"
            >
              <template v-slot:prepend>
                <q-icon name="attach_file" />
              </template>
            </q-file>

            <q-toggle v-model="form.active" label="Active" class="q-mb-md" />

            <q-input
              v-model="form.notes"
              label="Notes"
              type="textarea"
              filled
              class="q-mb-md"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey-7" v-close-popup @click="assistantModalOpen = false" />
          <q-btn flat label="Save" color="primary" :loading="processing" @click="saveAssistant" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Delete Confirmation Modal -->
    <q-dialog v-model="deleteModalOpen" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <div class="text-h6">Delete Assistant</div>
        </q-card-section>

        <q-card-section>
          Are you sure you want to delete this assistant? This action cannot be undone.
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey-7" v-close-popup @click="deleteModalOpen = false" />
          <q-btn flat label="Delete" color="negative" :loading="processing" @click="deleteAssistant" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default defineComponent({
  name: 'AssistantManagePage',
  
  setup() {
    const router = useRouter();
    return { router };
  },
  
  data() {
    return {
      loading: true,
      processing: false,
      assistants: [],
      gyms: [],
      availableUsers: [],
      assistantModalOpen: false,
      deleteModalOpen: false,
      editMode: false,
      currentAssistant: null,
      errors: {},
      form: this.getDefaultForm(),
      columns: [
        { name: 'name', required: true, label: 'Assistant', align: 'left', field: 'name', sortable: true },
        { name: 'role', align: 'center', label: 'Role', field: 'role', sortable: true },
        { name: 'gym', align: 'left', label: 'Gym', field: row => row.gym ? row.gym.name : 'None' },
        { name: 'user_account', align: 'center', label: 'User Account' },
        { name: 'status', align: 'center', label: 'Status' },
        { name: 'actions', align: 'center', label: 'Actions' }
      ]
    };
  },
  
  methods: {
    getDefaultForm() {
      return {
        name: '',
        email: '',
        phone: '',
        role: 'trainer',
        gym_id: '',
        user_account_id: null,
        photo: null,
        active: true,
        notes: '',
      };
    },
    
    getRoleBadgeColor(role) {
      const colors = {
        manager: 'primary',
        trainer: 'positive',
        receptionist: 'orange'
      };
      return colors[role] || 'grey';
    },
    
    async loadAssistants() {
      this.loading = true;
      
      try {
        const response = await axios.get('/api/assistants');
        this.assistants = response.data;
      } catch (error) {
        console.error('Error loading assistants:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async loadGyms() {
      try {
        const response = await axios.get('/api/gyms/user-gyms');
        this.gyms = response.data;
        
        // Set default gym if available
        if (this.gyms.length > 0 && !this.form.gym_id) {
          this.form.gym_id = this.gyms[0].id;
        }
      } catch (error) {
        console.error('Error loading gyms:', error);
      }
    },
    
    async loadAvailableUsers() {
      try {
        const response = await axios.get('/api/user-management/users');
        // Filter out users that are already linked to assistants
        this.availableUsers = response.data.filter(user => {
          // Include the current assistant's linked user when editing
          if (this.currentAssistant && user.id === this.currentAssistant.user_account_id) {
            return true;
          }
          return !user.assistant;
        });
      } catch (error) {
        console.error('Error loading available users:', error);
      }
    },
    
    openCreateModal() {
      this.editMode = false;
      this.currentAssistant = null;
      this.form = this.getDefaultForm();
      
      // Set default gym if available
      if (this.gyms.length > 0) {
        this.form.gym_id = this.gyms[0].id;
      }
      
      this.errors = {};
      this.assistantModalOpen = true;
      this.loadAvailableUsers();
    },
    
    editAssistant(assistant) {
      this.editMode = true;
      this.currentAssistant = assistant;
      this.form = {
        name: assistant.name,
        email: assistant.email,
        phone: assistant.phone || '',
        role: assistant.role,
        gym_id: assistant.gym_id,
        user_account_id: assistant.user_account_id,
        photo: null, // Don't include the existing photo in the form
        active: assistant.active,
        notes: assistant.notes || '',
      };
      this.errors = {};
      this.assistantModalOpen = true;
      this.loadAvailableUsers();
    },
    
    handlePhotoUpload(file) {
      this.form.photo = file;
    },
    
    async saveAssistant() {
      this.processing = true;
      this.errors = {};
      
      const formData = new FormData();
      Object.keys(this.form).forEach(key => {
        // Skip the photo if it's null
        if (key === 'photo' && !this.form[key]) return;
        
        formData.append(key, this.form[key]);
      });
      
      try {
        if (this.editMode) {
          // Update existing assistant
          const response = await axios.post(`/api/assistants/${this.currentAssistant.id}?_method=PUT`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
          
          // Find and replace the updated assistant in the list
          const index = this.assistants.findIndex(a => a.id === this.currentAssistant.id);
          if (index !== -1) {
            this.assistants[index] = response.data.assistant;
          }
        } else {
          // Create new assistant
          const response = await axios.post('/api/assistants', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
          this.assistants.push(response.data.assistant);
        }
        
        this.assistantModalOpen = false;
      } catch (error) {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          console.error('Error saving assistant:', error);
        }
      } finally {
        this.processing = false;
      }
    },
    
    confirmDeleteAssistant(assistant) {
      this.currentAssistant = assistant;
      this.deleteModalOpen = true;
    },
    
    async deleteAssistant() {
      this.processing = true;
      
      try {
        await axios.delete(`/api/assistants/${this.currentAssistant.id}`);
        
        // Remove the deleted assistant from the list
        this.assistants = this.assistants.filter(a => a.id !== this.currentAssistant.id);
        
        this.deleteModalOpen = false;
      } catch (error) {
        console.error('Error deleting assistant:', error);
      } finally {
        this.processing = false;
      }
    },
    
    viewUserAccount(userId) {
      this.router.push({ name: 'user-management', query: { userId } });
    },
    
    async linkUserAccount(assistant) {
      // Create a new user account for this assistant
      try {
        const userData = {
          name: assistant.name,
          email: assistant.email,
          password: Math.random().toString(36).slice(-10), // Generate random password
          role: assistant.role,
          assistant_id: assistant.id
        };
        
        const response = await axios.post('/api/user-management/users', userData);
        
        // Refresh the assistants list
        this.loadAssistants();
        
        // Show success message
        this.$q.notify({
          message: `User account created for ${assistant.name}. An email will be sent with login instructions.`,
          color: 'positive'
        });
      } catch (error) {
        console.error('Error creating user account:', error);
        this.$q.notify({
          message: 'Error creating user account. Please try again.',
          color: 'negative'
        });
      }
    }
  },
  
  mounted() {
    this.loadAssistants();
    this.loadGyms();
  },
});
</script>
