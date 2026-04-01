<script setup>
import { computed } from 'vue'
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router'

import { resourceNavigation } from '@/config/resources'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const pageTitle = computed(() => route.meta.title ?? 'Workspace')

async function handleLogout() {
  try {
    await auth.logout()
  } finally {
    await router.push({ name: 'login' })
  }
}
</script>

<template>
  <div class="app-shell">
    <aside class="app-sidebar">
      <div class="brand-lockup">
        <p class="eyebrow">Release / Exercise 0</p>
        <h1>DropStudio Orders</h1>
        <p class="muted-text">
          Laravel API + VueJS SPA wired together so you can focus on the interface pass next.
        </p>
      </div>

      <nav class="app-nav">
        <RouterLink :to="{ name: 'dashboard' }" class="app-nav__link">
          Dashboard
        </RouterLink>

        <RouterLink
          v-for="item in resourceNavigation"
          :key="item.key"
          :to="{ name: item.key }"
          class="app-nav__link"
        >
          {{ item.title }}
        </RouterLink>
      </nav>
    </aside>

    <div class="app-main">
      <header class="app-toolbar card">
        <div>
          <p class="eyebrow">Step 2 / VueJS</p>
          <h2>{{ pageTitle }}</h2>
        </div>

        <div class="toolbar-actions">
          <RouterLink
            v-if="!auth.isVerified"
            :to="{ name: 'email-confirmation' }"
            class="status-pill status-pill--warning"
          >
            Email pending
          </RouterLink>
          <span v-else class="status-pill status-pill--success">Verified</span>

          <div class="user-chip">
            <strong>{{ auth.user?.name }}</strong>
            <span>{{ auth.user?.email }}</span>
          </div>

          <button class="button button--ghost" type="button" @click="handleLogout">
            Logout
          </button>
        </div>
      </header>

      <main class="app-content">
        <RouterView />
      </main>
    </div>
  </div>
</template>
