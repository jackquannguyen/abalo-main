<template>
    <div class="article-form">
        <SiteHeader />
        <Impressum v-if="showImpressum" />
        <div v-else class="article-form__wrapper">
          <div class="article-form__card article-form__card--shadow">

            <h2 class="article-form__title">Neuen Artikel erstellen</h2>

            <form @submit.prevent="createArticle">
                <div class="article-form__group">
                    <label class="article-form__label">Name</label>
                    <input
                        type="text"
                        v-model="name"
                        class="article-form__input"
                        placeholder="Artikelname"
                    >
                </div>

                <div class="article-form__group">
                    <label class="article-form__label">Preis in Euro</label>
                    <input
                        type="number"
                        step="0.01"
                        v-model="price"
                        class="article-form__input"
                        placeholder="z.B. 12.99"
                    >
                </div>

                <div class="article-form__group">
                    <label class="article-form__label">Beschreibung</label>
                    <textarea
                        v-model="description"
                        class="article-form__input article-form__input--textarea"
                        placeholder="Beschreibung des Artikels"
                    ></textarea>
                </div>

                <button type="submit" class="article-form__button" :disabled="loading">
                    {{ loading ? 'Speichern...' : 'Artikel speichern' }}
                </button>
            </form>

            <p v-if="successMessage" class="article-form__success article-form__success--background1 ">
                {{ successMessage }}
            </p>

            <p v-if="errorMessage" class="article-form__error article-form__error--background2">
                {{ errorMessage }}
            </p>
        </div>
    </div>
    <SiteFooter @show-impressum="showImpressum = true" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import * as math from 'mathjs';
import SiteHeader from './SiteHeader.vue';
import SiteFooter from './SiteFooter.vue';
import Impressum from './Impressum.vue';

const name = ref('');
const price = ref('');
const description = ref('');
const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const showImpressum = ref(false);

async function createArticle() {
    successMessage.value = '';
    errorMessage.value = '';

    if (!name.value || !price.value || !description.value) {
        errorMessage.value = 'Bitte alle Felder ausfüllen.';
        return;
    }

    loading.value = true;

    try {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        const priceInCent = math.round(math.multiply(Number(price.value), 100));

        const response = await fetch('/api/articles', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                name: name.value,
                price: priceInCent,
                description: description.value
            })
        });

        const result = await response.json();

        if (!response.ok || result.success !== true) {
            throw new Error(result.error || 'Fehler beim Speichern.');
        }

        successMessage.value = 'Artikel wurde erfolgreich gespeichert.';

        name.value = '';
        price.value = '';
        description.value = '';
    } catch (error) {
        errorMessage.value = error.message;
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped lang="scss">
$primary: white;
$success-color: #d1fae5;
$error-color: #fee2e2;

.article-form {
  font-family: Arial, sans-serif;

  &__wrapper {
    min-height: 100vh;
    background: #f4f6f8;
    padding: 40px 20px;
  
  }

  &__card {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 14px;
  

  &--shadow {
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
  }

  &__title {
    margin-top: 0;
    color: #1f2937;
  }

  &__group {
    margin-bottom: 18px;
  }

  &__label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
  }

  &__input {
    width: 100%;
    padding: 11px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 15px;
    box-sizing: border-box;

    &--textarea {
      min-height: 110px;
    }

    &:focus {
      border-color: $primary;
    }
  }

  &__button {
    background: #d1d5db;
    color: gray;
    border: none;
    padding: 12px 18px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;

    &:disabled {
      background: #9ca3af;
      cursor: not-allowed;
    }
  }

  &__success {
    margin-top: 16px;
    color: #047857;
    padding: 12px;
    border-radius: 8px;
  

     &--background1 {
      background: $success-color;
     }
    }
  &__error {
    margin-top: 16px;
    color: #991b1b;
    padding: 12px;
    border-radius: 8px;
  
  &--background2 {
      background: $error-color;
     }
    }
}
</style>
