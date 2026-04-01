<script setup>
import { onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'

import { resourceNavigation, resourceConfigs } from '@/config/resources'
import { api } from '@/lib/api'

const cards = ref(
  resourceNavigation.map((resource) => ({
    ...resource,
    count: 0,
  })),
)
const isLoading = ref(true)
const errorMessage = ref('')

onMounted(async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const responses = await Promise.all(
      resourceNavigation.map((resource) => api.get(resourceConfigs[resource.key].endpoint)),
    )

    cards.value = resourceNavigation.map((resource, index) => ({
      ...resource,
      count: responses[index]?.data?.length ?? 0,
    }))
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <section class="stack">
    <article class="card hero-card">
      <p class="eyebrow">Workspace overview</p>
      <h2>The Step 2 shell is ready.</h2>
      <p>
        You already have authenticated navigation, protected CRUD routes and the six operational
        resources connected to the Laravel API.
      </p>
    </article>

    <p v-if="errorMessage" class="feedback feedback--error">{{ errorMessage }}</p>

    <section class="dashboard-grid">
      <article v-for="card in cards" :key="card.key" class="card resource-card">
        <p class="eyebrow">{{ card.title }}</p>
        <h3>{{ isLoading ? '...' : card.count }}</h3>
        <p>{{ card.description }}</p>
        <RouterLink class="button button--ghost" :to="{ name: card.key }">Open resource</RouterLink>
      </article>
    </section>
  </section>
</template>
