<script setup lang="ts">
import RoleCards from '@/views/roles/RoleCards.vue'
import UserList from '@/views/roles/UserList.vue'

definePage({
  meta: {
    action: 'read',
    subject: 'Roles',
  },
})

const user = {
  action: 'delete' as const,

  // `subject` property type is `Subjects` ("src/plugins/casl/AppAbility.ts")
  subject: 'Roles' as const,
}
</script>

<template>
  <VRow>
    <VCol cols="12">
      <h4 class="text-h4 mb-1">
        Roles List
      </h4>
      <p class="text-body-1 mb-0">
        A role provided access to predefined menus and features so that depending on assigned role an administrator can have access to what he need
      </p>
    </VCol>

    <!-- 👉 Roles Cards -->
    <VCol cols="12">
      <RoleCards />
    </VCol>

    <VCol cols="12">
      <h4 class="text-h4 mt-6 mb-1">
        Total users with their roles
      </h4>
      <p class="text-body-1 mb-6">
        Find all of your company's administrator accounts and their associate roles.
      </p>

      <!-- 👉 User List  -->
      <UserList />
    </VCol>
  </VRow>
</template>
