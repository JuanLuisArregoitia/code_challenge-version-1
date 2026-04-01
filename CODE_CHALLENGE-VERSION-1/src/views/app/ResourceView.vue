<script setup>
import { computed, reactive, ref, watch } from 'vue'

import { getResourceConfig, resourceConfigs } from '@/config/resources'
import { api } from '@/lib/api'

const props = defineProps({
  resourceKey: {
    type: String,
    required: true,
  },
})

const resource = computed(() => getResourceConfig(props.resourceKey))
const records = ref([])
const relationOptions = ref({})
const isLoading = ref(false)
const isSubmitting = ref(false)
const editingRecordId = ref(null)
const feedbackMessage = ref('')
const feedbackType = ref('success')
const fieldErrors = ref({})

const form = reactive({})

function buildEmptyForm() {
  return Object.fromEntries(resource.value.fields.map((field) => [field.key, '']))
}

function applyForm(values) {
  const nextValues = { ...buildEmptyForm(), ...values }

  Object.entries(nextValues).forEach(([key, value]) => {
    form[key] = value ?? ''
  })
}

function clearFeedback() {
  feedbackMessage.value = ''
  fieldErrors.value = {}
}

function resetForm() {
  editingRecordId.value = null
  applyForm({})
}

function startCreate() {
  clearFeedback()
  resetForm()
}

function startEdit(record) {
  editingRecordId.value = record.id
  clearFeedback()
  applyForm(record)
}

async function loadRecords() {
  const response = await api.get(resource.value.endpoint)
  records.value = response?.data ?? []
}

async function loadRelationOptions() {
  const resources = [...new Set(
    resource.value.fields
      .filter((field) => field.optionsResource)
      .map((field) => field.optionsResource),
  )]

  const entries = await Promise.all(
    resources.map(async (resourceKey) => {
      const response = await api.get(resourceConfigs[resourceKey].endpoint)
      return [resourceKey, response?.data ?? []]
    }),
  )

  relationOptions.value = Object.fromEntries(entries)
}

async function bootstrapResource() {
  isLoading.value = true
  startCreate()

  try {
    await Promise.all([loadRecords(), loadRelationOptions()])
  } catch (error) {
    feedbackType.value = 'error'
    feedbackMessage.value = error.message
  } finally {
    isLoading.value = false
  }
}

function getOptions(field) {
  return relationOptions.value[field.optionsResource] ?? []
}

function getOptionLabel(field, record) {
  return field.optionLabel ? field.optionLabel(record) : record.name ?? record.id
}

function formatCell(column, record) {
  return column.value(record)
}

function buildPayload() {
  return Object.fromEntries(
    resource.value.fields.map((field) => {
      let value = form[field.key]

      if (field.type === 'number' && value !== '') {
        value = Number(value)
      }

      if (field.type === 'select' && value !== '') {
        value = Number(value)
      }

      return [field.key, value]
    }),
  )
}

async function submitForm() {
  isSubmitting.value = true
  clearFeedback()

  const payload = buildPayload()
  const isEditing = editingRecordId.value !== null
  const endpoint = isEditing
    ? `${resource.value.endpoint}/${editingRecordId.value}`
    : resource.value.endpoint

  try {
    await (isEditing ? api.put(endpoint, payload) : api.post(endpoint, payload))
    feedbackType.value = 'success'
    feedbackMessage.value = `${isEditing ? 'Updated' : 'Created'} ${resource.value.singular} successfully.`
    await loadRecords()
    resetForm()
  } catch (error) {
    feedbackType.value = 'error'
    feedbackMessage.value = error.message
    fieldErrors.value = error.data?.errors ?? {}
  } finally {
    isSubmitting.value = false
  }
}

