<template>
  <q-page padding>
    <div class="q-pa-md">
      <div class="text-h4 q-mb-md">User Management</div>

      <!-- Loading indicator -->
      <div
        v-if="loading"
        class="flex flex-center q-pa-xl"
      >
        <q-spinner
          color="primary"
          size="3em"
        />
        <span class="q-ml-sm text-subtitle1">Loading users...</span>
      </div>

      <!-- Error message -->
      <q-banner
        v-if="error"
        rounded
        class="bg-negative text-white q-mb-md"
      >
        <template v-slot:avatar>
          <q-icon name="error" />
        </template>
        {{ error }}
        <template v-slot:action>
          <q-btn
            flat
            label="Retry"
            color="white"
            @click="fetchUsers"
          />
        </template>
      </q-banner>

      <!-- Success message -->
      <q-banner
        v-if="successMessage"
        rounded
        class="bg-positive text-white q-mb-md"
      >
        <template v-slot:avatar>
          <q-icon name="check_circle" />
        </template>
        {{ successMessage }}
        <template v-slot:action>
          <q-btn
            flat
            label="Dismiss"
            color="white"
            @click="successMessage = ''"
          />
        </template>
      </q-banner>

      <!-- Actions toolbar -->
      <div class="row q-mb-md items-center">
        <div class="col-6">
          <q-input
            v-model="search"
            dense
            outlined
            placeholder="Search users..."
            class="q-mr-sm"
            debounce="300"
          >
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
        <div class="col-6 text-right">
          <q-btn
            v-if="canCreateUsers"
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

      <!-- Users Table -->
      <q-table
        :rows="filteredUsers"
        :columns="columns"
        row-key="id"
        :loading="loading"
        :pagination="pagination"
        :filter="search"
        :rows-per-page-options="[10, 20, 50, 0]"
        binary-state-sort
        flat
        bordered
        class="users-table"
      >
        <!-- Role column with colored badge -->
        <template v-slot:body-cell-role="props">
          <q-td :props="props">
            <q-badge
              :color="getRoleBadgeColor(props.value)"
              :label="props.value"
              class="text-capitalize"
            />
          </q-td>
        </template>

        <!-- Status column -->
        <template v-slot:body-cell-status="props">
          <q-td :props="props">
            <q-badge
              :color="props.row.assistant ? 'blue-6' : ''"
              :label="props.row.assistant ? 'Linked to Assistant' : 'Regular Account'"
            />
          </q-td>
        </template>

        <!-- Admin column -->
        <template v-slot:body-cell-is_admin="props">
          <q-td :props="props">
            <q-icon
              :name="props.value ? 'check_circle' : 'cancel'"
              :color="props.value ? 'positive' : 'negative'"
              size="sm"
            />
          </q-td>
        </template>

        <!-- Actions column -->
        <template v-slot:body-cell-actions="props">
          <q-td
            :props="props"
            class="text-center"
          >
            <div class="row justify-center">
              <q-btn-group flat>
                <q-btn
                  v-if="canUpdateUsers"
                  flat
                  dense
                  color="primary"
                  icon="edit"
                  @click="openUserForm(props.row)"
                  class="q-px-sm"
                >
                  <q-tooltip>Edit User</q-tooltip>
                </q-btn>
                <q-btn
                  v-if="canDeleteUsers"
                  flat
                  dense
                  color="negative"
                  icon="delete"
                  @click="confirmDeleteUser(props.row)"
                  class="q-px-sm"
                >
                  <q-tooltip>Delete User</q-tooltip>
                </q-btn>
              </q-btn-group>
            </div>
          </q-td>
        </template>

        <!-- No data message -->
        <template v-slot:no-data>
          <div class="full-width row flex-center q-pa-md text-accent">
            <q-icon
              name="sentiment_dissatisfied"
              size="2em"
              class="q-mr-sm"
            />
            No users found
          </div>
        </template>
      </q-table>
    </div>

    <!-- User Form Modal -->
    <q-dialog
      v-model="showUserForm"
      persistent
      maximized
      transition-show="slide-up"
      transition-hide="slide-down"
    >
      <q-card class="column">
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
            <div class="text-subtitle1 q-mb-sm">User Details</div>
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

            <div class="row q-col-gutter-md">
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
              <div class="col-12 col-md-6">
                <q-toggle
                  v-model="userForm.is_admin"
                  label="Administrator Account"
                />
              </div>
            </div>

            <!-- Role & Permissions Section -->
            <q-separator class="q-my-md" />
            <div class="text-subtitle1 q-mb-sm">Role & Permissions</div>

            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <q-select
                  v-model="userForm.role"
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

            <div>
              <div class="text-subtitle2 q-mb-sm">Permissions</div>
              <q-option-group
                v-model="userForm.permissions"
                :options="permissionOptions"
                type="checkbox"
                :disable="!hasPermissionsLoaded"
              />
            </div>

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
import { api } from '../../boot/axios';
import { useAuthStore } from '../../stores/auth';
import { hasPermission } from '../../utils/permissions';

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

    // Methods
    const fetchUsers = async () => {
      if (!$q.permission('view-users')) {
        error.value = 'You do not have permission to view users';
        return;
      }

      loading.value = true;
      error.value = '';

      try {
        const response = await api.get('/api/user-management/users');
        users.value = response.data;
      } catch (err) {
        console.error('Error fetching users:', err);
        error.value = err.response?.data?.message || 'Failed to load users. Please try again.';
      } finally {
        loading.value = false;
      }
    };

    onMounted(async () => {
      if ($q.permission('view-users')) {
        await fetchUsers();
        await fetchRolesAndPermissions();
        await fetchAvailableAssistants();
      } else {
        error.value = 'You do not have permission to view users';
      }
    });

    return {
      // Data
      users,
      loading,
      error,
      successMessage,
      search,

      // Methods
      fetchUsers,
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
