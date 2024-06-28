<script setup>
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    default: false,
  },
  email: {
    type: String,
    default: '',
  },
  contact: {
    type: String,
    default: '',
  },
  password: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:isDialogVisible'])

const closeDialog = () => {
  emit('update:isDialogVisible', false)
}

const showPassword = ref(false)

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}
</script>

<template>
  <VDialog
    :model-value="props.isDialogVisible"
    persistent
    class="v-dialog-sm"
    @update:model-value="closeDialog"
  >
    <!-- Dialog Content -->
    <VCard title="Success!">
      <DialogCloseBtn
        variant="text"
        size="default"
        @click="closeDialog"
      />
      <VCardText>
        User created successfully. Don't forget to give him the login data and explain the application.
      </VCardText>
      <VCardText>
        <p class="mb-2">
          <strong>Email:</strong> {{ props.email }}
        </p>
        <p class="mb-2">
          <strong>Phone:</strong> {{ props.contact }}
        </p>
        <p class="mb-2">
          <strong>Password: </strong>
          <span
            v-if="!showPassword"
            @click="togglePasswordVisibility"
          > Click to see</span>
          <span
            v-else
            @click="togglePasswordVisibility"
          >{{ props.password }}</span>
        </p>
      </VCardText>
      <VCardText class="d-flex justify-end flex-wrap gap-4">
        <VBtn
          color="success"
          @click="closeDialog"
        >
          Done
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>
