<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useGenerateImageVariant } from '@/@core/composable/useGenerateImageVariant'
import authV1ForgotPasswordMaskDark from '@images/pages/auth-v1-forgot-password-mask-dark.png'
import authV1ForgotPasswordMaskLight from '@images/pages/auth-v1-forgot-password-mask-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

const form = ref({ email: '' })
const authV1ThemeForgotPasswordMask = useGenerateImageVariant(authV1ForgotPasswordMaskLight, authV1ForgotPasswordMaskDark)
const router = useRouter()
const showSuccessMessage = ref(false)

definePage({
  meta: {
    layout: 'blank',
    unauthenticatedOnly: true,
  },
})

const sendResetLink = async () => {
  try {
    const response = await $api('auth/password/email', {
      method: 'POST',
      body: JSON.stringify({ email: form.value.email }),
      headers: {
        'Content-Type': 'application/json',
      },
    })

    console.log(response)
    showSuccessMessage.value = true // Afișează mesajul de succes
  }
  catch (error) {
    console.error(error)

    // Aici poți adăuga logica pentru a gestiona erori
  }
}
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <VCard
      class="auth-card pa-1 pa-sm-7"
      max-width="448"
    >
      <VCardItem class="justify-center pb-6">
        <VCardTitle>
          <RouterLink to="/">
            <div class="app-logo">
              <VNodeRenderer :nodes="themeConfig.app.logo" />
              <h1 class="app-logo-title">
                {{ themeConfig.app.title }}
              </h1>
            </div>
          </RouterLink>
        </VCardTitle>
      </VCardItem>

      <VCardText v-if="!showSuccessMessage">
        <h4 class="text-h4 mb-1">
          Forgot Password? 🔒
        </h4>
        <p class="mb-0">
          Enter your email and we'll send you instructions to reset your password
        </p>
      </VCardText>

      <VCardText v-if="!showSuccessMessage">
        <VForm @submit.prevent="sendResetLink">
          <VRow>
            <!-- email -->
            <VCol cols="12">
              <VTextField
                v-model="form.email"
                autofocus
                label="Email"
                type="email"
                placeholder="johndoe@email.com"
              />
            </VCol>

            <!-- reset password -->
            <VCol cols="12">
              <VBtn
                block
                type="submit"
              >
                Send Reset Link
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>

      <VCardText
        v-if="showSuccessMessage"
        class="text-center"
      >
        If that email address is in our records, we will send you an email to reset your password.
      </VCardText>

      <!-- back to login -->
      <VCol cols="12">
        <RouterLink
          class="d-flex align-center justify-center"
          :to="{ name: 'login' }"
        >
          <VIcon
            start
            icon="ri-arrow-left-s-line"
            size="20"
            class="flip-in-rtl"
          />
          <span class="text-base">Back to login</span>
        </RouterLink>
      </VCol>
    </VCard>
    <VImg
      :src="authV1ThemeForgotPasswordMask"
      class="d-none d-md-block auth-footer-mask flip-in-rtl"
    />
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>
