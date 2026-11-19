<template>
    <div class="article-search">
        <div class="article-search__wrapper">
        <div class="article-search__card">
            <h2 class="article-search__title">Dynamische Artikelsuche</h2>
            <p class="article-search__subtitle">
                Alle Artikel werden angezeigt. Für die Suche mindestens 3 Zeichen eingeben.
            </p>

            <input
                type="text"
                v-model="search"
                class="article-search__input"
                placeholder="z.B. Reifen, Toyota, Radio..."
            >

            <p v-if="search.length > 0 && search.length < 3">
                Bitte mindestens 3 Zeichen eingeben.
            </p>

            <p v-if="loading">
                Suche läuft...
            </p>

            <p v-if="error" class="article-search__error article-search__error--visible">
                {{ error }}
            </p>

            <div v-if="articles.length > 0" class="article-search__result-list">
                <div
                    v-for="article in articles"
                    :key="article.id"
                    class="article-search__result-item"
                >
                    <div>
                        <h3>{{ article.ab_name }}</h3>
                        <p class="article-search__description">
                            {{ article.ab_description || 'Keine Beschreibung vorhanden.' }}
                        </p>
                    </div>

                    <div class="article-search__price">
                        {{ formatPrice(article.ab_price) }}
                    </div>
                </div>
            </div>

        <Paginator
    v-if="articles.length > 0"
    :rows="5"
    :totalRecords="totalArticles"
    :first="(currentPage - 1) * 5"
    @page="pageChange"
    template="PrevPageLink  CurrentPageReport  NextPageLink"
    :currentPageReportTemplate="'     {currentPage} von {totalPages}     '"
    class="article-search__paginator"
/>

            <p v-if="search.length >= 3 && !loading && !error && articles.length === 0" class="article-search__no-result">
                Keine Treffer gefunden.
            </p>
        </div>
    </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import * as math from 'mathjs';
import Paginator from 'primevue/paginator'; //aufgabe 7 m5


const search = ref('');
const articles = ref([]);
const loading = ref(false);
const error = ref('');

const totalArticles = ref(0);
const currentPage = ref(1);
let searchTimeout = null;
let currentRequest = null;

async function fetchPage() {
    const keyword = search.value.trim();
    if (keyword.length > 0 && keyword.length < 3) return;

    // Cancel the older request so a slow response cannot overwrite new results.
    currentRequest?.abort();
    const controller = new AbortController();
    currentRequest = controller;
    loading.value = true;
    error.value = '';

    try {
        const response = await fetch(
            `/api/articles?search=${encodeURIComponent(keyword)}&page=${currentPage.value}`,
            { headers: { 'Accept': 'application/json' }, signal: controller.signal }
        );
        if (!response.ok) throw new Error('Fehler beim Laden der API');
        const result = await response.json();
        if (controller.signal.aborted) return;

        articles.value = result.success === true && Array.isArray(result.data)
            ? result.data : [];
        totalArticles.value = result.success === true ? Number(result.total) || 0 : 0;
    } catch (e) {
        if (e.name === 'AbortError') return;
        console.error(e);
        articles.value = [];
        totalArticles.value = 0;
        error.value = 'Fehler beim Laden der Artikel.';
    } finally {
        if (currentRequest === controller) {
            loading.value = false;
            currentRequest = null;
        }
    }
}

onMounted(fetchPage);
onBeforeUnmount(() => {
    clearTimeout(searchTimeout);
    currentRequest?.abort();
});

watch(search, (newValue) => {
    clearTimeout(searchTimeout);
    currentRequest?.abort();
    currentPage.value = 1;
    const keyword = newValue.trim();

    if (keyword.length > 0 && keyword.length < 3) {
        articles.value = [];
        totalArticles.value = 0;
        loading.value = false;
        error.value = '';
        return;
    }

    if (keyword.length === 0) {
        fetchPage(); // Restore the default listing when the search is cleared.
        return;
    }

    // Debounce: do not call the server after every keystroke.
    searchTimeout = setTimeout(fetchPage, 300);
});

function pageChange(event) {
    currentPage.value = event.page + 1;
    fetchPage();
}

function formatPrice(price) {
    const value = Number(price);

    if (Number.isNaN(value)) {
        return price + ' €';
    }

    const euroValue = math.divide(math.bignumber(value), 100);
    return Number(euroValue).toFixed(2).replace('.', ',') + ' €';
}
</script>

<style scoped lang="scss">
.article-search {
&__wrapper {
    min-height: 100vh;
    background: #f4f6f8;
    padding: 40px 20px;
    font-family: Arial, sans-serif;
}

&__card {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: 14px;
    padding: 30px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

&__title {
    margin: 0;
    font-size: 28px;
    color: #1f2937;
}

&__subtitle {
    margin-top: 8px;
    margin-bottom: 22px;
    color: #6b7280;
}

&__input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 16px;
    box-sizing: border-box;


    &:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}
}

&__result-list {
    margin-top: 22px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

&__result-item {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fafafa;

    h3 {
    margin: 0 0 6px 0;
    font-size: 18px;
    color: #111827;
}
}

&__description {
    margin: 0;
    color: #6b7280;
    font-size: 14px;
}

&__price {
    min-width: 90px;
    text-align: right;
    font-weight: bold;
    color: #047857;
    white-space: nowrap;
}

&__page {
    font-size: 16px;
    color: black;
    min-width: 60px;
    text-align: center;
    margin-top: 16px;
  }

&__error {
    color: #991b1b;

    &--visible {
      background: #fee2e2;
      padding: 12px;
      border-radius: 8px;
    }
  }

&__no-result {
    margin-top: 18px;
    color: #6b7280;
    background: #f3f4f6;
    padding: 12px;
    border-radius: 8px;
}
&__pagination {
    margin-top: 20px;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 24px;
}

&__button {
    padding: 8px 20px;
    border: 1px solid gray;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    font-size: 14px;
    min-width: 90px;


&:disabled {
    cursor: not-allowed;
}

&:not(:disabled):hover {
    background: #f3f4f6;
}
}

&__page {
    font-size: 16px;
    color: black;
    min-width: 60px;
    text-align: center;
    margin-top: 16px;
}
}
</style>
