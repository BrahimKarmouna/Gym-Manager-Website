<template>
  <q-page class="q-pa-md">
    <div class="flex justify-between items-center q-mb-lg">
      <h1 class="text-2xl text-gray-800 font-bold">Assistant Management</h1>
      <q-btn color="primary" icon="add" label="Add New Assistant" @click="showAddModal = true" />
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 q-mb-md">
      <q-card class="text-center">
        <q-card-section>
          <div class="text-3xl font-bold text-primary">{{ assistants.length }}</div>
          <div class="text-sm text-gray-600">Total Assistants</div>
        </q-card-section>
      </q-card>
      <q-card class="text-center">
        <q-card-section>
          <div class="text-3xl font-bold text-primary">{{ activeAssistants.length }}</div>
          <div class="text-sm text-gray-600">Active</div>
        </q-card-section>
      </q-card>
      <q-card class="text-center">
        <q-card-section>
          <div class="text-3xl font-bold text-primary">{{ inactiveAssistants.length }}</div>
          <div class="text-sm text-gray-600">Inactive</div>
        </q-card-section>
      </q-card>
    </div>
    
    <!-- Search and Filter -->
    <div class="flex flex-col sm:flex-row gap-4 q-mb-md">
      <q-input v-model="searchQuery" placeholder="Search assistants..." dense outlined class="flex-1" clearable>
        <template v-slot:prepend>
          <q-icon name="search" />
        </template>
      </q-input>
      <div class="flex gap-4">
        <q-select v-model="statusFilter" :options="[
          {label: 'All Status', value: 'all'},
          {label: 'Active', value: 'active'},
          {label: 'Inactive', value: 'inactive'}
        ]" outlined dense />
        <q-select v-model="roleFilter" :options="[
          {label: 'All Roles', value: 'all'},
          {label: 'Trainer', value: 'trainer'},
          {label: 'Receptionist', value: 'receptionist'},
          {label: 'Manager', value: 'manager'}
        ]" outlined dense />
      </div>
    </div>

    <!-- Assistants Table -->
    <q-card class="q-mb-md">
      <q-table
        :rows="filteredAssistants"
        :columns="columns"
        row-key="id"
        :pagination="{ rowsPerPage: 0 }"
        :loading="false"
        v-model:sort="sorting"
      >
        <template v-slot:body="props">
          <q-tr :props="props">
            <q-td key="photo" :props="props">
              <q-avatar size="md">
                <img :src="props.row.photo || 'img/default-avatar.png'" :alt="props.row.name">
              </q-avatar>
            </q-td>
            <q-td key="name" :props="props">{{ props.row.name }}</q-td>
            <q-td key="contact" :props="props">
              <div>{{ props.row.email }}</div>
              <div>{{ props.row.phone }}</div>
            </q-td>
            <q-td key="role" :props="props">
              <q-badge color="primary" text-color="white" class="q-px-sm">
                {{ props.row.role }}
              </q-badge>
            </q-td>
            <q-td key="status" :props="props">
              <q-badge :color="props.row.active ? 'green' : 'red'" text-color="white" class="q-px-sm">
                {{ props.row.active ? 'Active' : 'Inactive' }}
              </q-badge>
            </q-td>
            <q-td key="actions" :props="props">
              <div class="flex gap-2">
                <q-btn flat round size="sm" color="primary" icon="edit" @click="editAssistant(props.row)" />
                <q-btn flat round size="sm" color="primary" icon="lock" @click="viewPermissions(props.row)" />
                <q-btn flat round size="sm" color="negative" icon="delete" @click="confirmDelete(props.row)" />
              </div>
            </q-td>
          </q-tr>
        </template>
        <template v-slot:no-data>
          <div class="text-center q-pa-xl">
            <q-icon name="person_off" size="3rem" color="grey" />
            <p class="text-grey-7">No assistants found</p>
          </div>
        </template>
      </q-table>
    </q-card>
    
    <!-- Pagination -->
    <div class="flex justify-center q-mb-lg">
      <q-pagination
        v-model="currentPage"
        :max="totalPages"
        :max-pages="5"
        boundary-links
      />
    </div>

    <!-- Add/Edit Assistant Modal -->
    <q-dialog v-model="showAssistantDialog" persistent maximized-mobile>
      <q-card class="max-w-2xl w-full">
        <q-card-section class="flex items-center justify-between">
          <h2 class="text-h6">{{ showEditModal ? 'Edit Assistant' : 'Add New Assistant' }}</h2>
          <q-btn icon="close" flat round dense v-close-popup @click="closeModals" />
        </q-card-section>
        
        <q-separator />
        
        <q-card-section class="q-pa-md">
          <q-form @submit="saveAssistant">
            <div class="flex justify-center q-mb-md">
              <q-avatar size="100px" class="cursor-pointer shadow-1">
                <img :src="formData.photo || 'img/default-avatar.png'" />
                <q-file v-model="photoFile" @input="handleFileUpload" accept=".jpg, .png, .jpeg" class="hidden">
                  <q-tooltip>Upload photo</q-tooltip>
                  <template v-slot:append>
                    <q-icon name="add_a_photo" @click.stop />
                  </template>
                </q-file>
              </q-avatar>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <q-input v-model="formData.name" label="Full Name" outlined dense :rules="[val => !!val || 'Name is required']" />
              <q-input v-model="formData.email" label="Email" outlined dense :rules="[val => !!val || 'Email is required', val => /^\S+@\S+\.\S+$/.test(val) || 'Invalid email format']" />
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 q-mt-md">
              <q-input v-model="formData.phone" label="Phone" outlined dense />
              <q-select v-model="formData.role" label="Role" outlined dense :options="[
                {label: 'Trainer', value: 'trainer'},
                {label: 'Receptionist', value: 'receptionist'},
                {label: 'Manager', value: 'manager'}
              ]" :rules="[val => !!val || 'Role is required']" />
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 q-mt-md">
              <q-input v-model="formData.username" label="Username" outlined dense :rules="[val => !!val || 'Username is required']" />
              <q-input v-model="formData.password" label="Password" outlined dense type="password" :rules="[val => showEditModal || !!val || 'Password is required']" />
            </div>
            
            <div class="q-mt-md">
              <q-toggle v-model="formData.active" label="Active" color="primary" />
            </div>
            
            <div class="flex justify-end gap-4 q-mt-lg">
              <q-btn flat label="Cancel" color="grey" v-close-popup @click="closeModals" />
              <q-btn type="submit" color="primary" label="Save" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Permissions Modal -->
    <q-dialog v-model="showPermissionModal" persistent maximized-mobile>
      <q-card class="max-w-3xl w-full">
        <q-card-section class="flex items-center justify-between">
          <h2 class="text-h6">Permissions - {{ selectedAssistant?.name }}</h2>
          <q-btn icon="close" flat round dense v-close-popup @click="closeModals" />
        </q-card-section>
        
        <q-separator />
        
        <q-card-section class="q-pa-md">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="text-subtitle1 text-primary q-mb-sm">Members</h3>
              <q-list dense>
                <q-item tag="label">
                  <q-item-section avatar>
                    <q-checkbox v-model="permissions.viewMembers" />
                  </q-item-section>
                  <q-item-section>View Members</q-item-section>
                </q-item>
                <q-item tag="label">
                  <q-item-section avatar>
                    <q-checkbox v-model="permissions.editMembers" />
                  </q-item-section>
                  <q-item-section>Add/Edit Members</q-item-section>
                </q-item>
                <q-item tag="label">
                  <q-item-section avatar>
                    <q-checkbox v-model="permissions.deleteMembers" />
                  </q-item-section>
                  <q-item-section>Delete Members</q-item-section>
                </q-item>
              </q-list>
            </div>
            
            <div>
              <h3 class="text-subtitle1 text-primary q-mb-sm">Classes</h3>
              <q-list dense>
                <q-item tag="label">
                  <q-item-section avatar>
                    <q-checkbox v-model="permissions.viewClasses" />
                  </q-item-section>
                  <q-item-section>View Classes</q-item-section>
                </q-item>
                <q-item tag="label">
                  <q-item-section avatar>
                    <q-checkbox v-model="permissions.manageClasses" />
                  </q-item-section>
                  <q-item-section>Manage Classes</q-item-section>
                </q-item>
              </q-list>
            </div>
            
            <div>
              <h3 class="text-subtitle1 text-primary q-mb-sm">Payments</h3>
              <q-list dense>
                <q-item tag="label">
                  <q-item-section avatar>
                    <q-checkbox v-model="permissions.viewPayments" />
                  </q-item-section>
                  <q-item-section>View Payments</q-item-section>
                </q-item>
                <q-item tag="label">
                  <q-item-section avatar>
                    <q-checkbox v-model="permissions.processPayments" />
                  </q-item-section>
                  <q-item-section>Process Payments</q-item-section>
                </q-item>
              </q-list>
            </div>
            
            <div>
              <h3 class="text-subtitle1 text-primary q-mb-sm">Reports</h3>
              <q-list dense>
                <q-item tag="label">
                  <q-item-section avatar>
                    <q-checkbox v-model="permissions.viewReports" />
                  </q-item-section>
                  <q-item-section>View Reports</q-item-section>
                </q-item>
              </q-list>
            </div>
          </div>
          
          <div class="flex justify-end gap-4 q-mt-lg">
            <q-btn flat label="Cancel" color="grey" v-close-popup @click="closeModals" />
            <q-btn color="primary" label="Save Permissions" @click="savePermissions" />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
    
    <!-- Delete Confirmation Modal -->
    <q-dialog v-model="showDeleteModal" persistent>
      <q-card class="max-w-sm w-full">
        <q-card-section class="text-center">
          <q-icon name="warning" color="negative" size="3rem" />
          <p class="text-h6 q-mt-sm">Confirm Delete</p>
          <p class="q-mt-sm">Are you sure you want to delete <strong>{{ selectedAssistant?.name }}</strong>?</p>
          <p class="text-negative">This action cannot be undone.</p>
        </q-card-section>
        
        <q-card-actions align="right" class="q-pa-md">
          <q-btn flat label="Cancel" color="grey" v-close-popup @click="closeModals" />
          <q-btn color="negative" label="Delete" @click="deleteAssistant" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      assistants: [
        { 
          id: 1,
          name: 'John Doe',
          email: 'john@example.com',
          phone: '555-123-4567',
          photo: null,
          role: 'trainer',
          username: 'johndoe',
          active: true
        },
        {
          id: 2,
          name: 'Jane Smith',
          email: 'jane@example.com',
          phone: '555-987-6543',
          photo: null,
          role: 'manager',
          username: 'janesmith',
          active: true
        },
        {
          id: 3,
          name: 'Mike Johnson',
          email: 'mike@example.com',
          phone: '555-456-7890',
          photo: null,
          role: 'receptionist',
          username: 'mikejohnson',
          active: false
        }
      ],
      columns: [
        { name: 'photo', label: 'Photo', field: 'photo', align: 'left', sortable: false },
        { name: 'name', label: 'Name', field: 'name', align: 'left', sortable: true },
        { name: 'contact', label: 'Contact', field: 'contact', align: 'left', sortable: false },
        { name: 'role', label: 'Role', field: 'role', align: 'left', sortable: true },
        { name: 'status', label: 'Status', field: 'status', align: 'left', sortable: true },
        { name: 'actions', label: 'Actions', field: 'actions', align: 'right', sortable: false }
      ],
      sorting: {
        column: 'name',
        direction: 'asc'
      },
      searchQuery: '',
      statusFilter: 'all',
      roleFilter: 'all',
      currentPage: 1,
      pageSize: 10,
      
      // Form data
      formData: {
        id: null,
        name: '',
        email: '',
        phone: '',
        photo: null,
        role: 'trainer',
        username: '',
        password: '',
        active: true
      },
      photoFile: null,
      
      // Permissions
      permissions: {
        viewMembers: false,
        editMembers: false,
        deleteMembers: false,
        viewClasses: false,
        manageClasses: false,
        viewPayments: false,
        processPayments: false,
        viewReports: false
      },
      
      // Modal states
      showAddModal: false,
      showEditModal: false,
      showPermissionModal: false,
      showDeleteModal: false,
      selectedAssistant: null
    }
  },
  
  computed: {
    activeAssistants() {
      return this.assistants.filter(assistant => assistant.active);
    },
    inactiveAssistants() {
      return this.assistants.filter(assistant => !assistant.active);
    },
    filteredAssistants() {
      let result = [...this.assistants];
      
      // Apply search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        result = result.filter(assistant => 
          assistant.name.toLowerCase().includes(query) || 
          assistant.email.toLowerCase().includes(query) ||
          assistant.phone.includes(query)
        );
      }
      
      // Apply status filter
      if (this.statusFilter !== 'all') {
        const isActive = this.statusFilter === 'active';
        result = result.filter(assistant => assistant.active === isActive);
      }
      
      // Apply role filter
      if (this.roleFilter !== 'all') {
        result = result.filter(assistant => assistant.role === this.roleFilter);
      }
      
      return result;
    },
    totalPages() {
      return Math.ceil(this.filteredAssistants.length / this.pageSize);
    },
    showAssistantDialog: {
      get() {
        return this.showAddModal || this.showEditModal;
      },
      set(value) {
        if (!value) {
          this.showAddModal = false;
          this.showEditModal = false;
        }
      }
    }
  },
  
  methods: {
    editAssistant(assistant) {
      this.formData = { ...assistant };
      this.formData.password = ''; // Don't show existing password
      this.selectedAssistant = assistant;
      this.showEditModal = true;
    },
    
    viewPermissions(assistant) {
      this.selectedAssistant = assistant;
      // In a real app, you would fetch permissions for this assistant
      // For now, just set some defaults based on role
      if (assistant.role === 'manager') {
        this.permissions = {
          viewMembers: true,
          editMembers: true,
          deleteMembers: true,
          viewClasses: true,
          manageClasses: true,
          viewPayments: true,
          processPayments: true,
          viewReports: true
        };
      } else if (assistant.role === 'trainer') {
        this.permissions = {
          viewMembers: true,
          editMembers: false,
          deleteMembers: false,
          viewClasses: true,
          manageClasses: true,
          viewPayments: false,
          processPayments: false,
          viewReports: false
        };
      } else {
        this.permissions = {
          viewMembers: true,
          editMembers: true,
          deleteMembers: false,
          viewClasses: true,
          manageClasses: false,
          viewPayments: true,
          processPayments: true,
          viewReports: false
        };
      }
      this.showPermissionModal = true;
    },
    
    confirmDelete(assistant) {
      this.selectedAssistant = assistant;
      this.showDeleteModal = true;
    },
    
    handleFileUpload() {
      if (this.photoFile) {
        // In a real app, you would upload the file to a server
        // For now, just create a URL for preview
        this.formData.photo = URL.createObjectURL(this.photoFile);
      }
    },
    
    saveAssistant() {
      if (this.showEditModal) {
        // Update existing assistant
        const index = this.assistants.findIndex(a => a.id === this.formData.id);
        if (index !== -1) {
          // If password is empty, don't update it
          if (!this.formData.password) {
            const { password, ...dataWithoutPassword } = this.formData;
            this.assistants[index] = { ...this.assistants[index], ...dataWithoutPassword };
          } else {
            this.assistants[index] = { ...this.formData };
          }
        }
      } else {
        // Add new assistant
        const newId = Math.max(0, ...this.assistants.map(a => a.id)) + 1;
        this.assistants.push({
          ...this.formData,
          id: newId
        });
      }
      
      this.closeModals();
    },
    
    savePermissions() {
      // In a real app, you would send permissions to the server
      this.$q.notify({
        type: 'positive',
        message: 'Permissions saved successfully!'
      });
      this.closeModals();
    },
    
    deleteAssistant() {
      const index = this.assistants.findIndex(a => a.id === this.selectedAssistant.id);
      if (index !== -1) {
        this.assistants.splice(index, 1);
        this.$q.notify({
          type: 'positive',
          message: 'Assistant deleted successfully!'
        });
      }
      this.closeModals();
    },
    
    closeModals() {
      this.showAddModal = false;
      this.showEditModal = false;
      this.showPermissionModal = false;
      this.showDeleteModal = false;
      this.selectedAssistant = null;
      this.formData = {
        id: null,
        name: '',
        email: '',
        phone: '',
        photo: null,
        role: 'trainer',
        username: '',
        password: '',
        active: true
      };
      this.photoFile = null;
    }
  }
}
</script>
