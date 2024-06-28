<script setup lang="ts">
import { onMounted, ref } from 'vue'
import type { CustomInputContent } from '@core/types'

const radioContent: CustomInputContent[] = [
  {
    title: 'Standard',
    desc: 'Timp de raspuns: 4 ore',
    value: '1',
    icon: 'ri-briefcase-line',
  },
  {
    title: 'Express',
    desc: 'Timp de raspuns 2 ore.',
    value: '2',
    icon: 'ri-rocket-line',
  },
  {
    title: 'Urgent',
    desc: 'Timp de raspuns: 10 minute.',
    value: '3',
    icon: 'ri-vip-crown-line',
  },
]

const showSuccessMessage = ref(false)

const formData = ref({
  fullName: '',
  contactNumber: '',
  priority: '1', // Assuming 'standard' maps to '1', 'express' to '2', and 'overnight' to '3'
  sumaNecesara: null,
  centruCost: '',
  detaliiPlata: '',
  paymentMethod: 'card',
  card: null, // Will store the card ID
  observatiiFinanciar: '',
  observatiiSolicitant: '',
  card: null as number | null, // Va stoca doar ID-ul cardului
  centruCost: null as number | null,
})

const cards = ref([])
const apiErrors = ref({})
const isLoading = ref(false)
const selectedCard = ref(null)
const centersOfCost = ref<{ id: number; name: string }[]>([])

const fetchCentersOfCost = async () => {
  try {
    isLoading.value = true
    console.log('Fetching centers of cost...')

    const response = await $api('/centers-of-cost', {
      method: 'GET',
    })

    const data = await response

    console.log('Răspuns API pentru centre de cost:', data)
    centersOfCost.value = data
    console.log('Centre de cost setate:', centersOfCost.value)
  }
  catch (error) {
    console.error('Error fetching centers of cost:', error)
    apiErrors.value = error.message || 'Unknown error'
  }
  finally {
    isLoading.value = false
  }
}

const fetchCards = async () => {
  try {
    isLoading.value = true
    console.log('Fetching cards...')

    const response = await $api('/masked-cards', {
      method: 'GET',
    })

    const data = await response

    console.log('Răspuns API:', data)
    cards.value = data
    console.log('Cards setate:', cards.value)
    apiErrors.value = {}
  }
  catch (error) {
    console.error('Error fetching cards:', error)
    apiErrors.value = error.message || 'Unknown error'
  }
  finally {
    isLoading.value = false
  }
}

const updateCardId = selectedCard => {
  if (selectedCard && typeof selectedCard === 'object')
    formData.value.card = selectedCard.id
  else
    formData.value.card = null
}

const updateCentruCost = selectedCentru => {
  if (selectedCentru && typeof selectedCentru === 'object')
    formData.value.centruCost = selectedCentru.id
  else
    formData.value.centruCost = null
}

const submitForm = async () => {
  try {
    isLoading.value = true
    console.log('Submitting form...')

    const response = await $api('/requests', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        full_name: formData.value.fullName,
        contact_number: formData.value.contactNumber,
        priority: formData.value.priority,
        suma_necesara: formData.value.sumaNecesara,
        center_of_cost_id: formData.value.centruCost,
        detalii_plata: formData.value.detaliiPlata,
        payment_method: formData.value.paymentMethod,
        card: formData.value.card,
        observatii_financiar: formData.value.observatiiFinanciar,
        observatii_solicitant: formData.value.observatiiSolicitant,
      }),
    })

    const data = await response.json()

    if (!response.ok)
      throw new Error(data.errors ? JSON.stringify(data.errors) : 'Unknown error')

    console.log('Form submitted successfully:', data)

    // Afișează mesajul de succes
    showSuccessMessage.value = true

    // Resetează formularul
    resetForm()

    // Ascunde mesajul de succes după 5 secunde
    setTimeout(() => {
      showSuccessMessage.value = false
    }, 5000)
  }
  catch (error) {
    console.error('Error submitting form:', error)
    apiErrors.value = error.message || 'Unknown error'
  }
  finally {
    isLoading.value = false
  }
}

const resetForm = () => {
  formData.value = {
    fullName: '',
    contactNumber: '',
    priority: '1',
    sumaNecesara: null,
    centruCost: null,
    detaliiPlata: '',
    paymentMethod: 'card',
    card: null,
    observatiiFinanciar: '',
    observatiiSolicitant: '',
  }
  selectedCard.value = null
}

onMounted(() => {
  fetchCards()
  fetchCentersOfCost()
})
</script>

