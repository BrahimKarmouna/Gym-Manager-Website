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
        <q-select v-model="statusFilter" :options="statusOptions" outlined dense />
        <q-select v-model="roleFilter" :options="roleOptions" outlined dense />
        <q-select v-model="gymFilter" :options="gymOptions" outlined dense />
      </div>
    </div>

    <!-- Assistants Table -->
    <q-card class="q-mb-md">
      <q-table
        :rows="filteredAssistants"
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
                <img :src="props.row.photo ? `/storage/${props.row.photo}` : '/img/default-avatar.png'" :alt="props.row.name">
              </q-avatar>
            </q-td>
            <q-td key="name" :props="props">{{ props.row.name }}</q-td>
            <q-td key="contact" :props="props">
              <div>{{ props.row.email }}</div>
              <div>{{ props.row.phone }}</div>
            </q-td>
            <q-td key="gym" :props="props">
              {{ props.row.gym ? props.row.gym.name : 'N/A' }}
            </q-td>
            <q-td key="role" :props="props">
              <q-badge :color="getRoleBadgeColor(props.row.role)" text-color="white" class="q-px-sm">
                {{ getRoleLabel(props.row.role) }}
              </q-badge>
            </q-td>
            <q-td key="status" :props="props">
              <q-badge :color="props.row.active ? 'green' : 'red'" text-color="white" class="q-px-sm">
                {{ props.row.active ? 'Active' : 'Inactive' }}
              </q-badge>
            </q-td>
            <q-td key="lastLogin" :props="props">
              {{ props.row.last_login ? formatDate(props.row.last_login) : 'Never' }}
            </q-td>
            <q-td key="actions" :props="props">
              <div class="flex gap-2">
                <q-btn flat round size="sm" color="primary" icon="edit" @click="editAssistant(props.row)">
                  <q-tooltip>Edit Assistant</q-tooltip>
                </q-btn>
                <q-btn flat round size="sm" color="secondary" icon="lock" @click="viewPermissions(props.row)">
                  <q-tooltip>Manage Permissions</q-tooltip>
                </q-btn>
                <q-btn flat round size="sm" color="negative" icon="delete" @click="confirmDelete(props.row)">
                  <q-tooltip>Delete Assistant</q-tooltip>
                </q-btn>
              </div>
            </q-td>
          </q-tr>
        </template>
        <template v-slot:no-data>
          <div class="text-center q-pa-xl">
            <q-icon name="person_off" size="3rem" color="grey" />
            <p class="text-grey-7">No assistants found</p>
            <q-btn color="primary" label="Add First Assistant" @click="showAddModal = true" class="q-mt-md" />
          </div>
        </template>
      </q-table>
    </q-card>
    
    <!-- Add/Edit Assistant Modal -->
    <q-dialog v-model="showAssistantDialog" persistent maximized-mobile>
      <q-card class="max-w-2xl w-full">
        <q-card-section class="flex items-center justify-between">
          <h2 class="text-h6">{{ showEditModal ? 'Edit Assistant' : 'Add New Assistant' }}</h2>
          <q-btn icon="close" flat round dense v-close-popup @click="closeModals" />
        </q-card-section>
        
        <q-separator />
        
        <q-card-section class="q-pa-md">
          <div class="flex flex-col md:flex-row gap-6">
            <!-- Photo upload -->
            <div class="flex flex-col items-center">
              <q-avatar size="100px" class="q-mb-sm">
                <img :src="assistantPhotoPreview || (formData.photo ? `/storage/${formData.photo}` : '/img/default-avatar.png')" />
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
              
              <q-input
                v-model="formData.phone"
                label="Phone"
                outlined
                dense
              />
              
              <q-select
                v-model="formData.role"
                :rules="[val => val && val.length > 0 || 'Please select a role']"
                :options="[
                  { label: 'Trainer', value: 'trainer' },
                  { label: 'Receptionist', value: 'receptionist' },
                  { label: 'Manager', value: 'manager' }
                ]"
                option-value="value"
                option-label="label"
                emit-value
                map-options
                label="Role *"
                @update:model-value="selectedRole = $event"
              />
              
              <!-- Replace the gym selection dropdown with a read-only display -->
              <div class="q-field q-field--outlined q-field--dense q-field--labeled q-field--no-input">
                <div class="q-field__inner relative-position">
                  <div class="q-field__control relative-position row no-wrap">
                    <div class="q-field__control-container col relative-position">
                      <div class="q-field__label">Gym</div>
                      <div class="q-field__native">{{ selectedGymName }}</div>
                    </div>
                  </div>
                </div>
              </div>
              
              <q-toggle
                v-model="formData.active"
                label="Active"
                color="green"
                class="col-span-2"
              />
              
              <q-input
                v-model="formData.notes"
                label="Notes"
                outlined
                type="textarea"
                class="col-span-2"
              />
            </div>
          </div>
        </q-card-section>
        
        <q-card-actions align="right" class="q-px-md q-pb-md">
          <q-btn flat label="Cancel" color="grey-7" @click="closeModals" />
          <q-btn 
            label="Save Assistant" 
            color="primary" 
            :loading="saving" 
            @click="saveAssistant" 
            :disable="!formData.name || !formData.email || !formData.role || (!showEditModal && !formData.password) || (formData.password && formData.password.length < 8)"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
    
    <!-- Permissions Modal -->
    <q-dialog v-model="showPermissionsModal" persistent maximized-mobile>
      <q-card class="max-w-3xl w-full">
        <q-card-section class="flex items-center justify-between">
          <h2 class="text-h6">Permissions - {{ selectedAssistant?.name }}</h2>
          <q-btn icon="close" flat round dense v-close-popup @click="closeModals" />
        </q-card-section>
        
        <q-separator />
        
        <q-card-section>
          <p class="text-grey-7 text-sm q-mb-md">
            Customize which features this assistant can access. 
            <q-chip color="secondary" text-color="white" dense>
              {{ getRoleLabel(selectedAssistant?.role) }} Role
            </q-chip>
          </p>
          
          <div class="q-mb-md">
            <q-list separator>
              <template v-for="(group, index) in permissionGroups" :key="index">
                <q-expansion-item
                  :label="formatResourceName(group.name)"
                  header-class="text-primary font-medium"
                  expand-icon-class="text-primary"
                  :default-opened="index === 0"
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
                        <q-checkbox
                          v-for="perm in group.permissions"
                          :key="perm.id"
                          v-model="perm.granted"
                          :label="formatPermissionName(perm.display)"
                          color="primary"
                        />
                      </div>
                    </q-card-section>
                  </q-card>
                </q-expansion-item>
              </template>
            </q-list>
          </div>
        </q-card-section>
        
        <q-card-actions align="right" class="q-px-md q-pb-md">
          <q-btn flat label="Cancel" color="grey-7" @click="closeModals" />
          <q-btn label="Save Permissions" color="primary" :loading="savingPermissions" @click="savePermissions" />
        </q-card-actions>
      </q-card>
    </q-dialog>
    
    <!-- Delete Confirmation -->
    <q-dialog v-model="showDeleteModal" persistent>
      <q-card class="max-w-md w-full">
        <q-card-section class="flex items-center">
          <q-avatar icon="warning" color="negative" text-color="white" />
          <span class="q-ml-sm text-h6">Delete Assistant</span>
        </q-card-section>
        
        <q-card-section>
          Are you sure you want to delete <strong>{{ selectedAssistant?.name }}</strong>? This action cannot be undone.
        </q-card-section>
        
        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey-7" v-close-popup />
          <q-btn label="Delete" color="negative" :loading="deleting" @click="deleteAssistant" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { date } from 'quasar';
