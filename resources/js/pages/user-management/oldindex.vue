<template>
  <q-page class="bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">User Managementtt</h1>
            <p class="mt-1 text-sm text-gray-600">Manage your system users, roles, and permissions</p>
          </div>
          <q-btn
            color="primary"
            icon="add"
            label="New User"
            @click="openUserForm()"
            class="bg-gradient-to-r from-blue-600 to-indigo-600"
          />
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <q-card
          flat
          bordered
          class="bg-white"
        >
          <q-card-section>
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-medium text-gray-500">Total Users</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ users.length }}</div>
              </div>
              <div class="p-3 bg-blue-50 rounded-full">
                <q-icon
                  name="people"
                  size="24px"
                  class="text-blue-600"
                />
              </div>
            </div>
            <div class="mt-4">
              <q-linear-progress
                :value="users.length / 100"
                color="blue"
                class="rounded-full"
              />
            </div>
          </q-card-section>
        </q-card>

        <q-card
          flat
          bordered
          class="bg-white"
        >
          <q-card-section>
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-medium text-gray-500">Active Roles</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ roleOptions.length }}</div>
              </div>
              <div class="p-3 bg-green-50 rounded-full">
                <q-icon
                  name="badge"
                  size="24px"
                  class="text-green-600"
                />
              </div>
            </div>
            <div class="mt-4">
              <q-linear-progress
                :value="roleOptions.length / 10"
                color="green"
                class="rounded-full"
              />
            </div>
          </q-card-section>
        </q-card>

        <q-card
          flat
          bordered
          class="bg-white"
        >
          <q-card-section>
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-medium text-gray-500">Total Permissions</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ permissionOptions.length }}</div>
              </div>
              <div class="p-3 bg-purple-50 rounded-full">
                <q-icon
                  name="lock"
                  size="24px"
                  class="text-purple-600"
                />
              </div>
            </div>
            <div class="mt-4">
              <q-linear-progress
                :value="permissionOptions.length / 20"
                color="purple"
                class="rounded-full"
              />
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- User Statistics Chart -->
      <div class="mb-6">
        <q-card
          flat
          bordered
          class="bg-white"
        >
          <q-card-section>
            <UserStatisticsChart :users="users" />
          </q-card-section>
        </q-card>
      </div>

      <!-- Search and Filter Bar -->
      <q-card
        flat
        bordered
        class="bg-white mb-6"
      >
        <q-card-section class="row items-center q-gutter-md">
          <q-input
            v-model="search"
            dense
            outlined
            placeholder="Search users..."
            class="col-grow"
          >
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
          <q-select
            v-model="selectedRole"
            :options="roleOptions"
            dense
            outlined
            label="Filter by role"
            class="col-auto"
            emit-value
            map-options
            clearable
          />
        </q-card-section>
      </q-card>

      <!-- Users Table -->
      <q-card
        flat
        bordered
        class="bg-white"
      >
        <q-table
          :rows="users"
          :columns="columns"
          row-key="id"
          :loading="loading"
          :filter="search"
          flat
          bordered
          separator="cell"
          class="rounded-borders"
        >
          <template v-slot:loading>
            <q-inner-loading
              showing
              color="primary"
            >
              <q-spinner-dots
                size="50px"
                color="primary"
              />
            </q-inner-loading>
          </template>

          <!-- Custom Column Slots -->
          <template v-slot:body-cell-avatar="props">
            <q-td
              :props="props"
              class="text-center"
            >
              <q-avatar size="32px">
                <img
                  :src="props.row.profile_photo_url"
                  :alt="props.row.name"
                >
              </q-avatar>
            </q-td>
          </template>

          <template v-slot:body-cell-role="props">
            <q-td :props="props">
              <q-chip
                :color="getRoleColor(props.row.roles[0]?.name)"
                text-color="white"
                size="sm"
                class="text-xs"
              >
                {{ props.row.roles[0]?.name || 'No Role' }}
              </q-chip>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td
              :props="props"
              class="text-center"
            >
              <div class="flex justify-center space-x-2">
                <q-btn
                  v-if="$permission && ($permission('manage-users') || $permission('update-users'))"
                  flat
                  round
                  size="sm"
                  color="primary"
                  icon="edit"
                  @click="openUserForm(props.row)"
                >
                  <q-tooltip>Edit User</q-tooltip>
                </q-btn>
                <q-btn
                  v-if="$permission && ($permission('manage-users') || $permission('delete users'))"
                  flat
                  round
                  size="sm"
                  color="negative"
                  icon="delete"
                  @click="confirmDeleteUser(props.row)"
                >
                  <q-tooltip>Delete User</q-tooltip>
                </q-btn>
              </div>
            </q-td>
          </template>
        </q-table>
      </q-card>
    </div>

    <!-- User Form Modal -->
    <q-dialog
      v-model="showUserForm"
      persistent
      transition-show="slide-up"
      transition-hide="slide-down"
      :maximized="$q.screen.lt.sm"
      :full-width="$q.screen.lt.md"
    >
      <q-card
        class="column"
        style="max-width: 900px; margin: 0 auto; max-height: 90vh;"
      >
        <q-card-section class="row items-center bg-primary text-white q-py-sm justify-center">
          <div class="text-h6 q-mx-auto">{{ editingUser ? 'Edit User' : 'Create New User' }}</div>
          <q-space />
          <q-btn
            icon="close"
            flat
            round
            dense
            v-close-popup
          />
        </q-card-section>

        <q-separator />

        <q-card-section class="q-pa-md col scroll" style="overflow-x: hidden;">
          <q-form
            @submit="saveUser"
            class="q-gutter-md"
          >
            <!-- User Details Section -->
            <q-card
              flat
              bordered
              class="q-mb-md"
            >
              <q-card-section class="bg-blue-1">
                <div class="text-subtitle1 q-mb-sm text-weight-bold">User Details</div>
              </q-card-section>
              <q-card-section>
                <div class="row q-col-gutter-md">
                  <div class="col-12 col-md-6">
                    <q-input
                      v-model="userForm.name"
                      label="Name"
                      outlined
                      :rules="[val => !!val || 'Name is required']"
                      class="full-width"
                    />
                  </div>
                  <div class="col-12 col-md-6">
                    <q-input
                      v-model="userForm.email"
                      label="Email"
                      outlined
                      type="email"
                      :rules="[
                        val => !!val || 'Email is required',
                        val => /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(val) || 'Please enter a valid email'
                      ]"
                      class="full-width"
                    />
                  </div>
                </div>

                <div class="row q-col-gutter-md q-mt-md">
                  <div class="col-12 col-md-6">
                    <q-input
                      v-model="userForm.password"
                      label="Password"
                      outlined
                      type="password"
                      :hint="editingUser ? 'Leave blank to keep current password' : 'Minimum 8 characters'"
                      :rules="[
                        val => editingUser ? true : !!val || 'Password is required',
                        val => !val || val.length >= 8 || 'Password must be at least 8 characters'
                      ]"
                      class="full-width"
                    />
                  </div>
                  <div class="col-12 col-md-6 flex items-center">
                    <q-toggle
                      v-model="userForm.is_admin"
                      label="Administrator Account"
                      color="primary"
                    />
                  </div>
                </div>
              </q-card-section>
            </q-card>

            <!-- Role & Permissions Section -->
            <q-card
              flat
              bordered
              class="q-mb-md"
            >
              <q-card-section class="bg-blue-1">
                <div class="text-subtitle1 q-mb-sm text-weight-bold">Role & Permissions</div>
              </q-card-section>
              <q-card-section>
                <div class="row q-col-gutter-md">
                  <div class="col-12 col-md-6">
                    <q-select
                      v-model="userForm.role_id"
                      :options="roleOptions"
                      label="Role *"
                      outlined
                      emit-value
                      map-options
                      :disable="!hasRolesLoaded"
                      :rules="[val => !!val || 'Role is required']"
                      lazy-rules
                    >
                      <template v-slot:option="scope">
                        <q-item v-bind="scope.itemProps">
                          <q-item-section>
                            <q-item-label>{{ scope.opt.label }}</q-item-label>
                            <q-item-label caption>{{ scope.opt.description || 'No description available' }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </template>
                      <template v-slot:no-option>
                        <q-item>
                          <q-item-section class="text-grey">
                            No roles available
                          </q-item-section>
                        </q-item>
                      </template>
                    </q-select>
                  </div>
                  <div class="col-12 col-md-6">
                    <q-select
                      v-model="userForm.assistant_id"
                      :options="assistantOptions"
                      label="Link to Assistant (Optional)"
                      outlined
                      emit-value
                      map-options
                      clearable
                      :disable="!hasAssistantsLoaded"
                      @update:model-value="assistantSelected"
                    >
                      <template v-slot:option="scope">
                        <q-item v-bind="scope.itemProps">
                          <q-item-section avatar>
                            <q-avatar>
                              <img :src="scope.opt.photo || '/img/default-avatar.png'" />
                            </q-avatar>
                          </q-item-section>
                          <q-item-section>
                            <q-item-label>{{ scope.opt.label }}</q-item-label>
                            <q-item-label caption>{{ scope.opt.email || '' }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </template>
                      <template v-slot:no-option>
                        <q-item>
                          <q-item-section class="text-grey">
                            No assistants available
                          </q-item-section>
                        </q-item>
                      </template>
                    </q-select>
                  </div>
                </div>

                <!-- Display assistant info if selected -->
                <div v-if="selectedAssistant" class="q-mt-md bg-gray-100 p-3 rounded">
                  <div class="flex items-center gap-3">
                    <q-avatar size="md">
                      <img :src="selectedAssistant.photo || '/img/default-avatar.png'" :alt="selectedAssistant.name">
                    </q-avatar>
                    <div>
                      <div class="font-bold">{{ selectedAssistant.name }}</div>
                      <div class="text-sm text-gray-600">{{ selectedAssistant.email }}</div>
                    </div>
                  </div>
                  
                  <!-- Permission selection for assistant -->
                  <div class="mt-4">
                    <q-checkbox v-model="showPermissionsInUserForm" label="Configure assistant permissions" />
                    
                    <div v-if="showPermissionsInUserForm" class="mt-3">
                      <div v-if="permissionsLoading" class="text-center q-pa-md">
                        <q-spinner color="primary" size="2em" />
                        <p class="q-mt-sm">Loading permissions...</p>
                      </div>
                      
                      <div v-else>
                        <q-list separator>
                          <template v-for="(group, resource) in groupedPermissions" :key="resource">
                            <q-expansion-item
                              :label="formatResourceName(resource)"
                              header-class="text-primary font-medium"
                              expand-icon-class="text-primary"
                              :default-opened="false"
                            >
                              <q-card>
                                <q-card-section>
                                  <div class="flex justify-between items-center q-mb-sm">
                                    <div class="text-subtitle2">Permissions</div>
                                    <q-btn
                                      flat
                                      size="sm"
                                      dense
                                      :color="isAllGroupSelected(group) ? 'negative' : 'primary'"
                                      :label="isAllGroupSelected(group) ? 'Deselect All' : 'Select All'"
                                      @click="toggleGroupPermissions(group)"
                                    />
                                  </div>
                                  
                                  <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <div v-for="permission in group" :key="permission.id" class="flex items-center justify-between p-2 border rounded hover:bg-gray-50">
                                      <div class="flex-1">
                                        <div class="font-medium">{{ formatPermissionName(permission.name.split('.')[1]) }}</div>
                                        <div class="text-xs text-gray-500">{{ getPermissionDescription(permission) }}</div>
                                      </div>
                                      <q-checkbox v-model="userForm.permissions" :val="permission.id" color="primary" class="ml-2" />
                                    </div>
                                  </div>
                                </q-card-section>
                              </q-card>
                            </q-expansion-item>
                          </template>
                        </q-list>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="!selectedAssistant" class="q-mt-md">
                  <div class="text-subtitle2 q-mb-sm text-weight-bold">Permissions</div>
                  <q-card
                    flat
                    bordered
                  >
                    <q-card-section class="q-pa-sm">
                      <div class="row q-col-gutter-sm">
                        <div
                          v-for="(permission, index) in permissionOptions"
                          :key="index"
                          class="col-12 col-sm-6 col-md-4"
                        >
                          <q-checkbox
                            v-model="userForm.permissions"
                            :val="permission.value"
                            :label="permission.label"
                            :disable="!hasPermissionsLoaded"
                            color="primary"
                          />
                        </div>
                      </div>
                    </q-card-section>
                  </q-card>
                </div>
              </q-card-section>
            </q-card>

            <!-- Actions -->
            <div class="row justify-end q-mt-lg">
              <q-btn
                label="Cancel"
                flat
                color="negative"
                v-close-popup
                class="q-mr-sm"
              />
              <q-btn
                label="Save"
                type="submit"
                color="primary"
                :loading="saving"
              />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Delete Confirmation Dialog -->
    <q-dialog
      v-model="showDeleteConfirm"
      persistent
    >
      <q-card>
        <q-card-section class="row items-center">
          <q-avatar
            icon="warning"
            color="negative"
            text-color="white"
          />
          <span class="q-ml-sm">Delete User</span>
        </q-card-section>

        <q-card-section>
          Are you sure you want to delete user <strong>{{ userToDelete?.name }}</strong>?
          <p class="text-caption q-mt-sm">This action cannot be undone.</p>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn
            flat
            label="Cancel"
            color="primary"
            v-close-popup
          />
          <q-btn
            flat
            label="Delete"
            color="negative"
            :loading="deleting"
            @click="deleteUser"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import { useAuthStore } from '@/stores/auth';
import { api } from '@/boot/axios';
import UserStatisticsChart from '@/components/UserStatisticsChart.vue';

export default defineComponent({
  name: 'UserManagementPage',

  components: {
    UserStatisticsChart
  },

  setup() {
    const $q = useQuasar();
    const authStore = useAuthStore();

    // Data
    const users = ref([]);
    const loading = ref(false);
    const error = ref('');
    const successMessage = ref('');
    const search = ref('');
    const selectedRole = ref(null);
    const columns = [
      { name: 'name', required: true, label: 'Name', align: 'left', field: 'name', sortable: true },
      { name: 'email', required: true, label: 'Email', align: 'left', field: 'email', sortable: true },
      { name: 'role', required: true, label: 'Role', align: 'left', field: row => row.role || 'User', sortable: true },
      { name: 'assistant', label: 'Assistant', align: 'left', field: row => row.assistant?.name || '-' },
      { name: 'creator', label: 'Created By', align: 'left', field: row => row.creator_name || '-', sortable: true },
      { name: 'actions', required: true, label: 'Actions', align: 'center' }
    ];
    const pagination = ref({
      sortBy: 'name',
      descending: false,
      page: 1,
      rowsPerPage: 10
    });
    const showUserForm = ref(false);
    const showDeleteConfirm = ref(false);
    const editingUser = ref(null);
    const userToDelete = ref(null);
    const saving = ref(false);
    const deleting = ref(false);
    const permissionsLoading = ref(false);
    const showPermissionsInUserForm = ref(false);
    const selectedAssistant = ref(null);
    const groupedPermissions = ref({});
    const groupSelectAll = ref({});

    // Form data
    const userForm = ref({
      name: '',
      email: '',
      password: '',
      role_id: null,
      permissions: [],
      assistant_id: null,
      is_admin: false
    });

    // Options data
    const roleOptions = ref([]);
    const permissionOptions = ref([]);
    const assistantOptions = ref([]);
    const hasRolesLoaded = ref(false);
    const hasPermissionsLoaded = ref(false);
    const hasAssistantsLoaded = ref(false);

    // Methods
    const fetchUsers = async () => {
      loading.value = true;
      error.value = '';

      try {
        const response = await api.get('/user-management/users');
        users.value = response.data.data;
      } catch (err) {
        console.error('Error fetching users:', err);
        error.value = err.response?.data?.message || 'Failed to load users';
      } finally {
        loading.value = false;
      }
    };

    const fetchRolesAndPermissions = async () => {
      try {
        const response = await api.get('/user-management/roles-permissions');
        roleOptions.value = response.data.roles.map(role => ({
          label: role.name,
          value: role.id,
          description: role.description
        }));
        permissionOptions.value = response.data.permissions.map(permission => ({
          label: permission.name,
          value: permission.id,
          name: permission.name,
          id: permission.id,
          description: permission.description
        }));
        hasRolesLoaded.value = true;
        hasPermissionsLoaded.value = true;
      } catch (err) {
        console.error('Error fetching roles and permissions:', err);
        $q.notify({
          color: 'negative',
          message: 'Failed to load roles and permissions',
          icon: 'error'
        });
      }
    };

    const fetchAvailableAssistants = async () => {
      try {
        const response = await api.get('/api/user-management/assistants');
        assistantOptions.value = response.data.map(assistant => ({
          label: assistant.name,
          value: assistant.id,
          name: assistant.name,
          email: assistant.email,
          photo: assistant.photo,
          id: assistant.id
        }));
        hasAssistantsLoaded.value = true;
      } catch (err) {
        console.error('Error fetching assistants:', err);
        $q.notify({
          color: 'negative',
          message: 'Failed to load assistants',
          icon: 'error'
        });
      }
    };

    const openUserForm = (user = null) => {
      userForm.value = {
        name: '',
        email: '',
        password: '',
        role_id: null,
        permissions: [],
        assistant_id: null,
        is_admin: false
      };
      
      selectedAssistant.value = null;
      showPermissionsInUserForm.value = false;

      if (user) {
        editingUser.value = user;
        userForm.value = {
          ...userForm.value,
          name: user.name,
          email: user.email,
          role_id: user.role_id || (user.roles && user.roles.length > 0 ? user.roles[0].id : null),
          permissions: user.permissions?.map(p => typeof p === 'object' ? p.id : p) || [],
          assistant_id: user.assistant_id,
          is_admin: user.is_admin
        };
        
        // Set selected assistant if user has one
        if (user.assistant_id) {
          const assistant = assistantOptions.value.find(a => a.value === user.assistant_id);
          if (assistant) {
            selectedAssistant.value = assistant;
          }
        }
      } else {
        editingUser.value = null;
      }

      showUserForm.value = true;
    };

    const saveUser = async () => {
      saving.value = true;

      try {
        const userData = { ...userForm.value };
        if (!userData.password) delete userData.password;

        // Keep role_id as is, no need to convert to role string
        delete userData.role; // Remove any role string if present

        let response;
        if (editingUser.value) {
          response = await api.put(`/user-management/users/${editingUser.value.id}`, userData);
          const index = users.value.findIndex(u => u.id === editingUser.value.id);
          if (index !== -1) {
            users.value[index] = response.data.user;
          }
          successMessage.value = 'User updated successfully';
        } else {
          response = await api.post('/user-management/users', userData);
          users.value.push(response.data.user);
          successMessage.value = 'User created successfully';
        }

        showUserForm.value = false;
        selectedAssistant.value = null;
        showPermissionsInUserForm.value = false;
        
        $q.notify({
          color: 'positive',
          message: successMessage.value,
          icon: 'check_circle'
        });
      } catch (err) {
        console.error('Error saving user:', err);
        $q.notify({
          color: 'negative',
          message: err.response?.data?.message || 'Failed to save user',
          icon: 'error'
        });
      } finally {
        saving.value = false;
      }
    };

    const confirmDeleteUser = (user) => {
      userToDelete.value = user;
      showDeleteConfirm.value = true;
    };

    const deleteUser = async () => {
      if (!userToDelete.value) return;

      deleting.value = true;

      try {
        await api.delete(`/user-management/users/${userToDelete.value.id}`);
        users.value = users.value.filter(u => u.id !== userToDelete.value.id);

        $q.notify({
          color: 'positive',
          message: 'User deleted successfully',
          icon: 'check_circle'
        });

        showDeleteConfirm.value = false;
      } catch (err) {
        console.error('Error deleting user:', err);
        $q.notify({
          color: 'negative',
          message: err.response?.data?.message || 'Failed to delete user',
          icon: 'error'
        });
      } finally {
        deleting.value = false;
        userToDelete.value = null;
      }
    };

    const assistantSelected = async (assistantId) => {
      if (assistantId) {
        selectedAssistant.value = assistantOptions.value.find(a => a.value === assistantId);
        
        // Auto-fill user data from assistant
        if (selectedAssistant.value) {
          userForm.value.name = selectedAssistant.value.name;
          userForm.value.email = selectedAssistant.value.email;
          
          // Load permissions for this assistant
          permissionsLoading.value = true;
          try {
            // Fetch all permissions if not already loaded
            if (permissionOptions.value.length === 0) {
              await fetchRolesAndPermissions();
            }
            
            // Group permissions by resource
            groupedPermissions.value = groupPermissionsByResource(permissionOptions.value);
            
            // Clear selected permissions
            userForm.value.permissions = [];
            
            // Update group select all state
            updateGroupSelectAllState();
            
            // Show permissions section automatically
            showPermissionsInUserForm.value = true;
          } catch (error) {
            console.error('Error loading permissions:', error);
          } finally {
            permissionsLoading.value = false;
          }
        }
      } else {
        selectedAssistant.value = null;
        showPermissionsInUserForm.value = false;
      }
    };
    
    // Helper methods for permissions
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
        const index = userForm.value.permissions.indexOf(permission.id);
        
        if (isSelected && index !== -1) {
          userForm.value.permissions.splice(index, 1);
        } else if (!isSelected && index === -1) {
          userForm.value.permissions.push(permission.id);
        }
      });
      
      // Update group select all state
      groupSelectAll.value[resource] = !isSelected;
    };
    
    const isAllGroupSelected = (group) => {
      return group.every(permission => userForm.value.permissions.includes(permission.id));
    };
    
    const formatResourceName = (resource) => {
      return resource.charAt(0).toUpperCase() + resource.slice(1).replace(/-/g, ' ');
    };
    
    const formatPermissionName = (action) => {
      return action.charAt(0).toUpperCase() + action.slice(1).replace(/-/g, ' ');
    };
    
    const getPermissionDescription = (permission) => {
      // Map of permission actions to descriptions
      const actionDescriptions = {
        'view': 'Can view and access this resource',
        'create': 'Can create new items in this resource',
        'edit': 'Can modify existing items in this resource',
        'delete': 'Can remove items from this resource',
        'manage': 'Has full control over this resource',
        'approve': 'Can approve items in this resource',
        'reject': 'Can reject items in this resource',
        'export': 'Can export data from this resource',
        'import': 'Can import data into this resource'
      };
      
      // Extract the action part from permission name (e.g., "view" from "clients.view")
      const action = permission.name.split('.')[1];
      
      return permission.description || actionDescriptions[action] || 'Access to this functionality';
    };
    
    const getRoleColor = (roleName) => {
      const roleColors = {
        'admin': 'purple',
        'manager': 'blue',
        'trainer': 'green',
        'receptionist': 'orange',
        'user': 'grey'
      };
      
      return roleColors[roleName?.toLowerCase()] || 'grey';
    };

    // Initialize
    onMounted(async () => {
      await Promise.all([
        fetchUsers(),
        fetchRolesAndPermissions(),
        fetchAvailableAssistants()
      ]);
    });

    return {
      // Data
      users,
      loading,
      error,
      successMessage,
      search,
      selectedRole,
      columns,
      pagination,
      showUserForm,
      showDeleteConfirm,
      editingUser,
      userToDelete,
      saving,
      deleting,
      userForm,
      roleOptions,
      permissionOptions,
      assistantOptions,
      hasRolesLoaded,
      hasPermissionsLoaded,
      hasAssistantsLoaded,
      permissionsLoading,
      showPermissionsInUserForm,
      selectedAssistant,
      groupedPermissions,
      groupSelectAll,

      // Methods
      fetchUsers,
      openUserForm,
      saveUser,
      confirmDeleteUser,
      deleteUser,
      assistantSelected,
      groupPermissionsByResource,
      toggleGroupPermissions,
      isAllGroupSelected,
      formatResourceName,
      formatPermissionName,
      getPermissionDescription,
      getRoleColor
    };
  }
});
</script>

<style scoped>
.users-table {
  border-radius: 8px;
  box-shadow: 0 1px 5px rgb(0 0 0 / 10%);
}
</style>
