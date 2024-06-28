<script setup>
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:isDialogVisible'])

const closeDialog = () => {
  emit('update:isDialogVisible', false)
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
        User successfully deleted.
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