import axios from 'axios';

export default {
  data() {
    return {
      // Assistant data
      assistants: [],
      gyms: [],
      loading: false,
      
      // Table configuration
      columns: [
        { name: 'photo', label: 'Photo', field: 'photo', align: 'center', sortable: false },
        { name: 'name', label: 'Name', field: 'name', align: 'left', sortable: true },
        { name: 'contact', label: 'Contact Info', field: 'email', align: 'left', sortable: true },
        { name: 'gym', label: 'Gym', field: row => row.gym?.name, align: 'left', sortable: true },
        { name: 'role', label: 'Role', field: 'role', align: 'left', sortable: true },
        { name: 'status', label: 'Status', field: 'active', align: 'center', sortable: true },
        { name: 'lastLogin', label: 'Last Login', field: 'last_login', align: 'left', sortable: true },
        { name: 'actions', label: 'Actions', field: 'actions', align: 'center', sortable: false }
      ],
      sorting: { field: 'name', direction: 'asc' },
      
      // Filters
      searchQuery: '',
      statusFilter: { label: 'All Status', value: 'all' },
      roleFilter: { label: 'All Roles', value: 'all' },
      gymFilter: { label: 'All Gyms', value: 'all' },
      
      // Filter options
      statusOptions: [
        { label: 'All Status', value: 'all' },
        { label: 'Active', value: 'active' },
        { label: 'Inactive', value: 'inactive' }
      ],
      roleOptions: [
        { label: 'All Roles', value: 'all' },
        { label: 'Trainer', value: 'trainer' },
        { label: 'Receptionist', value: 'receptionist' },
        { label: 'Manager', value: 'manager' }
      ],
      gymOptions: [{ label: 'All Gyms', value: 'all' }],
      
      // Form data
      formData: {
        name: '',
        email: '',
        password: '',
        phone: '',
        role: 'trainer',
        gym_id: null,
        active: true,
        notes: '',
        photo: null
      },
      photoFile: null,
      assistantPhotoPreview: null,
      
      // Permissions data
      permissionGroups: [],
      permissionsList: [],
      selectedPermissions: [],
      
      // Selected assistant
      selectedAssistant: null,
      
      // Modal states
      showAddModal: false,
      showEditModal: false,
      showPermissionsModal: false,
      showDeleteModal: false,
      
      // Loading states
      saving: false,
      savingPermissions: false,
      deleting: false,
      permissionsLoading: false,
      
      // Selected gym name for display
      selectedGymName: '',
      selectedRole: ''
    };
  },
  
  computed: {
    activeAssistants() {
      return this.assistants.filter(assistant => assistant.active);
    },
    inactiveAssistants() {
      return this.assistants.filter(assistant => !assistant.active);
    },
    filteredAssistants() {
      let filtered = [...this.assistants];
      
      // Filter by search query
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(assistant => 
          assistant.name.toLowerCase().includes(query) ||
          assistant.email.toLowerCase().includes(query) ||
          (assistant.phone && assistant.phone.toLowerCase().includes(query))
        );
      }
      
      // Filter by status
      if (this.statusFilter.value !== 'all') {
        filtered = filtered.filter(assistant => 
          (this.statusFilter.value === 'active' && assistant.active) ||
          (this.statusFilter.value === 'inactive' && !assistant.active)
        );
      }
      
      // Filter by role
      if (this.roleFilter.value !== 'all') {
        filtered = filtered.filter(assistant => 
          assistant.role === this.roleFilter.value
        );
      }
      
      // Filter by gym
      if (this.gymFilter.value !== 'all') {
        filtered = filtered.filter(assistant => 
          assistant.gym_id === this.gymFilter.value
        );
      }
      
      return filtered;
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
  
  created() {
    this.fetchAssistants();
    this.fetchGyms();
    
    // Debug logs for troubleshooting
    console.log('Component created');
  },
  
  mounted() {
    // Create a test gym if needed
    this.createTestGym();
  },
  
  methods: {
    async fetchAssistants() {
      this.loading = true;
      try {
        const response = await axios.get('/api/assistants');
        this.assistants = response.data;
      } catch (error) {
        console.error('Error fetching assistants:', error);
        this.$q.notify({
          color: 'negative',
          message: 'Failed to load assistants',
          icon: 'error'
        });
      } finally {
        this.loading = false;
      }
    },
    
    async fetchGyms() {
      try {
        // First, get the user's associated gyms
        const userGymsResponse = await axios.get('/api/gyms/user-gyms');
        const userGyms = userGymsResponse.data;
        
        // Then get all gyms for the dropdown
        const allGymsResponse = await axios.get('/api/gyms');
        this.gyms = allGymsResponse.data;
        
        // Update gym filter options
        this.gymOptions = [
          { label: 'All Gyms', value: 'all' },
          ...this.gyms.map(gym => ({
            label: gym.name,
            value: gym.id
          }))
        ];
        
        // If the user has associated gyms, set the first one as default
        if (userGyms.length > 0) {
          const defaultGymId = userGyms[0].id;
          const defaultGymName = userGyms[0].name;
          
          // Set the default gym for the form
          this.formData.gym_id = defaultGymId;
          this.selectedGymName = defaultGymName;
          
          // Also update the gym filter
          this.gymFilter = {
            label: defaultGymName,
            value: defaultGymId
          };
        } 
        // Fallback to the first gym in the list if no user gyms
        else if (this.gyms.length > 0) {
          this.formData.gym_id = this.gyms[0].id;
          this.selectedGymName = this.gyms[0].name;
        }
      } catch (error) {
        console.error('Error fetching gyms:', error);
      }
    },
    
    // Create a test gym for development purposes
    async createTestGym() {
      try {
        console.log('Checking if gyms exist...');
        // Check if we have any gyms first - using user-gyms endpoint which returns authenticated user's gyms
        const response = await axios.get('/api/gyms/user-gyms');
        console.log('User gyms response:', response.data);
        
        if (!response.data || response.data.length === 0) {
          console.log('No user gyms found, creating a test gym');
          
          // Create a test gym
          const createResponse = await axios.post('/api/gyms', {
            name: 'Main Gym',
            location: 'Downtown'
          });
          
          console.log('Test gym created:', createResponse.data);
          
          // Wait a moment and then refresh gyms
          setTimeout(() => {
            this.fetchGyms();
          }, 500);
          
          return createResponse.data;
        } else {
          console.log('User already has gyms:', response.data);
          return response.data[0];
        }
      } catch (error) {
        console.error('Error checking/creating gym:', error);
        return null;
      }
    },
    
    async saveAssistant() {
      try {
        this.saving = true;
        
        // Make sure gym_id exists
        if (this.gyms.length === 0) {
          throw new Error('No gyms available. Please create a gym first.');
        }
        
        // Create a data object for submission with proper types
        const data = new FormData();
        data.append('name', this.formData.name);
        data.append('email', this.formData.email);
        
        // Make sure role is valid based on backend validation
        const validRoles = ['trainer', 'receptionist', 'manager'];
        data.append('role', validRoles.includes(this.formData.role) ? this.formData.role : 'trainer');
        
        // Make sure gym_id exists and is a number
        const gymId = parseInt(this.formData.gym_id) || this.gyms[0].id;
        data.append('gym_id', gymId);
        
        // Ensure active is a proper boolean
        data.append('active', this.formData.active ? '1' : '0');
        
        // Add password only if it's provided (required for new, optional for edit)
        if (this.formData.password) {
          data.append('password', this.formData.password);
        } else if (!this.showEditModal) {
          // If creating new assistant and no password, set a default
          data.append('password', 'password123');
        }
        
        // Add optional fields if they exist
        if (this.formData.phone) data.append('phone', this.formData.phone);
        if (this.formData.notes) data.append('notes', this.formData.notes);
        
        // Add the photo file if it exists
        if (this.photoFile) {
          data.append('photo', this.photoFile);
        }
        
        // Debug output
        console.log('Submitting gym_id:', gymId);
        console.log('Available gyms:', this.gyms);
        
        let response;
        
        // Send request based on whether creating or editing
        if (this.showEditModal) {
          data.append('_method', 'PUT'); // Laravel will recognize this as PUT request
          response = await axios.post(`/api/assistants/${this.selectedAssistant.id}`, data);
        } else {
          response = await axios.post('/api/assistants', data);
        }
        
        // Refresh the assistants list
        this.fetchAssistants();
        
        // Close the modal
        this.closeModals();
        
        this.$q.notify({
          color: 'positive',
          message: 'Assistant saved successfully',
          icon: 'check_circle'
        });
      } catch (error) {
        console.error('Error saving assistant:', error);
        
        let errorMessage = 'Failed to save assistant';
        
        if (error.response && error.response.data && error.response.data.errors) {
          const errors = Object.values(error.response.data.errors).flat();
          errorMessage = errors.join(', ');
        }
        
        this.$q.notify({
          color: 'negative',
          message: errorMessage,
          icon: 'error'
        });
      } finally {
        this.saving = false;
      }
    },
    
    editAssistant(assistant) {
      this.selectedAssistant = assistant;
      this.showEditModal = true;
      
      // Ensure all fields are properly initialized
      this.formData = {
        name: assistant.name || '',
        email: assistant.email || '',
        password: '', // Don't populate password for security
        phone: assistant.phone || '',
        role: assistant.role || 'trainer',
        gym_id: assistant.gym_id || (this.gyms.length > 0 ? this.gyms[0].id : null),
        active: assistant.active === undefined ? true : Boolean(assistant.active),
        notes: assistant.notes || ''
      };
      
      if (assistant.photo) {
        this.assistantPhotoPreview = `/storage/${assistant.photo}`;
      } else {
        this.assistantPhotoPreview = null;
      }
      
      // Update selected gym name
      if (assistant.gym && assistant.gym.name) {
        this.selectedGymName = assistant.gym.name;
      } else if (this.gyms.length > 0) {
        const gym = this.gyms.find(g => g.id === assistant.gym_id);
        this.selectedGymName = gym ? gym.name : this.gyms[0].name;
      }
    },
    
    async viewPermissions(assistant) {
      this.selectedAssistant = assistant;
      this.permissionsLoading = true;
      
      try {
        // Fetch assistant permissions
        const response = await axios.get(`/api/assistants/${assistant.id}/permissions`);
        
        console.log('Permission response:', response.data);
        
        // Process permissions into a format suitable for display
        if (response.data && response.data.permissions) {
          this.permissionsList = response.data.permissions;
          
          // Group permissions by resource for display
          this.permissionGroups = this.groupPermissionsByResource(this.permissionsList);
          
          // Track which permissions are currently selected
          this.selectedPermissions = response.data.role_permissions || [];
        }
        
        // Show the permissions modal
        this.showPermissionsModal = true;
      } catch (error) {
        console.error('Error fetching permissions:', error);
        this.$q.notify({
          color: 'negative',
          message: 'Failed to load permissions',
          icon: 'error'
        });
      } finally {
        this.permissionsLoading = false;
      }
    },
    
    confirmDelete(assistant) {
      this.selectedAssistant = assistant;
      this.showDeleteModal = true;
    },
    
    handleFileUpload() {
      if (!this.photoFile) return;
      
      const reader = new FileReader();
      reader.onload = (e) => {
        this.assistantPhotoPreview = e.target.result;
      };
      reader.readAsDataURL(this.photoFile);
    },
    
    async savePermissions() {
      this.savingPermissions = true;
      
      try {
        // Send the selected permissions to the server using PUT method (as defined in the routes)
        await axios.put(`/api/assistants/${this.selectedAssistant.id}/permissions`, {
          permissions: this.selectedPermissions
        });
        
        // Show success message
        this.$q.notify({
          color: 'positive',
          message: 'Permissions saved successfully',
          icon: 'check_circle'
        });
        
        // Close the modal
        this.showPermissionsModal = false;
      } catch (error) {
        console.error('Error saving permissions:', error);
        
        // Show error message
        this.$q.notify({
          color: 'negative',
          message: 'Failed to save permissions',
          icon: 'error'
        });
      } finally {
        this.savingPermissions = false;
      }
    },
    
    async deleteAssistant() {
      this.deleting = true;
      
      try {
        await axios.delete(`/api/assistants/${this.selectedAssistant.id}`);
        
        // Show success message
        this.$q.notify({
          color: 'positive',
          message: 'Assistant deleted successfully',
          icon: 'check_circle'
        });
        
        // Remove from local list
        this.assistants = this.assistants.filter(a => a.id !== this.selectedAssistant.id);
        
        // Close the modal
        this.showDeleteModal = false;
      } catch (error) {
        console.error('Error deleting assistant:', error);
        
        // Show error message
        this.$q.notify({
          color: 'negative',
          message: 'Failed to delete assistant',
          icon: 'error'
        });
      } finally {
        this.deleting = false;
      }
    },
    
    closeModals() {
      this.showAddModal = false;
      this.showEditModal = false;
      this.showPermissionsModal = false;
      this.showDeleteModal = false;
      
      // Reset form data
      setTimeout(() => {
        this.selectedAssistant = null;
        this.photoFile = null;
        this.assistantPhotoPreview = null;
        
        // Reset form to default values but maintain the gym selection
        const currentGymId = this.formData.gym_id;
        const currentGymName = this.selectedGymName;
        
        this.formData = {
          name: '',
          email: '',
          password: '',
          phone: '',
          role: 'trainer',
          gym_id: currentGymId, // Maintain the gym selection
          active: true,
          notes: '',
          photo: null
        };
        
        // Maintain selected gym name
        this.selectedGymName = currentGymName;
      }, 300);
    },
    
    // Helper to group permissions by resource
    groupPermissionsByResource(permissions) {
      const groups = {};
      
      // Process each permission
      permissions.forEach(permission => {
        const parts = permission.name.split('-');
        const action = parts[0]; // view, manage, etc
        const resource = parts[1]; // dashboard, members, etc
        
        // Initialize the resource group if it doesn't exist
        if (!groups[resource]) {
          groups[resource] = {
            name: resource.charAt(0).toUpperCase() + resource.slice(1), // Capitalize
            permissions: []
          };
        }
        
        // Add this permission to the appropriate group
        groups[resource].permissions.push({
          id: permission.name,
          name: permission.name,
          action: action,
          display: `${action.charAt(0).toUpperCase() + action.slice(1)} ${resource}`, // Capitalize
          granted: this.selectedPermissions.includes(permission.name)
        });
      });
      
      // Convert to array and sort by resource name
      return Object.values(groups).sort((a, b) => a.name.localeCompare(b.name));
    },
    
    // Toggle a permission selection
    togglePermission(permission) {
      const index = this.selectedPermissions.indexOf(permission.name);
      
      if (index === -1) {
        // Add the permission
        this.selectedPermissions.push(permission.name);
      } else {
        // Remove the permission
        this.selectedPermissions.splice(index, 1);
      }
      
      // Update the permission's granted status
      permission.granted = !permission.granted;
    },
    
    // Helper methods
    formatDate(dateString) {
      return date.formatDate(dateString, 'MMM D, YYYY HH:mm');
    },
    
    getRoleLabel(role) {
      switch (role) {
        case 'trainer': return 'Trainer';
        case 'receptionist': return 'Receptionist';
        case 'manager': return 'Manager';
        default: return role;
      }
    },
    
    getRoleBadgeColor(role) {
      switch (role) {
        case 'trainer': return 'blue';
        case 'receptionist': return 'purple';
        case 'manager': return 'orange';
        default: return 'grey';
      }
    },
    
    formatResourceName(resource) {
      // Capitalize first letter
      return resource.charAt(0).toUpperCase() + resource.slice(1);
    },
    
    formatPermissionName(name) {
      // Convert e.g. "view-members" to "View Members"
      const parts = name.split('-');
      return parts.map(part => part.charAt(0).toUpperCase() + part.slice(1)).join(' ');
    },
    
    isAllGroupSelected(group) {
      return group.permissions.every(perm => perm.granted);
    },
    
    toggleGroupPermissions(group) {
      const allSelected = this.isAllGroupSelected(group);
      group.permissions.forEach(perm => {
        perm.granted = !allSelected;
      });
    }
  }
};
</script>

<style scoped>
.q-table__card {
  box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
  transition: all 0.3s cubic-bezier(.25,.8,.25,1);
}

.q-table__card:hover {
  box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
}
</style>
