<script setup lang="ts">
import type { UserProperties } from '@db/apps/users/types'
import { onMounted, ref } from 'vue'
import AddNewUserDrawer from '@/views/apps/user/list/AddNewUserDrawer.vue'
import DeleteSuccessPopup from '@/views/apps/user/list/DeleteSuccessPopup.vue'
import RegistrationSuccessPopup from '@/views/apps/user/list/RegistrationSuccessPopup.vue'

const emit = defineEmits<{
  (e: 'api-error', value: Record<string, string[]>): void
}>()

definePage({
  meta: {
    action: 'read',
    subject: 'Users',
  },
})

onMounted(() => {
  fetchStats()
})

// 👉 - Popup success registration
const isRegistrationSuccessDialogVisible = ref(false)

// 👉 - Popup success delete
const isDeleteSuccessDialogVisible = ref(false)

// Statistics
const widgetData = ref([])

// 👉 Store
const searchQuery = ref('')
const selectedRole = ref()
const selectedStatus = ref()

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()
const selectedRows = ref([])

// Update data table options
const updateOptions = (options: any) => {
  page.value = options.page
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
}

// Headers
const headers = [
  { title: 'User', key: 'user' },
  { title: 'Email', key: 'email' },
  { title: 'Role', key: 'role' },
  { title: 'Status', key: 'status' },
  { title: 'Actions', key: 'actions', sortable: false },
]

// 👉 Fetching users
const { data: usersData, execute: fetchUsers } = await useApi<any>(createUrl('/apps/users', {
  query: {
    name: searchQuery,
    status: selectedStatus,
    role: selectedRole,
    itemsPerPage,
    page,
    sortBy,
    orderBy,
  },
}))

const users = computed((): UserProperties[] => usersData.value.users)
const totalUsers = computed(() => usersData.value.totalUsers)

// 👉 search filters
const { data: rolesData, execute: fetchRoles } = await useApi<any>(createUrl('/apps/roles/list', {}))

const roles = computed(() => rolesData.value.map(role => ({
  title: role.name,
  value: role.name,
})))

const status = [
  { title: 'Pending', value: 'Pending' },
  { title: 'Active', value: 'Active' },
  { title: 'Inactive', value: 'Inactive' },
]

const resolveUserRoleVariant = (role: string) => {
  const roleLowerCase = role.toLowerCase()

  if (roleLowerCase === 'subscriber')
    return { color: 'success', icon: 'ri-user-line' }
  if (roleLowerCase === 'author')
    return { color: 'error', icon: 'ri-computer-line' }
  if (roleLowerCase === 'maintainer')
    return { color: 'info', icon: 'ri-pie-chart-line' }
  if (roleLowerCase === 'editor')
    return { color: 'warning', icon: 'ri-edit-box-line' }
  if (roleLowerCase === 'admin')
    return { color: 'primary', icon: 'ri-vip-crown-line' }

  return { color: 'success', icon: 'ri-user-line' }
}

const resolveUserStatusVariant = (stat: string) => {
  const statLowerCase = stat.toLowerCase()
  if (statLowerCase === 'pending')
    return 'warning'
  if (statLowerCase === 'active')
    return 'success'
  if (statLowerCase === 'inactive')
    return 'secondary'

  return 'primary'
}

// 👉 Add new user drawer
const isAddNewUserDrawerVisible = ref(false)
const apiErrors = ref<Record<string, string[]>>({})

const dialogUserData = ref<UserProperties>({
  fullName: '',
  role: '',
  contact: '',
  email: '',
  status: '',
  password: '',
  avatar: '',
})

const addNewUser = async (userData: UserProperties) => {
  try {
    await $api('/apps/users', {
      method: 'POST',
      body: userData,
    })

    // Stocam datele despre userul nou creat pentru a le afisa in dialog
    dialogUserData.value = userData

    // Resetați erorile API
    apiErrors.value = {}
    isAddNewUserDrawerVisible.value = false

    // Afișați dialogul de succes
    isRegistrationSuccessDialogVisible.value = true

    // Reîncărcați lista de utilizatori după adăugarea cu succes a unui utilizator nou
    fetchUsers()
    fetchStats()
  }
  catch (error) {
    if (error.response) {
    // Verificăm dacă avem un răspuns de la API
      if (error.response.status === 422) {
      // Verificăm dacă avem erori de validare (status code 422)
        const errors = error.response._data?.errors
        if (apiErrors.value) {
        // Emitem evenimentul 'api-error' cu erorile primite către componenta AddNewUserDrawer
          apiErrors.value = errors
        }
      }
      else {
      // Pentru alte tipuri de erori, afișăm mesajul de eroare
        console.error('Error message:', error.response._data?.message)
      }
    }
    else {
    // Dacă nu avem un răspuns de la API, afișăm mesajul de eroare general
      console.error('Error:', error.message)
    }
  }
}

