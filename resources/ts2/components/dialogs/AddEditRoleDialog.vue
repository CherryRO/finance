<script setup lang="ts">
import { computed, defineEmits, defineProps, ref, watch, withDefaults } from 'vue'
import { VForm } from 'vuetify/components/VForm'

interface Permission {
  name: string
  create: boolean
  read: boolean
  update: boolean
  delete: boolean
}

interface RoleDetails {
  id: number
  name: string
  permissions: Permission[]
}

interface Props {
  rolePermissions?: RoleDetails
  isDialogVisible: boolean
}

interface Emit {
  (e: 'update:isDialogVisible', value: boolean): void
  (e: 'update:rolePermissions', value: RoleDetails): void
  (e: 'update:modelValue', value: Permission[]): void
}

const props = withDefaults(defineProps<Props>(), {
  rolePermissions: () => ({
    name: '',
    permissions: [],
  }),
})

const emit = defineEmits<Emit>()

const permissions = ref<Permission[]>([
  { name: 'Users', create: false, read: false, update: false, delete: false },
  { name: 'Roles', create: false, read: false, update: false, delete: false },
])

const isSelectAll = ref(false)
const role = ref('')
const refPermissionForm = ref<VForm>()
const errors = ref([])

const checkedCount = computed(() => {
  let counter = 0
  permissions.value.forEach(permission => {
    Object.entries(permission).forEach(([key, value]) => {
      if (key !== 'name' && value)
        counter++
    })
  })

  return counter
})

const isIndeterminate = computed(() => checkedCount.value > 0 && checkedCount.value < (permissions.value.length * 3))

watch(isSelectAll, selectAllState => {
  try {
    if (permissions.value.some(permission => permission.create !== selectAllState || permission.read !== selectAllState || permission.update !== selectAllState || permission.delete !== selectAllState)) {
      permissions.value = permissions.value.map(permission => ({
        ...permission,
        create: selectAllState,
        read: selectAllState,
        update: selectAllState,
        delete: selectAllState,
      }))
    }
  }
  catch (error) {
    console.error('Error updating permissions:', error)

    // Handle the error gracefully (e.g., show a message to the user)
  }
})

watch(isIndeterminate, () => {
  if (!isIndeterminate.value)
    isSelectAll.value = false
})

watch(permissions, () => {
  if (checkedCount.value === (permissions.value.length * 3))
    isSelectAll.value = true
}, { deep: true })

watch(() => props.rolePermissions, newVal => {
  if (newVal && newVal.permissions.length) {
    role.value = newVal.name
    permissions.value = permissions.value.map(permission => {
      const rolePermission = newVal.permissions.find(item => item.name === permission.name)
      if (rolePermission) {
        return {
          ...permission,
          ...rolePermission,
        }
      }

      return permission
    })
  }
}, { immediate: true })

const onSubmit = async () => {
  // Asigură-te că toate câmpurile de permisiuni sunt fie true, fie false
  const sanitizedPermissions = permissions.value.map(permission => ({
    name: permission.name,
    create: permission.create ?? false,
    read: permission.read ?? false,
    update: permission.update ?? false,
    delete: permission.delete ?? false,
  }))

  const rolePermissions = {
    name: role.value,
    permissions: sanitizedPermissions,
  }

  try {
    if (props.rolePermissions?.name) {
      // Dacă rolul există deja, faceți o cerere PUT pentru a-l actualiza
      await $api(`/roles/update/${props.rolePermissions.id}`, {
        method: 'PUT',
        body: rolePermissions,
      })
    }
    else {
      // Dacă este un rol nou, faceți o cerere POST pentru a-l crea
      await $api('/roles/add', {
        method: 'POST',
        body: rolePermissions,
      })
    }

    emit('update:isDialogVisible', false)
    emit('reload-roles')
    isSelectAll.value = false
    refPermissionForm.value?.reset()
  }
  catch (error) {
    console.error('Failed to save role:', error)
    errors.value.push(`Error updating permissions: ${error.message}`)

    // Gestionați eroarea aici (de exemplu, afișați un mesaj de eroare utilizatorului)
  }
}

