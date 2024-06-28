<script setup lang="ts">
import { useGenerateImageVariant } from '@/@core/composable/useGenerateImageVariant'
import authV1LoginMaskDark from '@images/pages/auth-v1-login-mask-dark.png'
import authV1LoginMaskLight from '@images/pages/auth-v1-login-mask-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

definePage({
  meta: {
    layout: 'blank',
    unauthenticatedOnly: true,
  },
})

const form = ref({
  email: '',
  password: '',
  remember: false,
})

const authV1ThemeLoginMask = useGenerateImageVariant(authV1LoginMaskLight, authV1LoginMaskDark)
const isPasswordVisible = ref(false)
const authError = ref(false)

const route = useRoute()
const router = useRouter()

const ability = useAbility()

const errors = ref<Record<string, string | undefined>>({
  email: undefined,
  password: undefined,
})

const refVForm = ref<VForm>()

const credentials = ref({
  email: '',
  password: '',
})

const rememberMe = ref(false)

const login = async () => {
  try {
    const csrfToken = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN=')).split('=')[1]

    const res = await $api('/auth/login', {
      method: 'POST',
      body: {
        email: credentials.value.email,
        password: credentials.value.password,
      },
      headers: {
        'X-XSRF-TOKEN': decodeURIComponent(csrfToken),
      },
      onResponseError({ response }) {
        errors.value = response._data.errors
        if (response.status === 401)
          authError.value = true
        console.log('authError set to true')
      },
    })

    const { accessToken, userData, userAbilityRules } = res

    const expirationTime = new Date(Date.now() + 12 * 60 * 60 * 1000) // 12 hours in miliseconds

    useCookie('userAbilityRules', {
      expires: expirationTime,
    }).value = userAbilityRules

    ability.update(userAbilityRules)

    useCookie('userData', {
      expires: expirationTime,
    }).value = userData

    useCookie('accessToken', {
      expires: expirationTime,
    }).value = accessToken

    await nextTick(() => {
      router.replace(route.query.to ? String(route.query.to) : '/')
    })
  }
  catch (err) {
    console.error(err)
  }
}

const onSubmit = () => {
  refVForm.value?.validate()
    .then(({ valid: isValid }) => {
      if (isValid)
        login()
    })
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

      <VCardText>
        <h4 class="text-h4 mb-1">
          Welcome to <span class="text-capitalize">{{ themeConfig.app.title }}! 👋🏻</span>
        </h4>

        <p class="mb-0">
          Please sign-in to your account and start the adventure
        </p>
      </VCardText>

      <VCardText>
        <VAlert
          v-if="authError"
          color="error"
          variant="outlined"
          title="Authentification failed."
        >
          Please check your email address and password and try again.
        </VAlert>
      </VCardText>

      <VCardText>
        <VForm
          ref="refVForm"
          @submit.prevent="onSubmit"
        >
          <VRow>
            <!-- email -->
            <VCol cols="12">
              <VTextField
                v-model="credentials.email"
                autofocus
                label="Email"
                type="email"
                placeholder="johndoe@email.com"
              />
            </VCol>

            <!-- password -->
            <VCol cols="12">
              <VTextField
                v-model="credentials.password"
                label="Password"
                placeholder="············"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
              />

              <!-- remember me checkbox -->
              <div class="d-flex align-center justify-space-between flex-wrap my-6">
                <VCheckbox
                  v-model="credentials.remember"
                  label="Remember me"
                />

                <RouterLink
                  class="text-primary"
                  :to="{ name: 'pages-authentication-forgot-password-v1' }"
                >
                  Forgot Password?
                </RouterLink>
              </div>

              <!-- login button -->
              <VBtn
                block
                type="submit"
              >
                Login
              </VBtn>
            </VCol>

            <!--
              DISABLED CREATE ACCOUNT AND OTHER LOGIN
              create account
              <VCol
              cols="12"
              class="text-body-1 text-center"
              >
              <span class="d-inline-block">
              New on our platform?
              </span>
              <RouterLink
              class="text-primary ms-1 d-inline-block text-body-1"
              :to="{ name: 'pages-authentication-register-v1' }"
              >
              Create an account
              </RouterLink>
              </VCol>

              <VCol
              cols="12"
              class="d-flex align-center"
              >
              <VDivider />
              <span class="mx-4 text-high-emphasis">or</span>
              <VDivider />
              </VCol>

              <!-- auth providers
              <VCol
              cols="12"
              class="text-center"
              >
              <AuthProvider />
              </VCol>
            -->
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
    <VImg
      :src="authV1ThemeLoginMask"
      class="d-none d-md-block auth-footer-mask flip-in-rtl"
    />
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>