<template>
  <VCard class="overflow-visible">
    <div class="w-100 sticky-header overflow-hidden rounded-t">
      <div class="d-flex align-center gap-4 flex-wrap bg-custom-background pa-6">
        <VCardTitle>Solicitare bugetare achizitie</VCardTitle>
        <VSpacer />
      </div>
    </div>

    <VCardText>
      <VRow>
        <VCol
          md="8"
          cols="12"
          class="mx-auto"
        >
          <VForm @submit.prevent="submitForm">
            <h2 class="text-lg font-weight-medium mb-6">
              1. Detalii solicitant
            </h2>
            <VRow>
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="formData.fullName"
                  prepend-inner-icon="ri-user-line"
                  label="Nume complet"
                  placeholder="John Doe"
                />
              </VCol>

              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="formData.contactNumber"
                  prepend-inner-icon="ri-smartphone-line"
                  label="Telefon"
                  placeholder="0744 444 444"
                  type="number"
                />
              </VCol>
            </VRow>

            <VDivider class="my-4" />

            <h2 class="text-lg font-weight-medium mb-6">
              2. Prioritate cerere
            </h2>

            <CustomRadiosWithIcon
              v-model:selected-radio="formData.priority"
              :radio-content="radioContent"
              :grid-column="{ sm: '4', cols: '12' }"
            />

            <VDivider class="my-4" />

            <h2 class="text-lg font-weight-medium mb-6">
              3. Detalii plata
            </h2>

            <VCol
              cols="12"
              md="6"
            >
              <VTextField
                v-model="formData.sumaNecesara"
                prepend-inner-icon="ri-money-euro-box-line"
                label="Suma necesara"
                placeholder="500 RON"
                type="number"
              />
            </VCol>

            <VCol
              cols="12"
              md="6"
            >
              <VAutocomplete
                v-model="formData.centruCost"
                label="Centru de cost"
                prepend-inner-icon="ri-building-line"
                :items="centersOfCost"
                item-title="name"
                item-value="id"
                placeholder="Alege centru cost"
                return-object
                @update:model-value="updateCentruCost"
              >
                <template #item="{ props, item }">
                  <VListItem
                    v-bind="props"
                    :title="item.raw.name"
                  />
                </template>
              </VAutocomplete>
            </VCol>

            <VCol
              cols="12"
              md="6"
            >
              <VTextField
                v-model="formData.detaliiPlata"
                prepend-inner-icon="ri-user-line"
                label="Detalii plată"
                placeholder="Suruburi"
              />
            </VCol>

            <VDivider class="my-4" />

            <h2 class="text-lg font-weight-medium mb-6">
              4. Metoda de plata
            </h2>

            <VRadioGroup
              v-model="formData.paymentMethod"
              inline
              class="mb-4"
            >
              <VRadio
                value="card"
                label="Card Bancar"
              />
              <VRadio
                value="cash"
                label="Numerar"
              />
            </VRadioGroup>

            <VRow v-show="formData.paymentMethod === 'card'">
              <VCol
                cols="12"
                md="6"
              >
                <VAutocomplete
                  v-if="cards.length > 0"
                  v-model="formData.card"
                  label="Card detinut"
                  prepend-inner-icon="ri-building-line"
                  :items="cards"
                  item-title="card_number"
                  item-value="id"
                  placeholder="Selecteaza cardul..."
                  return-object
                  @update:model-value="updateCardId"
                >
                  <template #item="{ props, item }">
                    <VListItem
                      v-bind="props"
                      :title="item.raw.card_number"
                    />
                  </template>
                </VAutocomplete>
                <p v-else>
                  Se încarcă cardurile...
                </p>
              </VCol>
            </VRow>

            <div v-show="formData.paymentMethod === 'cash'">
              <p>
                Incercam sa limitam platile numerar pentru a respecta legislatia in vigoare.
              </p>
              <p>Daca plata nu poate fi facuta cu cardul bancar, trimite cererea si vei primi sms cu detalii despre ridicarea sumei.</p>
            </div>
          </VForm>
        </VCol>
      </VRow>
    </VCardText>
    <div class="w-100 sticky-header overflow-hidden rounded-t">
      <div class="d-flex align-center justify-center gap-4 flex-wrap bg-custom-background pa-6">
        <div>
          <VAlert
            v-if="showSuccessMessage"
            type="success"
            variant="tonal"
            closable
            class="mb-4"
          >
            Formularul a fost trimis cu succes!
          </VAlert>
          <VBtn
            :loading="isLoading"
            @click="submitForm"
          >
            Trimite solicitare
          </VBtn>
        </div>
      </div>
    </div>
  </VCard>
</template>

<style lang="scss" scoped>
.sticky-header {
  position: sticky;
  z-index: 9;
  transition: all 0.3s ease-in-out;
}

.layout-nav-type-vertical {
  &.layout-navbar-sticky {
    .sticky-header {
      inset-block: 4rem 0;
    }
  }

  &.layout-navbar-static {
    .sticky-header {
      inset-block: 0 0;
    }
  }
}

.layout-nav-type-horizontal {
  &.layout-navbar-static {
    .sticky-header {
      inset-block: 0 0;
    }
  }

  &.layout-navbar-sticky {
    .sticky-header {
      inset-block: 7.375rem 0;
    }
  }
}
</style>
