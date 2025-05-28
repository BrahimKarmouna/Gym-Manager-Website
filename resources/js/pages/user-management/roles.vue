<script>
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import axios from 'axios';

export default defineComponent({
  name: 'RoleManagement',

  setup() {
    const $q = useQuasar();

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
      { name: 'name', required: true, label: 'Role Name', align: 'left', field: 'name', sortable: true },
      { name: 'color', align: 'left', label: 'Color', field: 'color' },
      { name: 'description', align: 'left', label: 'Description', field: 'description' },
      { name: 'users', align: 'left', label: 'Users', field: 'users_count' },
      { name: 'actions', align: 'center', label: 'Actions', field: 'actions' }
    ];

    // API Calls
    const fetchRoles = async () => {
      try {
        loading.value = true;
        const response = await axios.get('/user-management/roles');
        roles.value = response.data;
      } catch (error) {
        $q.notify({ color: 'negative', message: 'Failed to load roles' });
      } finally {
        loading.value = false;
      }
    };

    const fetchPermissions = async () => {
      try {
        const response = await axios.get('/user-management/permissions');
        permissionOptions.value = response.data.map(permission => ({
          label: permission.name,
          value: permission.id,
          group: permission.group
        }));
      } catch (error) {
        $q.notify({ color: 'negative', message: 'Failed to load permissions' });
      }
    };

    const saveRole = async () => {
      try {
        saving.value = true;
        const endpoint = editingRole.value
          ? `/user-management/roles/${editingRole.value.id}`
          : '/user-management/roles';
        const method = editingRole.value ? 'put' : 'post';

        await axios({ method, url: endpoint, data: roleForm.value });
        await fetchRoles();
        showRoleForm.value = false;

        $q.notify({ color: 'positive', message: `Role ${editingRole.value ? 'updated' : 'created'} successfully` });
      } catch (error) {
        $q.notify({ color: 'negative', message: `Failed to ${editingRole.value ? 'update' : 'create'} role` });
      } finally {
        saving.value = false;
      }
    };

    const deleteRole = async () => {
      try {
        deleting.value = true;
        await axios.delete(`/user-management/roles/${editingRole.value.id}`);
        await fetchRoles();
        showDeleteConfirm.value = false;
        $q.notify({ color: 'positive', message: 'Role deleted successfully' });
      } catch (error) {
        $q.notify({ color: 'negative', message: 'Failed to delete role' });
      } finally {
        deleting.value = false;
      }
    };

    // UI Methods
    const openRoleForm = (role = null) => {
      editingRole.value = role;
      roleForm.value = role
        ? { ...role, permissions: role.permissions?.map(p => p.id) || [] }
        : { name: '', color: '#1976D2', description: '', permissions: [] };
      showRoleForm.value = true;
    };

    const confirmDeleteRole = (role) => {
      if (role.users_count > 0) {
        $q.notify({ color: 'warning', message: 'Cannot delete a role with users assigned' });
        return;
      }
      editingRole.value = role;
      showDeleteConfirm.value = true;
    };

    const toggleAllPermissions = () => {
      roleForm.value.permissions = roleForm.value.permissions.length === permissionOptions.value.length
        ? []
        : permissionOptions.value.map(p => p.value);
    };

    const groupedPermissions = computed(() => {
      return permissionOptions.value.reduce((groups, permission) => {
        if (!groups[permission.group]) groups[permission.group] = [];
        groups[permission.group].push(permission);
        return groups;
      }, {});
    });

    const formatGroupName = (groupName) => groupName.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());

    onMounted(() => {
      fetchRoles();
      fetchPermissions();
    });

    return {
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
