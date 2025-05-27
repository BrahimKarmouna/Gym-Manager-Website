<template>
  <q-page class="bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Role Management</h1>
            <p class="mt-1 text-sm text-gray-600">Create and manage roles and their permissions</p>
          </div>
          <q-btn
            color="primary"
            icon="add"
            label="New Role"
            @click="openRoleForm()"
            class="bg-gradient-to-r from-blue-600 to-indigo-600"
          />
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <!-- Roles Table -->
      <q-table
        :rows="roles"
        :columns="columns"
        row-key="id"
        :loading="loading"
        flat
        bordered
        class="bg-white"
      >
        <template v-slot:body-cell-color="props">
          <q-td :props="props">
            <div class="flex items-center">
              <div
                class="w-6 h-6 rounded mr-2"
                :style="{ backgroundColor: props.row.color }"
              />
              {{ props.row.color }}
            </div>
          </q-td>
        </template>

        <template v-slot:body-cell-users="props">
          <q-td :props="props">
            <q-chip
              color="grey-3"
              text-color="grey-8"
              dense
              class="text-xs"
            >
              {{ props.row.users_count || 0 }} users
            </q-chip>
          </q-td>
        </template>

        <template v-slot:body-cell-actions="props">
          <q-td :props="props" class="q-gutter-sm">
            <q-btn
              flat
              round
              dense
              color="primary"
              icon="edit"
              @click="openRoleForm(props.row)"
            >
              <q-tooltip>Edit Role</q-tooltip>
            </q-btn>
            <q-btn
              flat
              round
              dense
              color="negative"
              icon="delete"
              @click="confirmDeleteRole(props.row)"
              :disable="props.row.users_count > 0"
            >
              <q-tooltip>Delete Role</q-tooltip>
            </q-btn>
          </q-td>
        </template>
      </q-table>
    </div>

    <!-- Role Form Dialog -->
    <q-dialog v-model="showRoleForm" persistent>
      <q-card class="column" style="width: 700px; max-width: 90vw">
        <q-card-section class="row items-center bg-primary text-white">
          <div class="text-h6">{{ editingRole ? 'Edit Role' : 'Create New Role' }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pa-lg col scroll">
          <q-form @submit="saveRole" class="q-gutter-md">
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-8">
                <q-input
                  v-model="roleForm.name"
                  label="Role Name"
                  outlined
                  :rules="[val => !!val || 'Role name is required']"
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input
                  v-model="roleForm.color"
                  label="Color"
                  outlined
                  type="color"
                  :rules="[val => !!val || 'Color is required']"
                />
              </div>
            </div>

            <q-input
              v-model="roleForm.description"
              label="Description"
              outlined
              type="textarea"
              :rules="[val => !!val || 'Description is required']"
            />

            <!-- Permissions Section -->
            <div class="q-mt-md">
              <div class="text-subtitle2 q-mb-sm">
                <div class="flex items-center justify-between">
                  <span class="text-weight-bold">Role Permissions</span>
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
                                v-model="roleForm.permissions"
                                :val="perm.value"
                                :label="perm.label"
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

            <!-- Actions -->
            <div class="row justify-end q-mt-lg">
              <q-btn
                flat
                color="primary"
                label="Cancel"
                v-close-popup
              />
              <q-btn
                :loading="saving"
                type="submit"
                color="primary"
                label="Save"
                class="q-ml-sm"
              />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Delete Confirmation Dialog -->
    <q-dialog v-model="showDeleteConfirm">
      <q-card>
        <q-card-section class="row items-center">
          <q-avatar icon="warning" color="negative" text-color="white" />
          <span class="q-ml-sm">Are you sure you want to delete this role?</span>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn
            flat
            label="Delete"
            color="negative"
            @click="deleteRole"
            :loading="deleting"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import { useApi } from '@/composables/useApi';

export default defineComponent({
  name: 'RoleManagement',

  setup() {
    const $q = useQuasar();
    const api = useApi();

    // Data
    const roles = ref([]);
    const permissionOptions = ref([]);
    const loading = ref(true);
    const saving = ref(false);
    const deleting = ref(false);
    const showRoleForm = ref(false);
    const showDeleteConfirm = ref(false);
    const editingRole = ref(null);

    const roleForm = ref({
      name: '',
      color: '#1976D2',
      description: '',
      permissions: []
    });

    // Table columns
    const columns = [
      {
        name: 'name',
        required: true,
        label: 'Role Name',
        align: 'left',
        field: 'name',
        sortable: true
      },
      {
        name: 'color',
        align: 'left',
        label: 'Color',
        field: 'color'
      },
      {
        name: 'description',
        align: 'left',
        label: 'Description',
        field: 'description'
      },
      {
        name: 'users',
        align: 'left',
        label: 'Users',
        field: 'users_count'
      },
      {
        name: 'actions',
        align: 'center',
        label: 'Actions',
        field: 'actions'
      }
    ];

    // Methods
    const fetchRoles = async () => {
      try {
        loading.value = true;
        const response = await api.get('/user-management/roles');
        roles.value = response.data;
      } catch (error) {
        $q.notify({
          color: 'negative',
          message: 'Failed to load roles'
        });
      } finally {
        loading.value = false;
      }
    };

    const fetchPermissions = async () => {
      try {
        const response = await api.get('/user-management/permissions');
        permissionOptions.value = response.data.map(permission => ({
          label: permission.name,
          value: permission.id,
          group: permission.group
        }));
      } catch (error) {
        $q.notify({
          color: 'negative',
          message: 'Failed to load permissions'
        });
      }
    };

    const openRoleForm = (role = null) => {
      editingRole.value = role;
      if (role) {
        roleForm.value = {
          name: role.name,
          color: role.color,
          description: role.description,
          permissions: role.permissions?.map(p => p.id) || []
        };
      } else {
        roleForm.value = {
          name: '',
          color: '#1976D2',
          description: '',
          permissions: []
        };
      }
      showRoleForm.value = true;
    };

    const saveRole = async () => {
      try {
        saving.value = true;
        const endpoint = editingRole.value
          ? `/user-management/roles/${editingRole.value.id}`
          : '/user-management/roles';
        const method = editingRole.value ? 'put' : 'post';

        await api[method](endpoint, roleForm.value);
        
        await fetchRoles();
        showRoleForm.value = false;
        
        $q.notify({
          color: 'positive',
          message: `Role ${editingRole.value ? 'updated' : 'created'} successfully`
        });
      } catch (error) {
        $q.notify({
          color: 'negative',
          message: `Failed to ${editingRole.value ? 'update' : 'create'} role`
        });
      } finally {
        saving.value = false;
      }
    };

    const confirmDeleteRole = (role) => {
      if (role.users_count > 0) {
        $q.notify({
          color: 'warning',
          message: 'Cannot delete a role that has users assigned to it'
        });
        return;
      }
      editingRole.value = role;
      showDeleteConfirm.value = true;
    };

    const deleteRole = async () => {
      try {
        deleting.value = true;
        await api.delete(`/user-management/roles/${editingRole.value.id}`);
        await fetchRoles();
        showDeleteConfirm.value = false;
        $q.notify({
          color: 'positive',
          message: 'Role deleted successfully'
        });
      } catch (error) {
        $q.notify({
          color: 'negative',
          message: 'Failed to delete role'
        });
      } finally {
        deleting.value = false;
      }
    };

    const toggleAllPermissions = () => {
      if (roleForm.value.permissions.length === permissionOptions.value.length) {
        roleForm.value.permissions = [];
      } else {
        roleForm.value.permissions = permissionOptions.value.map(p => p.value);
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
    onMounted(async () => {
      await Promise.all([
        fetchRoles(),
        fetchPermissions()
      ]);
    });

    return {
      // Data
      roles,
      columns,
      loading,
      saving,
      deleting,
      showRoleForm,
      showDeleteConfirm,
      roleForm,
      editingRole,
      permissionOptions,
      groupedPermissions,

      // Methods
      openRoleForm,
      saveRole,
      confirmDeleteRole,
      deleteRole,
      toggleAllPermissions,
      formatGroupName
    };
  }
});
</script>