// 👉 Delete user
const deleteUser = async (id: number) => {
  try {
    await $api(`/apps/users/${id}`, {
      method: 'DELETE',
    })

    // Delete from selectedRows
    const index = selectedRows.value.findIndex(row => row === id)
    if (index !== -1)
      selectedRows.value.splice(index, 1)

    // Refetch Users
    // TODO: Make this async
    fetchUsers()
    fetchStats()

    // Set isDeleteSucessfulPopup to true after successful deletion
    isDeleteSuccessDialogVisible.value = true
  }
  catch (error) {
    // Handle error if needed
    console.error('Error deleting user:', error)
  }
}

async function fetchStats() {
  try {
    const response = await $api('/apps/stats', {
      method: 'GET',
    })

    const stats = response

    widgetData.value = [
      { title: 'Users', value: stats.totalUsers, desc: 'Total Users', icon: 'ri-group-line', iconColor: 'primary' },
      { title: 'Admins', value: stats.totalAdmins, desc: 'Total admins', icon: 'ri-user-add-line', iconColor: 'warning' },
      { title: 'Active Users', value: stats.totalActiveUsers, desc: 'Total active Users', icon: 'ri-user-follow-line', iconColor: 'success', tooltip: 'Users who have been active in last 30 days' },
      { title: 'Inactive Users', value: stats.totalInactiveUsers, desc: 'Total inactive Users', icon: 'ri-user-search-line', iconColor: 'error', tooltip: 'Users who have been inactive for more than 30 days' },
    ]
  }
  catch (error) {
    console.error('Error fetching stats:', error)
  }
}

const createUsers = {
  action: 'create' as const,
  subject: 'Users' as const,
}

const deleteUsers = {
  action: 'delete' as const,
  subject: 'Users' as const,
}
</script>

