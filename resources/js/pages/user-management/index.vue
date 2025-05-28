<template>
  <q-page class="bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
            <p class="mt-1 text-sm text-gray-600">Manage your system users, roles, and permissions</p>
          </div>
          <div class="flex items-center space-x-4">
            <q-btn
              color="secondary"
              icon="badge"
              label="Manage Roles"
              :to="{ name: 'user-management.roles' }"
              class="bg-gradient-to-r from-purple-600 to-indigo-600"
            />
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
                :color="props.row.roles[0]?.color || 'grey'"
                text-color="white"
                dense
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
    >
      <q-card
        class="column"
        style="width: 700px; max-width: 90vw"
      >
        <q-card-section class="row items-center bg-primary text-white">
          <div class="text-h6">{{ editingUser ? 'Edit User' : 'Create New User' }}</div>
          <q-space />
          <q-btn
            icon="close"
            flat
            round
            dense
            v-close-popup
          />
        </q-card-section>

        <q-card-section class="q-pa-lg col scroll">
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
            <q-card flat bordered class="q-mb-md">
              <q-card-section class="bg-blue-1">
                <div class="text-subtitle1 q-mb-sm text-weight-bold">Role & Permissions</div>
              </q-card-section>
              <q-card-section>
                <div class="row q-col-gutter-md">
                  <div class="col-12 col-md-6">
                    <q-select
                      v-model="userForm.role"
                      :options="roleOptions"
                      label="Role"
                      outlined
                      emit-value
                      map-options
                      :rules="[val => !!val || 'Role is required']"
                    >
                      <template v-slot:option="scope">
                        <q-item v-bind="scope.itemProps">
                          <q-item-section>
                            <q-item-label>{{ scope.opt.label }}</q-item-label>
                            <q-item-label caption>{{ scope.opt.description || 'No description available' }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </template>
                    </q-select>
                  </div>

                  <div class="col-12 col-md-6 flex items-center">
                    <q-toggle
                      v-model="userForm.is_admin"
                      label="Administrator Account"
                      color="primary"
                    />
                  </div>
                </div>
                
                <!-- Team Assignment (shown when role is assistant) -->
                <div class="row q-col-gutter-md q-mt-md" v-if="userForm.role && isAssistantRole(userForm.role)">
                  <div class="col-12">
                    <div class="text-subtitle2 q-mb-sm text-weight-medium">Team Assignment</div>
                    <q-select
                      v-model="userForm.teams"
                      :options="teamOptions"
                      label="Assign to Teams"
                      outlined
                      multiple
                      emit-value
                      map-options
                      use-chips
                      :disable="userForm.is_admin"
                    >
                      <template v-slot:option="scope">
                        <q-item v-bind="scope.itemProps">
                          <q-item-section>
                            <q-item-label>{{ scope.opt.label }}</q-item-label>
                            <q-item-label caption>{{ scope.opt.description || 'No description' }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </template>
                    </q-select>
                  </div>
                </div>

                <div class="q-mt-md">
                  <div class="text-subtitle2 q-mb-sm">
                    <div class="flex items-center justify-between">
                      <span class="text-weight-bold">Additional Permissions</span>
                      <q-btn
                        flat
                        dense
                        color="primary"
                        label="Select All"
                        @click="toggleAllPermissions"
                      />
                    </div>
                  </div>

                  <div class="row q-col-gutter-md">
                    <template v-for="(group, groupName) in groupedPermissions" :key="groupName">
                      <div class="col-12 col-md-6 col-lg-4">
                        <q-card flat bordered>
                          <q-card-section class="bg-grey-2">
                            <div class="text-subtitle2 text-weight-medium">{{ formatGroupName(groupName) }}</div>
                          </q-card-section>
                          <q-card-section class="q-pa-sm">
                            <q-list dense>
                              <q-item v-for="perm in group" :key="perm.value">
                                <q-item-section>
                                  <q-checkbox
                                    v-model="userForm.permissions"
                                    :val="perm.value"
                                    :label="perm.label"
                                    :disable="userForm.is_admin"
                                  />
                                </q-item-section>
                              </q-item>
                            </q-list>
                          </q-card-section>
                        </q-card>
                      </div>
                    </template>
                  </div>
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

    // Form data
    const userForm = ref({
      name: '',
      email: '',
      password: '',
      role: null,
      permissions: [],
      is_admin: false,
      teams: []
    });

    // Options data
    const roleOptions = ref([]);
    const permissionOptions = ref([]);
    const assistantOptions = ref([]);
    const teamOptions = ref([]);
    const hasRolesLoaded = ref(false);
    const hasPermissionsLoaded = ref(false);
    const hasAssistantsLoaded = ref(false);
    const hasTeamsLoaded = ref(false);

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
          group: permission.group
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
        const response = await api.get('/user-management/available-assistants');
        assistantOptions.value = response.data.assistants.map(assistant => ({
          label: assistant.name,
          value: assistant.id
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
    
    const fetchTeams = async () => {
      try {
        const response = await api.get('/api/teams');
        teamOptions.value = response.data.teams.map(team => ({
          label: team.display_name || team.name,
          value: team.id,
          description: team.description
        }));
        hasTeamsLoaded.value = true;
      } catch (err) {
        console.error('Error fetching teams:', err);
        $q.notify({
          color: 'negative',
          message: 'Failed to load teams',
          icon: 'error'
        });
      }
    };

    const isAssistantRole = (roleId) => {
      // Find the role by ID and check if it's an assistant role
      const role = roleOptions.value.find(r => r.value === roleId);
      return role && role.label.toLowerCase().includes('assistant');
    };

    const openUserForm = (user = null) => {
      userForm.value = {
        name: '',
        email: '',
        password: '',
        role: null,
        permissions: [],
        is_admin: false,
        teams: []
      };

      if (user) {
        editingUser.value = user;
        
        // Handle role assignment properly
        let roleId = null;
        
        // First, check if user has a role_id directly
        if (user.role_id) {
          roleId = user.role_id;
        } 
        // Then check if user has roles from Spatie
        else if (user.roles && user.roles.length > 0) {
          roleId = user.roles[0].id;
        } 
        // Finally, try to find a matching role by name if user has a string role
        else if (user.role) {
          const matchingRole = roleOptions.value.find(r => 
            r.label.toLowerCase() === user.role.toLowerCase());
          if (matchingRole) {
            roleId = matchingRole.value;
          }
        }
        
        userForm.value = {
          ...userForm.value,
          name: user.name,
          email: user.email,
          role: roleId,
          permissions: user.permissions?.map(p => typeof p === 'object' ? p.id : p) || [],
          is_admin: user.is_admin,
          teams: user.teams?.map(t => t.id) || []
        };
      } else {
        editingUser.value = null;
      }

      showUserForm.value = true;
    };

    const saveUser = async () => {
      saving.value = true;

      try {
        // Create a clean object with only the data we need to send
        const userData = {
          name: userForm.value.name,
          email: userForm.value.email,
          is_admin: userForm.value.is_admin,
          permissions: userForm.value.permissions || [],
        };
        
        // Only include password if it's not empty
        if (userForm.value.password) {
          userData.password = userForm.value.password;
        }
        
        // Handle role assignment - both for Spatie roles and the role field
        if (userForm.value.role !== null) {
          // Set role_id for Spatie role assignment
          userData.role_id = userForm.value.role;
          
          // Also set the string role field based on the selected role
          const selectedRole = roleOptions.value.find(r => r.value === userForm.value.role);
          if (selectedRole) {
            userData.role = selectedRole.label;
          }
        }
        
        // Add team assignments if the user is an assistant
        if (isAssistantRole(userForm.value.role)) {
          userData.teams = userForm.value.teams || [];
          // If this is an assistant role, make sure we set the role field correctly
          userData.role = 'assistant';
        }
        
        console.log('Sending user data:', JSON.stringify(userData));
        
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

    const toggleAllPermissions = () => {
      if (userForm.value.permissions.length === permissionOptions.value.length) {
        userForm.value.permissions = [];
      } else {
        userForm.value.permissions = permissionOptions.value.map(p => p.value);
      }
    };

    const groupedPermissions = computed(() => {
      const groups = {};
      permissionOptions.value.forEach(permission => {
        if (!groups[permission.group]) {
          groups[permission.group] = [];
        }
        groups[permission.group].push(permission);
      });
      return groups;
    });

    const formatGroupName = (groupName) => {
      return groupName.replace('_', ' ').replace(/\w\S*/g, (w) => (w.replace(/^\w/, (c) => c.toUpperCase())));
    };

    // Initialize
    onMounted(() => {
      fetchUsers();
      fetchRolesAndPermissions();
      fetchAvailableAssistants();
      fetchTeams();
    });

    return {
      // Data
      users,
      loading,
      error,
      successMessage,
      search,
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
      teamOptions,
      hasRolesLoaded,
      hasPermissionsLoaded,
      hasAssistantsLoaded,
      hasTeamsLoaded,

      // Methods
      fetchUsers,
      openUserForm,
      saveUser,
      confirmDeleteUser,
      deleteUser,
      toggleAllPermissions,
      groupedPermissions,
      formatGroupName,
      isAssistantRole
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
