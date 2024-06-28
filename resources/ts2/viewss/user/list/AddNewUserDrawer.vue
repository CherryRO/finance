<script setup lang="ts">
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

import type { VForm } from 'vuetify/components/VForm'

import type { UserProperties } from '@db/users/types'

interface Props {
  isDrawerOpen: boolean
  roles: Array<any>
  apiErrors: Record<string, string[]>
}

const props = defineProps<Props>()

const emit = defineEmits<{
  (e: 'update:isDrawerOpen', value: boolean): void
  (e: 'user-data', value: UserProperties): void
}>()

const isFormValid = ref(false)
const refForm = ref<VForm>()
const fullName = ref('')
const email = ref('')
const contact = ref('')
const role = ref()
const password = ref('')
const status = ref()

// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)

  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const userData: UserProperties = {
        fullName: fullName.value,
        role: role.value,
        contact: contact.value,
        email: email.value,
        status: status.value,
        password: password.value,
        avatar: '',
      }

      emit('user-data', userData)
    }
  }).catch((errors: Record<string, string[]>) => {
    apiErrors.value = errors
  })
}

const handleDrawerModelValueUpdate = (val: boolean) => {
  emit('update:isDrawerOpen', val)
}
</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <AppDrawerHeaderSection
      title="Add User"
      @cancel="closeNavigationDrawer"
    />

    <VDivider />

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- 👉 Form -->
          <VForm
            ref="refForm"
            v-model="isFormValid"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Full name -->
              <VCol cols="12">
                <VTextField
                  v-model="fullName"
                  :rules="[requiredValidator]"
                  label="Full Name"
                  placeholder="John Doe"
                  :error-messages="apiErrors.fullName"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">
                <VTextField
                  v-model="email"
                  :rules="[requiredValidator, emailValidator]"
                  label="Email"
                  placeholder="johndoe@email.com"
                  :error-messages="apiErrors.email"
                />
              </VCol>

              <!-- 👉 Phone -->
              <VCol cols="12">
                <VTextField
                  v-model="contact"
                  type="number"
                  :rules="[requiredValidator]"
                  label="Phone Number"
                  placeholder="0712345678"
                  :error-messages="apiErrors.contact"
                />
              </VCol>

              <!-- 👉 Role -->
              <VCol cols="12">
                <VSelect
                  v-model="role"
                  label="Select Role"
                  placeholder="Select Role"
                  :rules="[requiredValidator]"
                  :items="props.roles"
                  item-title="title"
                  item-value="value"
                  :error-messages="apiErrors.role"
                />
              </VCol>
              <!-- 👉 Password -->
              <VCol cols="12">
                <VTextField
                  v-model="password"
                  :rules="[requiredValidator]"
                  label="Password"
                  type="password"
                  placeholder="Enter password"
                  :error-messages="apiErrors.password"
                />
              </VCol>

              <!-- 👉 Status -->
              <VCol cols="12">
                <VSelect
                  v-model="status"
                  label="Select Status"
                  placeholder="Select Status"
                  :rules="[requiredValidator]"
                  :items="[{ title: 'Active', value: 'Active' }, { title: 'Inactive', value: 'Inactive' }, { title: 'Pending', value: 'Pending' }]"
                />
              </VCol>

              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-4"
                >
                  Submit
                </VBtn>
                <VBtn
                  type="reset"
                  variant="outlined"
                  color="error"
                  @click="closeNavigationDrawer"
                >
                  Cancel
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>