const onReset = () => {
  emit('update:isDialogVisible', false)
  isSelectAll.value = false
  refPermissionForm.value?.reset()
}

const onDelete = async () => {
  try {
    await $api(`/roles/delete/${props.rolePermissions.id}`, {
      method: 'DELETE',
    })

    emit('update:isDialogVisible', false)
    emit('reload-roles')
    isSelectAll.value = false
    refPermissionForm.value?.reset()
  }
  catch (error) {
    console.error('Failed to delete role:', error)
    errors.value.push(`Error deleting role: ${error.message}`)

    // Gestionați eroarea aici (de exemplu, afișați un mesaj de eroare utilizatorului)
  }
}

const user = {
  action: 'delete' as const,
  subject: 'Roles' as const,
}
</script>

<template>
  <VDialog
    :width="$vuetify.display.smAndDown ? 'auto' : 900"
    :model-value="props.isDialogVisible"
    @update:model-value="onReset"
  >
    <VCard class="pa-sm-11 pa-3">
      <DialogCloseBtn
        variant="text"
        size="default"
        @click="onReset"
      />

      <VCardText>
        <div class="text-center mb-10">
          <h4 class="text-h4 mb-2">
            {{ props.rolePermissions?.name ? 'Edit' : 'Add' }} Role
          </h4>
          <p class="text-body-1">
            {{ props.rolePermissions?.name ? 'Edit' : 'Add' }} Role
          </p>
        </div>

        <VForm ref="refPermissionForm">
          <VTextField
            v-model="role"
            label="Role Name"
            placeholder="Enter Role Name"
          />

          <h5 class="text-h5 my-6">
            Role Permissions
          </h5>

          <VTable class="permission-table text-no-wrap">
            <tr>
              <td class="text-h6">
                Administrator Access
              </td>
              <td colspan="4">
                <div class="d-flex justify-end">
                  <VCheckbox
                    v-model="isSelectAll"
                    v-model:indeterminate="isIndeterminate"
                    label="Select All"
                  />
                </div>
              </td>
            </tr>

            <template
              v-for="permission in permissions"
              :key="permission.name"
            >
              <tr>
                <td class="text-h6">
                  {{ permission.name }}
                </td>
                <td style="inline-size: 5.75rem;">
                  <div class="d-flex justify-end">
                    <VCheckbox
                      v-model="permission.create"
                      label="Create"
                    />
                  </div>
                </td>
                <td style="inline-size: 5.75rem;">
                  <div class="d-flex justify-end">
                    <VCheckbox
                      v-model="permission.read"
                      label="Read"
                    />
                  </div>
                </td>
                <td style="inline-size: 5.75rem;">
                  <div class="d-flex justify-end">
                    <VCheckbox
                      v-model="permission.update"
                      label="Update"
                    />
                  </div>
                </td>
                <td style="inline-size: 5.75rem;">
                  <div class="d-flex justify-end">
                    <VCheckbox
                      v-model="permission.delete"
                      label="Delete"
                    />
                  </div>
                </td>
              </tr>
            </template>
          </VTable>
          <div v-if="errors.length">
            <ul>
              <li
                v-for="error in errors"
                :key="error"
              >
                {{ error }}
              </li>
            </ul>
          </div>
          <div class="d-flex align-center justify-center gap-3 mt-6">
            <VBtn @click="onSubmit">
              Submit
            </VBtn>
            <VBtn
              v-if="$can(user.action, user.subject) && props.rolePermissions?.name"
              color="error"
              variant="outlined"
              @click="onDelete"
            >
              Delete
            </VBtn>
            <VBtn
              color="secondary"
              variant="outlined"
              @click="onReset"
            >
              Cancel
            </VBtn>
          </div>
        </VForm>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<style lang="scss">
.permission-table {
  td {
    border-block-end: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    padding-block: 0.625rem;

    .v-checkbox {
      min-inline-size: 4.75rem;
    }

    &:not(:first-child) {
      padding-inline: 0.75rem;
    }

    .v-label {
      white-space: nowrap;
    }
  }
}
</style>
