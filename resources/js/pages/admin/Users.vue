
<template>
  <q-page class="q-pa-md">
    <div class="flex justify-between items-center q-mb-lg">
      <h1 class="text-2xl text-gray-800 font-bold">User Management</h1>
      <q-btn color="primary" icon="add" label="Add New User" @click="showAddModal = true" />
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 q-mb-md">
      <q-card class="text-center">
        <q-card-section>
          <div class="text-3xl font-bold text-primary">{{ users.length }}</div>
          <div class="text-sm text-gray-600">Total Users</div>
        </q-card-section>
      </q-card>
      <q-card class="text-center">
        <q-card-section>
          <div class="text-3xl font-bold text-primary">{{ activeUsers.length }}</div>
          <div class="text-sm text-gray-600">Active</div>
        </q-card-section>
      </q-card>
      <q-card class="text-center">
        <q-card-section>
          <div class="text-3xl font-bold text-primary">{{ users.filter(u => u.assistant).length }}</div>
          <div class="text-sm text-gray-600">Linked to Assistants</div>
        </q-card-section>
      </q-card>
    </div>
    
    <!-- Search and Filter -->
    <div class="flex flex-col sm:flex-row gap-4 q-mb-md">
      <q-input v-model="searchQuery" placeholder="Search users..." dense outlined class="flex-1" clearable>
        <template v-slot:prepend>
          <q-icon name="search" />
        </template>
      </q-input>
      <div class="flex gap-4">
        <q-select v-model="roleFilter" :options="roleOptions" label="Role" outlined dense />
        <q-select v-model="assistantFilter" :options="assistantFilterOptions" label="Assistant Status" outlined dense />
      </div>
    </div>

    <!-- Users Table -->
    <q-card class="q-mb-md">
      <q-table
        :rows="filteredUsers"
        :columns="columns"
        row-key="id"
        :pagination="{ rowsPerPage: 10 }"
        :loading="loading"
        v-model:sort="sorting"
      >
        <template v-slot:body="props">
          <q-tr :props="props">
            <q-td key="photo" :props="props">
              <q-avatar size="md">
                <img :src="props.row.profile_photo_path ? `/storage/${props.row.profile_photo_path}` : '/img/default-avatar.png'" :alt="props.row.name">
              </q-avatar>
            </q-td>
            <q-td key="name" :props="props">{{ props.row.name }}</q-td>
            <q-td key="email" :props="props">{{ props.row.email }}</q-td>
            <q-td key="role" :props="props">
              <q-badge :color="getRoleBadgeColor(props.row.role)" text-color="white" class="q-px-sm">
                {{ getRoleLabel(props.row.role) }}
              </q-badge>
            </q-td>
            <q-td key="assistant" :props="props">
              <div v-if="props.row.assistant">
                <q-badge color="green" text-color="white" class="q-px-sm">
                  Linked to: {{ props.row.assistant.name }}
                </q-badge>
              </div>
              <div v-else>
                <q-badge color="grey" text-color="white" class="q-px-sm">
                  Not Linked
                </q-badge>
              </div>
            </q-td>
            <q-td key="createdBy" :props="props">
              {{ props.row.creator ? props.row.creator.name : 'System' }}
            </q-td>
            <q-td key="actions" :props="props">
              <div class="flex gap-2">
                <q-btn flat round size="sm" color="primary" icon="edit" @click="editUser(props.row)">
                  <q-tooltip>Edit User</q-tooltip>
                </q-btn>
                <q-btn flat round size="sm" color="secondary" icon="lock" @click="viewPermissions(props.row)">
                  <q-tooltip>Manage Permissions</q-tooltip>
                </q-btn>
                <q-btn flat round size="sm" color="negative" icon="delete" @click="confirmDelete(props.row)">
                  <q-tooltip>Delete User</q-tooltip>
                </q-btn>
              </div>
            </q-td>
          </q-tr>
        </template>
        <template v-slot:no-data>
          <div class="text-center q-pa-xl">
            <q-icon name="person_off" size="3rem" color="grey" />
            <p class="text-grey-7">No users found</p>
            <q-btn color="primary" label="Add First User" @click="showAddModal = true" class="q-mt-md" />
          </div>
        </template>
      </q-table>
    </q-card>
    
    <!-- Add/Edit User Modal -->
    <q-dialog v-model="showUserDialog" persistent maximized-mobile>
      <q-card class="max-w-2xl w-full">
        <q-card-section class="flex items-center justify-between">
          <h2 class="text-h6">{{ showEditModal ? 'Edit User' : 'Add New User' }}</h2>
          <q-btn icon="close" flat round dense v-close-popup @click="closeModals" />
        </q-card-section>
        
        <q-separator />
        
        <q-card-section class="q-pa-md">
          <div class="flex flex-col md:flex-row gap-6">
            <!-- Photo upload -->
            <div class="flex flex-col items-center">
              <q-avatar size="100px" class="q-mb-sm">
                <img :src="userPhotoPreview || (formData.photo ? `/storage/${formData.photo}` : '/img/default-avatar.png')" />
              </q-avatar>
              <q-file
                v-model="photoFile"
                label="Upload Photo"
                outlined
                dense
                accept=".jpg,.jpeg,.png"
                @update:model-value="handleFileUpload"
                class="q-mt-sm"
                style="width: 200px"
              >
                <template v-slot:prepend>
                  <q-icon name="photo_camera" />
                </template>
              </q-file>
            </div>
            
            <!-- Form fields -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
              <q-input
                v-model="formData.name"
                label="Name *"
                outlined
                dense
                :rules="[val => !!val || 'Name is required']"
              />
              
              <q-input
                v-model="formData.email"
                label="Email *"
                outlined
                dense
                type="email"
                :rules="[
                  val => !!val || 'Email is required',
                  val => /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(val) || 'Invalid email format'
                ]"
              />
              
              <q-input
                v-model="formData.password"
                label="Password"
                :placeholder="showEditModal ? 'Leave blank to keep current' : 'Minimum 8 characters'"
                outlined
                dense
                type="password"
                :rules="[
                  val => showEditModal || !!val || 'Password is required',
                  val => !val || val.length >= 8 || 'Password must be at least 8 characters'
                ]"
              />
              
              <!-- Role selection from database -->
              <q-select
                v-model="formData.role"
                :options="availableRoles"
                option-value="id"
                option-label="name"
                emit-value
                map-options
                label="Role *"
                outlined
                dense
                :rules="[val => !!val || 'Role is required']"
              />
              
              <!-- Assistant selection -->
              <q-select
                v-model="formData.assistant_id"
                :options="availableAssistants"
                option-value="id"
                option-label="name"
                emit-value
                map-options
                label="Link to Assistant"
                outlined
                dense
                clearable
                class="col-span-2"
                @update:model-value="assistantSelected"
              >
                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section avatar>
                      <q-avatar>
                        <img :src="scope.opt.photo ? `/storage/${scope.opt.photo}` : '/img/default-avatar.png'" />
                      </q-avatar>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>{{ scope.opt.name }}</q-item-label>
                      <q-item-label caption>{{ scope.opt.email }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
              
              <!-- Display assistant info if selected -->
              <div v-if="selectedAssistant" class="col-span-2 bg-gray-100 p-3 rounded">
                <div class="flex items-center gap-3">
                  <q-avatar size="md">
                    <img :src="selectedAssistant.photo ? `/storage/${selectedAssistant.photo}` : '/img/default-avatar.png'" :alt="selectedAssistant.name">
                  </q-avatar>
                  <div>
                    <div class="font-bold">{{ selectedAssistant.name }}</div>
                    <div class="text-sm text-gray-600">{{ selectedAssistant.email }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </q-card-section>
        
        <q-separator />
        
        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey" v-close-popup @click="closeModals" />
          <q-btn flat label="Save" color="primary" @click="saveUser" :loading="saving" />
        </q-card-actions>
      </q-card>
    </q-dialog>
    
    <!-- Permissions Modal -->
    <q-dialog v-model="showPermissionsModal" persistent maximized-mobile>
      <q-card class="max-w-3xl w-full">
        <q-card-section class="flex items-center justify-between">
          <h2 class="text-h6">Manage Permissions: {{ selectedUser ? selectedUser.name : '' }}</h2>
          <q-btn icon="close" flat round dense v-close-popup @click="closeModals" />
        </q-card-section>
        
        <q-separator />
        
        <q-card-section class="q-pa-md">
          <!-- User info -->
          <div v-if="selectedUser" class="flex items-center gap-4 mb-4 bg-gray-100 p-3 rounded">
            <q-avatar size="lg">
              <img :src="selectedUser.profile_photo_path ? `/storage/${selectedUser.profile_photo_path}` : '/img/default-avatar.png'" :alt="selectedUser.name">
            </q-avatar>
            <div>
              <div class="text-h6">{{ selectedUser.name }}</div>
              <div class="text-subtitle1">{{ selectedUser.email }}</div>
              <q-badge :color="getRoleBadgeColor(selectedUser.role)" text-color="white" class="q-px-sm q-mt-sm">
                {{ getRoleLabel(selectedUser.role) }}
              </q-badge>
            </div>
          </div>
          
          <!-- Assistant info if linked -->
          <div v-if="selectedUser && selectedUser.assistant" class="mb-4 bg-blue-50 p-3 rounded">
            <div class="text-subtitle1 font-bold mb-2">Linked Assistant</div>
            <div class="flex items-center gap-3">
              <q-avatar size="md">
                <img :src="selectedUser.assistant.photo ? `/storage/${selectedUser.assistant.photo}` : '/img/default-avatar.png'" :alt="selectedUser.assistant.name">
              </q-avatar>
              <div>
                <div class="font-bold">{{ selectedUser.assistant.name }}</div>
                <div class="text-sm text-gray-600">{{ selectedUser.assistant.email }}</div>
              </div>
            </div>
          </div>
          
          <!-- Permissions list -->
          <div class="text-subtitle1 font-bold mb-2">Permissions</div>
          
          <div v-if="loading" class="text-center q-pa-md">
            <q-spinner color="primary" size="3em" />
            <p>Loading permissions...</p>
          </div>
          
          <div v-else>
            <div v-for="(group, resource) in groupedPermissions" :key="resource" class="q-mb-md">
              <div class="flex items-center justify-between bg-gray-100 p-2 rounded">
                <div class="font-bold">{{ formatResourceName(resource) }}</div>
                <q-checkbox 
                  v-model="groupSelectAll[resource]" 
                  @update:model-value="toggleGroupPermissions(group)"
                  :indeterminate="!isAllGroupSelected(group) && hasSelectedPermissions(group)"
                />
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2 q-mt-sm">
                <div v-for="permission in group" :key="permission.id" class="flex items-center justify-between p-2 border rounded">
                  <div>{{ formatPermissionName(permission.name.split('.')[1]) }}</div>
                  <q-checkbox v-model="selectedPermissions" :val="permission.id" />
                </div>
              </div>
            </div>
          </div>
        </q-card-section>
        
        <q-separator />
        
        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey" v-close-popup @click="closeModals" />
          <q-btn flat label="Save Permissions" color="primary" @click="savePermissions" :loading="saving" />
        </q-card-actions>
      </q-card>
    </q-dialog>
    
    <!-- Delete Confirmation Modal -->
    <q-dialog v-model="showDeleteModal" persistent>
      <q-card class="max-w-md w-full">
        <q-card-section class="flex items-center">
          <q-avatar icon="warning" color="negative" text-color="white" />
          <span class="text-h6 q-ml-md">Confirm Deletion</span>
        </q-card-section>
        
        <q-card-section>
          Are you sure you want to delete this user? This action cannot be undone.
          <div v-if="selectedUser && selectedUser.assistant" class="text-negative q-mt-sm">
            Warning: This user is linked to an assistant. Deleting this user will affect the assistant's access.
          </div>
        </q-card-section>
        
        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey" v-close-popup @click="closeModals" />
          <q-btn flat label="Delete" color="negative" @click="deleteUser" :loading="deleting" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { date } from 'quasar';
import axios from 'axios';
import { ref, reactive, computed, onMounted } from 'vue';

export default {
  name: 'UserManagement',
  
  setup() {
    // Data
    const users = ref([]);
    const availableRoles = ref([]);
    const availableAssistants = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const showAddModal = ref(false);
    const showEditModal = ref(false);
    const showPermissionsModal = ref(false);
    const showDeleteModal = ref(false);
    const searchQuery = ref('');
    const roleFilter = ref(null);
    const assistantFilter = ref(null);
    const selectedUser = ref(null);
    const selectedAssistant = ref(null);
    const photoFile = ref(null);
    const userPhotoPreview = ref(null);
    const selectedPermissions = ref([]);
    const allPermissions = ref([]);
    const groupedPermissions = ref({});
    const groupSelectAll = ref({});
    const sorting = ref({ field: 'name', order: 'asc' });
    
    const formData = reactive({
      id: null,
      name: '',
      email: '',
      password: '',
      role: '',
      assistant_id: null,
      photo: null
    });
    
    // Computed properties
    const showUserDialog = computed(() => showAddModal.value || showEditModal.value);
    
    const activeUsers = computed(() => {
      return users.value.filter(user => user.email_verified_at !== null);
    });
    
    const roleOptions = computed(() => {
      return [
        { label: 'All Roles', value: null },
        ...availableRoles.value.map(role => ({
          label: role.name,
          value: role.id
        }))
      ];
    });
    
    const assistantFilterOptions = computed(() => [
      { label: 'All Users', value: null },
      { label: 'Linked to Assistant', value: 'linked' },
      { label: 'Not Linked', value: 'not-linked' }
    ]);
    
    const filteredUsers = computed(() => {
      let result = [...users.value];
      
      // Apply search filter
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(user => 
          user.name.toLowerCase().includes(query) || 
          user.email.toLowerCase().includes(query)
        );
      }
      
      // Apply role filter
      if (roleFilter.value) {
        result = result.filter(user => user.role === roleFilter.value);
      }
      
      // Apply assistant filter
      if (assistantFilter.value === 'linked') {
        result = result.filter(user => user.assistant);
      } else if (assistantFilter.value === 'not-linked') {
        result = result.filter(user => !user.assistant);
      }
      
      // Apply sorting
      if (sorting.value) {
        const { field, order } = sorting.value;
        result.sort((a, b) => {
          let valA = a[field];
          let valB = b[field];
          
          // Handle nested properties
          if (field === 'assistant') {
            valA = a.assistant ? a.assistant.name : '';
            valB = b.assistant ? b.assistant.name : '';
          } else if (field === 'createdBy') {
            valA = a.creator ? a.creator.name : '';
            valB = b.creator ? b.creator.name : '';
          }
          
          if (valA < valB) return order === 'asc' ? -1 : 1;
          if (valA > valB) return order === 'asc' ? 1 : -1;
          return 0;
        });
      }
      
      return result;
    });
    
    const columns = [
      { name: 'photo', label: '', field: 'photo', align: 'center', sortable: false },
      { name: 'name', label: 'Name', field: 'name', sortable: true },
      { name: 'email', label: 'Email', field: 'email', sortable: true },
      { name: 'role', label: 'Role', field: 'role', sortable: true },
      { name: 'assistant', label: 'Assistant', field: 'assistant', sortable: true },
      { name: 'createdBy', label: 'Created By', field: 'createdBy', sortable: true },
      { name: 'actions', label: 'Actions', field: 'actions', align: 'center', sortable: false }
    ];
    
    // Methods
    const fetchUsers = async () => {
      loading.value = true;
      try {
        const response = await axios.get('/api/users');
        users.value = response.data.data;
      } catch (error) {
        console.error('Error fetching users:', error);
      } finally {
        loading.value = false;
      }
    };
    
    const fetchRoles = async () => {
      try {
        const response = await axios.get('/api/roles');
        availableRoles.value = response.data;
      } catch (error) {
        console.error('Error fetching roles:', error);
      }
    };
    
    const fetchAssistants = async () => {
      try {
        const response = await axios.get('/api/assistants');
        availableAssistants.value = response.data.data.filter(assistant => !assistant.user_account_id);
      } catch (error) {
        console.error('Error fetching assistants:', error);
      }
    };
    
    const fetchPermissions = async () => {
      try {
        const response = await axios.get('/api/permissions');
        allPermissions.value = response.data;
        groupedPermissions.value = groupPermissionsByResource(response.data);
        
        // Initialize group select all state
        Object.keys(groupedPermissions.value).forEach(resource => {
          groupSelectAll.value[resource] = false;
        });
      } catch (error) {
        console.error('Error fetching permissions:', error);
      }
    };
    
    const fetchUserPermissions = async (userId) => {
      try {
        const response = await axios.get(`/api/users/${userId}/permissions`);
        selectedPermissions.value = response.data.map(p => p.id);
        
        // Update group select all state
        updateGroupSelectAllState();
      } catch (error) {
        console.error('Error fetching user permissions:', error);
      }
    };
    
    const saveUser = async () => {
      saving.value = true;
      
      try {
        const formDataToSend = new FormData();
        
        // Add all form fields to FormData
        Object.keys(formData).forEach(key => {
          if (formData[key] !== null && key !== 'photo') {
            formDataToSend.append(key, formData[key]);
          }
        });
        
        // Add photo if selected
        if (photoFile.value) {
          formDataToSend.append('photo', photoFile.value);
        }
        
        let response;
        if (showEditModal.value) {
          response = await axios.post(`/api/users/${formData.id}`, formDataToSend, {
            headers: {
              'Content-Type': 'multipart/form-data',
              'X-HTTP-Method-Override': 'PUT'
            }
          });
        } else {
          response = await axios.post('/api/users', formDataToSend, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          });
        }
        
        // Update users list
        if (showEditModal.value) {
          const index = users.value.findIndex(user => user.id === formData.id);
          if (index !== -1) {
            users.value[index] = response.data;
          }
        } else {
          users.value.push(response.data);
        }
        
        closeModals();
      } catch (error) {
        console.error('Error saving user:', error);
        alert('Error saving user. Please check the form and try again.');
      } finally {
        saving.value = false;
      }
    };
    
    const editUser = (user) => {
      selectedUser.value = user;
      
      // Reset form data
      Object.keys(formData).forEach(key => {
        formData[key] = user[key] || null;
      });
      
      // Set assistant if linked
      if (user.assistant) {
        formData.assistant_id = user.assistant.id;
        selectedAssistant.value = user.assistant;
      } else {
        formData.assistant_id = null;
        selectedAssistant.value = null;
      }
      
      // Clear password since we don't want to update it automatically
      formData.password = '';
      
      // Reset photo preview
      photoFile.value = null;
      userPhotoPreview.value = null;
      
      showEditModal.value = true;
    };
    
    const viewPermissions = async (user) => {
      selectedUser.value = user;
      loading.value = true;
      
      try {
        // Fetch all permissions if not already loaded
        if (allPermissions.value.length === 0) {
          await fetchPermissions();
        }
        
        // Fetch user's permissions
        await fetchUserPermissions(user.id);
        
        showPermissionsModal.value = true;
      } catch (error) {
        console.error('Error loading permissions:', error);
      } finally {
        loading.value = false;
      }
    };
    
    const savePermissions = async () => {
      saving.value = true;
      
      try {
        await axios.post(`/api/users/${selectedUser.value.id}/permissions`, {
          permissions: selectedPermissions.value
        });
        
        closeModals();
      } catch (error) {
        console.error('Error saving permissions:', error);
      } finally {
        saving.value = false;
      }
    };
    
    const confirmDelete = (user) => {
      selectedUser.value = user;
      showDeleteModal.value = true;
    };
    
    const deleteUser = async () => {
      deleting.value = true;
      
      try {
        await axios.delete(`/api/users/${selectedUser.value.id}`);
        
        // Remove from users list
        const index = users.value.findIndex(user => user.id === selectedUser.value.id);
        if (index !== -1) {
          users.value.splice(index, 1);
        }
        
        closeModals();
      } catch (error) {
        console.error('Error deleting user:', error);
      } finally {
        deleting.value = false;
      }
    };
    
    const assistantSelected = (assistantId) => {
      if (assistantId) {
        selectedAssistant.value = availableAssistants.value.find(a => a.id === assistantId);
        
        // Auto-fill user data from assistant
        if (selectedAssistant.value) {
          formData.name = selectedAssistant.value.name;
          formData.email = selectedAssistant.value.email;
        }
      } else {
        selectedAssistant.value = null;
      }
    };
    
    const handleFileUpload = () => {
      if (photoFile.value) {
        const reader = new FileReader();
        reader.onload = (e) => {
          userPhotoPreview.value = e.target.result;
        };
        reader.readAsDataURL(photoFile.value);
      } else {
        userPhotoPreview.value = null;
      }
    };
    
    const closeModals = () => {
      showAddModal.value = false;
      showEditModal.value = false;
      showPermissionsModal.value = false;
      showDeleteModal.value = false;
      
      // Reset form data
      Object.keys(formData).forEach(key => {
        formData[key] = null;
      });
      
      selectedUser.value = null;
      selectedAssistant.value = null;
      photoFile.value = null;
      userPhotoPreview.value = null;
      selectedPermissions.value = [];
    };
    
    // Helper methods
    const groupPermissionsByResource = (permissions) => {
      const grouped = {};
      
      permissions.forEach(permission => {
        const [resource] = permission.name.split('.');
        
        if (!grouped[resource]) {
          grouped[resource] = [];
        }
        
        grouped[resource].push(permission);
      });
      
      return grouped;
    };
    
    const updateGroupSelectAllState = () => {
      Object.keys(groupedPermissions.value).forEach(resource => {
        const group = groupedPermissions.value[resource];
        groupSelectAll.value[resource] = isAllGroupSelected(group);
      });
    };
    
    const toggleGroupPermissions = (group) => {
      const resource = group[0].name.split('.')[0];
      const isSelected = groupSelectAll.value[resource];
      
      group.forEach(permission => {
        const index = selectedPermissions.value.indexOf(permission.id);
        
        if (isSelected && index === -1) {
          selectedPermissions.value.push(permission.id);
        } else if (!isSelected && index !== -1) {
          selectedPermissions.value.splice(index, 1);
        }
      });
    };
    
    const isAllGroupSelected = (group) => {
      return group.every(permission => selectedPermissions.value.includes(permission.id));
    };
    
    const hasSelectedPermissions = (group) => {
      return group.some(permission => selectedPermissions.value.includes(permission.id));
    };
    
    const formatResourceName = (resource) => {
      return resource.charAt(0).toUpperCase() + resource.slice(1).replace(/-/g, ' ');
    };
    
    const formatPermissionName = (action) => {
      return action.charAt(0).toUpperCase() + action.slice(1).replace(/-/g, ' ');
    };
    
    const getRoleLabel = (role) => {
      const roleObj = availableRoles.value.find(r => r.id === role || r.name === role);
      return roleObj ? roleObj.name : role;
    };
    
    const getRoleBadgeColor = (role) => {
      const roleMap = {
        'admin': 'purple',
        'manager': 'blue',
        'trainer': 'green',
        'receptionist': 'orange',
        'user': 'grey'
      };
      
      const roleName = typeof role === 'object' ? role.name : role;
      return roleMap[roleName.toLowerCase()] || 'grey';
    };
    
    const formatDate = (dateString) => {
      if (!dateString) return '';
      return date.formatDate(dateString, 'MMM D, YYYY');
    };
    
    // Lifecycle hooks
    onMounted(async () => {
      await Promise.all([
        fetchUsers(),
        fetchRoles(),
        fetchAssistants()
      ]);
    });
    
    return {
      // Data
      users,
      availableRoles,
      availableAssistants,
      loading,
      saving,
      deleting,
      showAddModal,
      showEditModal,
      showPermissionsModal,
      showDeleteModal,
      showUserDialog,
      searchQuery,
      roleFilter,
      assistantFilter,
      selectedUser,
      selectedAssistant,
      photoFile,
      userPhotoPreview,
      formData,
      selectedPermissions,
      groupedPermissions,
      groupSelectAll,
      sorting,
      columns,
      
      // Computed
      activeUsers,
      roleOptions,
      assistantFilterOptions,
      filteredUsers,
      
      // Methods
      fetchUsers,
      fetchRoles,
      fetchAssistants,
      saveUser,
      editUser,
      viewPermissions,
      savePermissions,
      confirmDelete,
      deleteUser,
      assistantSelected,
      handleFileUpload,
      closeModals,
      groupPermissionsByResource,
      toggleGroupPermissions,
      isAllGroupSelected,
      hasSelectedPermissions,
      formatResourceName,
      formatPermissionName,
      getRoleLabel,
      getRoleBadgeColor,
      formatDate
    };
  }
};
</script>

<style scoped>
.q-field--labeled.q-field--no-input .q-field__control {
  padding-top: 0;
}
.q-field--labeled.q-field--no-input .q-field__control-container {
  padding-top: 24px;
}
</style>