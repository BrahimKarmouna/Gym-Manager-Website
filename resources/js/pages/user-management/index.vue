<template>
  <q-page padding>
    <div class="q-pa-md">
      <q-card
        flat
        bordered
        class="q-mb-md bg-grey-1"
      >
        <q-card-section class="bg-primary text-white">
          <div class="text-h5">User Management</div>
          <div class="text-subtitle2">Manage system users, roles and permissions</div>
        </q-card-section>

        <!-- Loading indicator -->
        <div
          v-if="loading"
          class="text-center q-pa-xl"
        >
          <q-spinner
            color="primary"
            size="3em"
          />
          <div class="text-subtitle1 q-mt-md">Loading users...</div>
        </div>

        <!-- Error message -->
        <q-banner
          v-if="error"
          class="bg-negative text-white q-mx-md q-my-sm rounded-borders"
        >
          {{ error }}
          <template v-slot:action>
            <q-btn
              flat
              color="white"
              label="Retry"
              @click="fetchUsers"
            />
          </template>
        </q-banner>

        <!-- Success message -->
        <q-banner
          v-if="successMessage"
          class="bg-positive text-white q-mx-md q-my-sm rounded-borders"
        >
          {{ successMessage }}
        </q-banner>

        <q-card-section v-if="!loading">
          <!-- Search and actions -->
          <div class="row justify-between q-mb-md items-center">
            <div class="col-12 col-md-6">
              <q-input
                v-model="search"
                dense
                outlined
                placeholder="Search users..."
                class="q-mb-md"
                bg-color="white"
              >
                <template v-slot:prepend>
                  <q-icon name="search" />
                </template>
                <template v-slot:append>
                  <q-icon
                    name="close"
                    class="cursor-pointer"
                    v-if="search"
                    @click="search = ''"
                  />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-6 text-right">
              <q-btn
                v-if="$permission && ($permission('manage-users') || $permission('manage users') || $permission('create users') || $permission('create-users'))"
                color="primary"
                icon="add"
                label="New User"
                @click="openUserForm()"
                class="q-mr-sm"
              />
              <q-btn
                outline
                color="primary"
                icon="refresh"
                label="Refresh"
                @click="fetchUsers"
              />
            </div>
          </div>

          <!-- User Table -->
          <q-table
            :rows="users"
            :columns="columns"
            :loading="loading"
            row-key="id"
            :filter="search"
            :pagination.sync="pagination"
            class="my-sticky-header-table"
            flat
            bordered
          >
            <!-- Custom column slots -->
            <template v-slot:body-cell-actions="props">
              <q-td
                :props="props"
                class="text-center"
              >
                <div class="row justify-center">
                  <q-btn-group
                    flat
                    spread
                  >
                    <q-btn
                      v-if="$permission && ($permission('manage-users') || $permission('manage users') || $permission('update users') || $permission('update-users'))"
                      flat
                      dense
                      color="primary"
                      icon="edit"
                      @click="openUserForm(props.row)"
                    >
                      <q-tooltip>Edit User</q-tooltip>
                    </q-btn>
                    <q-btn
                      v-if="$permission && ($permission('manage-users') || $permission('manage users') || $permission('delete users') || $permission('delete-users'))"
                      flat
                      dense
                      color="negative"
                      icon="delete"
                      @click="confirmDeleteUser(props.row)"
                    >
                      <q-tooltip>Delete User</q-tooltip>
                    </q-btn>
                  </q-btn-group>
                </div>
              </q-td>
            </template>

            <template v-slot:no-data>
              <div class="full-width row flex-center q-pa-md text-grey-7">
                <q-icon
                  name="sentiment_dissatisfied"
                  size="2em"
                  class="q-mr-md"
                />
                No users found
              </div>
            </template>
          </q-table>
        </q-card-section>
      </q-card>

      <!-- User Form Modal -->
      <q-dialog
        v-model="showUserForm"
        persistent
        maximized
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
                        label="Role"
                        outlined
                        emit-value
                        map-options
                        :disable="!hasRolesLoaded"
                      >
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
                      >
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

                  <div class="q-mt-md">
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
    </div>
  </q-page>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import { api } from '../../boot/axios';
import { useAuthStore } from '../../stores/auth';

export default defineComponent({
  name: 'UserManagementPage',

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
          value: role.id
        }));
        permissionOptions.value = response.data.permissions.map(permission => ({
          label: permission.name,
          value: permission.id
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
        const response = await api.get('/user-management/assistants');
        assistantOptions.value = response.data.map(assistant => ({
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

        // Convert role_id to role string for backend
        if (userData.role_id) {
          const roleObj = roleOptions.value.find(r => r.value === userData.role_id);
          userData.role = roleObj ? roleObj.label : '';
          // Keep both role_id and role for compatibility with different controllers
        }

        let response;
        if (editingUser.value) {
          response = await api.put(`/user-management/users/${editingUser.value.id}`, userData);
          const index = users.value.findIndex(u => u.id === editingUser.value.id);
          if (index !== -1) {
            users.value[index] = response.data.data;
          }
          successMessage.value = 'User updated successfully';
        } else {
          response = await api.post('/user-management/users', userData);
          users.value.push(response.data.data);
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

      // Methods
      fetchUsers,
      openUserForm,
      saveUser,
      confirmDeleteUser,
      deleteUser
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
