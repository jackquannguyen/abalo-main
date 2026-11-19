<template>
    <div class="page">
      <SiteHeader />
      <SiteBody>
      <Impressum v-if="showImpressum" />
      <ArticleSearch v-else/> 
      <!-- Aufgabe 5 for ArticleSearch  -->
      </SiteBody>

      <SiteFooter @show-impressum="showImpressum = true" />
    </div>
</template>
  
<script setup>
import { ref } from 'vue'
const showImpressum = ref(false)
import SiteHeader from '../components/SiteHeader.vue'
import SiteBody from '../components/SiteBody.vue'
import SiteFooter from '../components/SiteFooter.vue'
import Impressum from '../components/Impressum.vue'
import ArticleSearch from '../components/ArticleSearch.vue'
</script>

<style scoped>
.page {
  display: flex;
  flex-direction: column;
}
</style>