async function removeRecord(record) {
  const confirmed = window.confirm(`Delete this ${resource.value.singular}?`)

  if (!confirmed) {
    return
  }

  clearFeedback()

  try {
    await api.delete(`${resource.value.endpoint}/${record.id}`)
    feedbackType.value = 'success'
    feedbackMessage.value = `${resource.value.singular} deleted successfully.`

    if (editingRecordId.value === record.id) {
      resetForm()
    }

    await loadRecords()
  } catch (error) {
    feedbackType.value = 'error'
    feedbackMessage.value = error.message
  }
}

watch(
  () => props.resourceKey,
  () => {
    bootstrapResource()
  },
  { immediate: true },
)
</script>

<template>
  <section class="resource-layout">
    <article class="card resource-overview">
      <p class="eyebrow">{{ resource.title }}</p>
      <h2>{{ resource.description }}</h2>
      <p>{{ resource.intro }}</p>

      <div class="action-bar">
        <button class="button" type="button" @click="startCreate">
          New {{ resource.singular }}
        </button>
        <span class="status-pill">{{ records.length }} loaded</span>
      </div>
    </article>

    <div class="resource-grid">
      <article class="card">
        <div class="section-heading">
          <div>
            <p class="eyebrow">Records</p>
            <h3>{{ resource.title }}</h3>
          </div>
          <span class="muted-text">{{ isLoading ? 'Loading...' : `${records.length} rows` }}</span>
        </div>

        <p
          v-if="feedbackMessage"
          class="feedback"
          :class="feedbackType === 'error' ? 'feedback--error' : 'feedback--success'"
        >
          {{ feedbackMessage }}
        </p>

        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th v-for="column in resource.columns" :key="column.label">{{ column.label }}</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!isLoading && !records.length">
                <td :colspan="resource.columns.length + 1" class="empty-row">
                  No {{ resource.title.toLowerCase() }} yet.
                </td>
              </tr>
              <tr v-for="record in records" :key="record.id">
                <td v-for="column in resource.columns" :key="column.label">
                  {{ formatCell(column, record) }}
                </td>
                <td class="row-actions">
                  <button class="button button--ghost button--small" type="button" @click="startEdit(record)">
                    Edit
                  </button>
                  <button class="button button--ghost button--small danger" type="button" @click="removeRecord(record)">
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>

      <article class="card">
        <div class="section-heading">
          <div>
            <p class="eyebrow">{{ editingRecordId ? 'Editing' : 'Creating' }}</p>
            <h3>{{ editingRecordId ? `Update ${resource.singular}` : `Create ${resource.singular}` }}</h3>
          </div>
          <button v-if="editingRecordId" class="button button--ghost button--small" type="button" @click="startCreate">
            Reset
          </button>
        </div>

        <form class="form-grid" @submit.prevent="submitForm">
          <label v-for="field in resource.fields" :key="field.key" class="field">
            <span>{{ field.label }}</span>

            <textarea
              v-if="field.type === 'textarea'"
              v-model="form[field.key]"
              class="input textarea"
              :placeholder="field.placeholder"
              rows="4"
            />

            <select
              v-else-if="field.type === 'select'"
              v-model="form[field.key]"
              class="input"
            >
              <option value="">Select an option</option>
              <option
                v-for="option in getOptions(field)"
                :key="option.id"
                :value="option.id"
              >
                {{ getOptionLabel(field, option) }}
              </option>
            </select>

            <input
              v-else
              v-model="form[field.key]"
              class="input"
              :type="field.type"
              :placeholder="field.placeholder"
              :step="field.step"
              :min="field.min"
              :max="field.max"
            />

            <small v-if="fieldErrors[field.key]?.[0]" class="field-error">
              {{ fieldErrors[field.key][0] }}
            </small>
          </label>

          <div class="action-bar">
            <button class="button" type="submit" :disabled="isSubmitting">
              {{ isSubmitting ? 'Saving...' : editingRecordId ? 'Update record' : 'Create record' }}
            </button>
          </div>
        </form>
      </article>
    </div>
  </section>
</template>