<template>
  <section>
    <!-- 👉 Widgets -->
    <div class="d-flex mb-6">
      <VRow>
        <template
          v-for="(data, id) in widgetData"
          :key="id"
        >
          <VCol
            cols="12"
            md="3"
            sm="6"
          >
            <VCard>
              <VCardText>
                <div class="d-flex justify-space-between">
                  <div class="d-flex flex-column gap-y-1">
                    <span class="text-base text-high-emphasis">{{ data.title }}</span>
                    <h4 class="text-h4 d-flex align-center gap-2">
                      {{ data.value }}
                      <span
                        class="text-base font-weight-regular"
                        :class="data.change > 0 ? 'text-success' : 'text-error'"
                      >{{ prefixWithPlus(data.change) }}</span>
                    </h4>

                    <p class="text-sm mb-0">
                      {{ data.desc }}
                    </p>
                  </div>
                  <VAvatar
                    :color="data.iconColor"
                    variant="tonal"
                    rounded
                    size="42"
                  >
                    <VIcon
                      :icon="data.icon"
                      size="26"
                    />
                  </VAvatar>
                  <VTooltip
                    activator="parent"
                    location="bottom"
                    open-delay="1000"
                  >
                    <span>{{ data.tooltip }}</span>
                  </VTooltip>
                </div>
              </VCardText>
            </VCard>
          </VCol>
        </template>
      </VRow>
    </div>

    <VCard class="mb-6">
      <VCardItem class="pb-4">
        <VCardTitle>Filters</VCardTitle>
      </VCardItem>
      <VDivider />

      <VCardText>
        <VRow align="center">
          <!-- 👉 Select Role -->
          <VCol
            cols="12"
            sm="2"
          >
            <VSelect
              v-model="selectedRole"
              label="Select Role"
              placeholder="Select Role"
              :items="roles"
              clearable
              clear-icon="ri-close-line"
            />
          </VCol>

          <!-- 👉 Select Status -->
          <VCol
            cols="12"
            sm="2"
          >
            <VSelect
              v-model="selectedStatus"
              label="Select Status"
              placeholder="Select Status"
              :items="status"
              clearable
              clear-icon="ri-close-line"
            />
          </VCol>
          <VCol
            cols="12"
            sm="8"
            class="d-flex align-center justify-end gap-4"
          >
            <!-- 👉 Search -->
            <div class="app-user-search-filter">
              <VTextField
                v-model="searchQuery"
                placeholder="Search User"
                density="compact"
              />
            </div>
            <!-- 👉 Add user button -->
            <VBtn
              v-if="$can(createUsers.action, createUsers.subject)"
              @click="isAddNewUserDrawerVisible = true"
            >
              Add New User
            </VBtn>
          </VCol>
        </VRow>
      </VCardText>

      <!-- SECTION datatable -->
      <VDataTableServer
        v-model:model-value="selectedRows"
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :items="users"
        item-value="id"
        :items-length="totalUsers"
        :headers="headers"
        show-select
        class="text-no-wrap rounded-0"
        @update:options="updateOptions"
      >
        <!-- User -->
        <template #item.user="{ item }">
          <div class="d-flex align-center">
            <VAvatar
              size="34"
              :variant="!item.avatar ? 'tonal' : undefined"
              :color="!item.avatar ? resolveUserRoleVariant(item.role).color : undefined"
              class="me-3"
            >
              <VImg
                v-if="item.avatar"
                :src="item.avatar"
              />
              <span v-else>{{ avatarText(item.fullName) }}</span>
            </VAvatar>

            <div class="d-flex flex-column">
              <RouterLink
                :to="{ name: 'apps-user-view-id', params: { id: item.id } }"
                class="text-link text-base font-weight-medium"
              >
                {{ item.fullName }}
              </RouterLink>

              <span class="text-sm text-medium-emphasis">{{ item.phone }}</span>
            </div>
          </div>
        </template>
        <!-- Role -->
        <template #item.role="{ item }">
          <div class="d-flex gap-2">
            <VIcon
              :icon="resolveUserRoleVariant(item.role).icon"
              :color="resolveUserRoleVariant(item.role).color"
              size="22"
            />
            <span class="text-capitalize text-high-emphasis">{{ item.role }}</span>
          </div>
        </template>
        <!-- Phone -->
        <template #item.plan="{ item }">
          <span class="text-capitalize text-high-emphasis">{{ item.phone }}</span>
        </template>
        <!-- Status -->
        <template #item.status="{ item }">
          <VChip
            :color="resolveUserStatusVariant(item.status)"
            size="small"
            class="text-capitalize"
          >
            {{ item.status }}
          </VChip>
        </template>

        <!-- Actions -->
        <template #item.actions="{ item }">
          <IconBtn
            v-if="$can(deleteUsers.action, deleteUsers.subject)"
            size="small"
            @click="deleteUser(item.id)"
          >
            <VIcon icon="ri-delete-bin-7-line" />
          </IconBtn>

          <IconBtn
            size="small"
            :to="{ name: 'apps-user-view-id', params: { id: item.id } }"
          >
            <VIcon icon="ri-eye-line" />
          </IconBtn>

          <IconBtn
            size="small"
            color="medium-emphasis"
          >
            <VIcon icon="ri-more-2-line" />

            <VMenu activator="parent">
              <VList>
                <VListItem link>
                  <template #prepend>
                    <VIcon icon="ri-download-line" />
                  </template>
                  <VListItemTitle>Download</VListItemTitle>
                </VListItem>
                <VListItem link>
                  <template #prepend>
                    <VIcon icon="ri-edit-box-line" />
                  </template>
                  <VListItemTitle>Edit</VListItemTitle>
                </VListItem>
              </VList>
            </VMenu>
          </IconBtn>
        </template>

        <!-- Pagination -->
        <template #bottom>
          <VDivider />

          <div class="d-flex justify-end flex-wrap gap-x-6 px-2 py-1">
            <div class="d-flex align-center gap-x-2 text-medium-emphasis text-base">
              Rows Per Page:
              <VSelect
                v-model="itemsPerPage"
                class="per-page-select"
                variant="plain"
                :items="[10, 20, 25, 50, 100]"
              />
            </div>

            <p class="d-flex align-center text-base text-high-emphasis me-2 mb-0">
              {{ paginationMeta({ page, itemsPerPage }, totalUsers) }}
            </p>

            <div class="d-flex gap-x-2 align-center me-2">
              <VBtn
                class="flip-in-rtl"
                icon="ri-arrow-left-s-line"
                variant="text"
                density="comfortable"
                color="high-emphasis"
                :disabled="page <= 1"
                @click="page <= 1 ? page = 1 : page--"
              />

              <VBtn
                class="flip-in-rtl"
                icon="ri-arrow-right-s-line"
                density="comfortable"
                variant="text"
                color="high-emphasis"
                :disabled="page >= Math.ceil(totalUsers / itemsPerPage)"
                @click="page >= Math.ceil(totalUsers / itemsPerPage) ? page = Math.ceil(totalUsers / itemsPerPage) : page++ "
              />
            </div>
          </div>
        </template>
      </VDataTableServer>
      <!-- SECTION -->
    </VCard>
    <!-- 👉 Add New User -->

    <AddNewUserDrawer
      v-model:isDrawerOpen="isAddNewUserDrawerVisible"
      :roles="roles"
      :api-errors="apiErrors"
      @user-data="addNewUser"
    />
    <RegistrationSuccessPopup
      :is-dialog-visible="isRegistrationSuccessDialogVisible"
      :email="dialogUserData.email"
      :contact="dialogUserData.contact"
      :password="dialogUserData.password"
      @update:is-dialog-visible="isRegistrationSuccessDialogVisible = $event"
    />
    <DeleteSuccessPopup
      :is-dialog-visible="isDeleteSuccessDialogVisible"
      @update:is-dialog-visible="isDeleteSuccessDialogVisible = $event"
    />
  </section>
</template>

<style lang="scss" scoped>
.app-user-search-filter {
  inline-size: 15.625rem;
}
</style>
