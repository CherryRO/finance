<script setup lang="ts">
import { onMounted, ref } from 'vue'

import poseM from '@images/pages/pose_m1.png'

interface Permission {
  name: string
  read: boolean
  write: boolean
  create: boolean
}

interface RoleDetails {
  id: number // Adaugă ID-ul rolului
  name: string
  permissions: Permission[]
}

interface Roles {
  role: string
  users: string[]
  details: RoleDetails
}

// 👉 Roles List
const roles = ref<Role[]>([])

const fetchRoles = async () => {
  try {
    const response = await $api('roles/rolespermission', {
      method: 'GET',
    })

    roles.value = response
  }
  catch (error) {
    console.error('Failed to fetch roles:', error)
  }
}

onMounted(fetchRoles)

const isRoleDialogVisible = ref(false)

const roleDetail = ref<RoleDetails>()

const isAddRoleDialogVisible = ref(false)

const editPermission = (item: Roles) => {
  isRoleDialogVisible.value = true
  roleDetail.value = {
    id: item.id,
    name: item.details.name,
    permissions: item.details.permissions,
  }
  console.log('Item:', item)
}

const addRoles = {
  action: 'create' as const,
  subject: 'Roles' as const,
}

const editRoles = {
  action: 'update' as const,
  subject: 'Roles' as const,
}
</script>

<template>
  <VRow>
    <!-- 👉 Roles -->
    <VCol
      v-for="item in roles"
      :key="item.role"
      cols="12"
      sm="6"
      lg="4"
    >
      <VCard>
        <VCardText class="d-flex align-center pb-4">
          <span>Total {{ item.users.length }} users</span>

          <VSpacer />

          <div class="v-avatar-group">
            <template
              v-for="(user, index) in item.users"
              :key="user"
            >
              <VAvatar
                v-if="item.users.length > 4 && item.users.length !== 4 && index < 3"
                size="40"
                :image="user"
              />

              <VAvatar
                v-if="item.users.length === 4"
                size="40"
                :image="user"
              />
            </template>
            <VAvatar
              v-if="item.users.length > 4"
              :color="$vuetify.theme.current.dark ? '#383B55' : '#F0EFF0'"
            >
              <span class="text-high-emphasis">
                +{{ item.users.length - 3 }}
              </span>
            </VAvatar>
          </div>
        </VCardText>

        <VCardText>
          <div class="d-flex justify-space-between align-end">
            <div>
              <h5 class="text-h5 mb-1">
                {{ item.role }}
              </h5>
              <a
                v-if="$can(editRoles.action, editRoles.subject)"
                href="javascript:void(0)"
                @click="editPermission(item)"
              >
                Edit Role
              </a>
            </div>

            <IconBtn
              color="secondary"
              class="mt-n2"
            >
              <VIcon icon="ri-file-copy-line" />
            </IconBtn>
          </div>
        </VCardText>
      </VCard>
    </VCol>

    <!-- 👉 Add New Role -->
    <VCol
      v-if="$can(addRoles.action, addRoles.subject)"
      cols="12"
      sm="6"
      lg="4"
    >
      <VCard
        class="h-100"
        :ripple="false"
      >
        <VRow
          no-gutters
          class="h-100"
        >
          <VCol
            cols="5"
            class="d-flex flex-column justify-end align-center"
          >
            <img
              width="69"
              :src="poseM"
            >
          </VCol>

          <VCol cols="7">
            <VCardText class="d-flex flex-column align-end justify-end gap-4">
              <VBtn
                size="small"
                @click="isAddRoleDialogVisible = true"
              >
                Add Role
              </VBtn>
              <span class="text-end">Add new role, if it doesn't exist.</span>
            </VCardText>
          </VCol>
        </VRow>
      </VCard>
      <AddEditRoleDialog
        v-model:is-dialog-visible="isAddRoleDialogVisible"
        @reload-roles="fetchRoles"
      />
    </VCol>
  </VRow>

  <AddEditRoleDialog
    v-model:is-dialog-visible="isRoleDialogVisible"
    v-model:role-permissions="roleDetail"
    @reload-roles="fetchRoles"
  />
</template>
