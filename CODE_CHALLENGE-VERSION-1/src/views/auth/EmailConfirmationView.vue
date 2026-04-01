<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

import AuthLayout from '@/components/layout/AuthLayout.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

const message = ref('A verification message should already be on its way to your inbox.')
const isSubmitting = ref(false)

async function resendEmail() {
  isSubmitting.value = true

  try {
    const response = await auth.resendVerificationEmail()
    message.value = response.message
  } catch (error) {
    message.value = error.message
  } finally {
    isSubmitting.value = false
  }
}

async function refreshStatus() {
  isSubmitting.value = true

  try {
    await auth.refreshUser()
    message.value = auth.isVerified
      ? 'Your email is already verified. Redirecting to the dashboard...'
      : 'The email is still pending verification.'

    if (auth.isVerified) {
      await router.push({ name: 'dashboard' })
    }
  } catch (error) {
    message.value = error.message
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <AuthLayout
    title="Confirm your email address"
    description="Protected CRUD routes stay behind a verified account so the SPA mirrors the security flow requested in the challenge."
  >
    <div class="form-grid">
      <div class="card card--soft">
        <p class="eyebrow">Current account</p>
        <h3>{{ auth.user?.name }}</h3>
        <p class="muted-text">{{ auth.user?.email }}</p>
      </div>

      <p class="feedback feedback--success">{{ message }}</p>

      <div class="action-bar">
        <button class="button" type="button" :disabled="isSubmitting" @click="resendEmail">
          Resend verification email
        </button>
        <button class="button button--ghost" type="button" :disabled="isSubmitting" @click="refreshStatus">
          I already verified
        </button>
      </div>
    </div>
  </AuthLayout>
</template>
