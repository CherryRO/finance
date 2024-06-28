<script setup>
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    default: false,
  },
  router: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['update:isDialogVisible'])

const redirectToLogin = () => {
  emit('update:isDialogVisible', false)
  props.router.push({ name: 'login' })
}
</script>

<template>
  <VDialog
    :model-value="isDialogVisible"
    persistent
    class="v-dialog-sm"
    @update:model-value="$emit('update:isDialogVisible', $event)"
  >
    <VCard title="Success!">
      <VCardText>
        Your password has been changed! Please log in.
      </VCardText>
      <VCardText class="d-flex justify-end flex-wrap gap-4">
        <VBtn
          color="success"
          @click="redirectToLogin"
        >
          Done
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>
