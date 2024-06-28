<script setup lang="ts">
definePage({
  meta: {
    layout: 'blank',
    public: true,
  },
})
</script>

<template>
  <div>
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <VCol cols="12">
          <!-- 👉 Sticky Section -->
          <DemoFormLayoutFormSticky />
        </VCol>
      </vcol>
    </VRow>
  </div>
</template>
