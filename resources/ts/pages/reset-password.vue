<script setup lang="ts">
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import { useGenerateImageVariant } from '@/@core/composable/useGenerateImageVariant'
import ResetSuccessPopup from '@/views/pages/authentication/ResetSuccessPopup.vue'
import authV1ResetPasswordMaskDark from '@images/pages/auth-v1-reset-password-mask-dark.png'
import authV1ResetPasswordMaskLight from '@images/pages/auth-v1-reset-password-mask-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

definePage({
  meta: {
    layout: 'blank',
    unauthenticatedOnly: true,
  },
})

const closeDialog = () => {
  isResetSuccessDialogVisible.value = false
  router.push({ name: 'login' })
}

const authV1ResetPasswordMask = useGenerateImageVariant(authV1ResetPasswordMaskLight, authV1ResetPasswordMaskDark)

const router = useRouter()
const route = useRoute()
const token = route.query.token
const isValidToken = ref(false)
const passwordErrors = ref([])
const passwordTimeout = ref(null)
const isResetSuccessDialogVisible = ref(false)

onMounted(async () => {
  try {
    // Utilizează funcția $api pentru a verifica tokenul
    const response = await $api(`auth/verify-token?token=${token}`, {
      method: 'GET',
    })

    // Preiați și verificați validitatea tokenului din răspuns
    isValidToken.value = response.isValid
    if (!isValidToken.value) {
      alert('Token expirat')
      router.push({ name: 'login' })
    }
  }
  catch (error) {
    console.error('Error verifying token:', error)
    alert('Failed to verify token')
    router.push({ name: 'login' })
  }
})

const form = ref({
  newPassword: '',
  confirmPassword: '',
})

const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)

const submitNewPassword = async () => {
  if (form.value.newPassword !== form.value.confirmPassword) {
    alert('Passwords do not match')

    return
  }

  const token = route.query.token // Asumând că tokenul este trimis ca query parametru
  const email = route.query.email

  console.log(token)

  try {
    const response = await $api('auth/password/reset', {
      method: 'POST',
      body: JSON.stringify({
        token,
        email,
        password: form.value.newPassword,
        password_confirmation: form.value.confirmPassword,
      }),
      headers: {
        'Content-Type': 'application/json',
      },
    })

    console.log(response)

    // Aici poți adăuga logica pentru a gestiona răspunsul pozitiv, cum ar fi redirecționarea către pagina de login
    isResetSuccessDialogVisible.value = true
  }
  catch (error) {
    console.error(error)

    // Aici poți adăuga logica pentru a gestiona erori
  }
}

const isPasswordMatched = computed(() => {
  return form.value.newPassword === form.value.confirmPassword
})

const isPasswordComplex = computed(() => {
  const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/

  return passwordRegex.test(form.value.confirmPassword)
})

const checkPasswordsMatch = () => {
  passwordErrors.value = []
  if (!isPasswordMatched.value)
    passwordErrors.value.push('Passwords do not match')

  if (!isPasswordComplex.value && form.value.newPassword !== '')
    passwordErrors.value.push('Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one digit')
  isResetSuccessDialogVisible.value = true
}

watch(
  () => form.value.confirmPassword,
  () => {
    // Resetează timerul existent, dacă există
    clearTimeout(passwordTimeout.value)

    // Setează un nou timer pentru a verifica parolele după 2 secunde
    passwordTimeout.value = setTimeout(() => {
      checkPasswordsMatch()
    }, 2000)
  },
)
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

      <VCardText>
        <h4 class="text-h4 mb-1">
          Reset Password 🔒
        </h4>
        <p class="mb-0">
          Please enter your new password below.
        </p>
      </VCardText>

      <VCardText>
        <VForm @submit.prevent="submitNewPassword">
          <VRow>
            <!-- Password -->
            <VCol cols="12">
              <VTextField
                v-model="form.newPassword"
                label="New Password"
                placeholder="Enter new password"
                :type="isPasswordVisible ? 'text' : 'password'"
                :error="passwordErrors.length > 0"
                :error-messages="passwordErrors"
                :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                @click:append-inner="() => (isPasswordVisible = !isPasswordVisible)"
              />
            </VCol>

            <!-- Confirm Password -->
            <VCol cols="12">
              <VTextField
                v-model="form.confirmPassword"
                label="Confirm Password"
                placeholder="Confirm new password"
                :type="isConfirmPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isConfirmPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                :error="!isPasswordMatched"
                :error-messages="isPasswordMatched ? [] : ['Passwords do not match']"
                @click:append-inner="() => (isConfirmPasswordVisible = !isConfirmPasswordVisible)"
              />
            </VCol>

            <!-- Reset Password Button -->
            <VCol cols="12">
              <VBtn
                block
                type="submit"
                :disabled="!isPasswordMatched"
              >
                Set New Password
              </VBtn>
            </VCol>

            <!-- Back to Login -->
            <VCol cols="12">
              <RouterLink
                class="d-flex align-center justify-center"
                :to="{ name: 'login' }"
              >
                <VIcon
                  start
                  size="20"
                  icon="ri-arrow-left-s-line"
                  class="flip-in-rtl"
                />
                <span class="text-base">Back to login</span>
              </RouterLink>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
    <VImg
      :src="authV1ResetPasswordMask"
      class="d-none d-md-block auth-footer-mask flip-in-rtl"
    />
  </div>
  <ResetSuccessPopup
    v-model:isDialogVisible="isResetSuccessDialogVisible"
    :router="router"
    @update:is-dialog-visible="closeDialog"
  />
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>
