<template>
    <div class="footer">
      <div class="footer__container">
        <div class="footer__title">
        <h2>Wir freuen uns auf Ihren Besuch!</h2>
      </div>

      <div class="footer__content">
        <p class="footer__text">(c) Abalo GmbH</p>
        <p class="footer__text">Vu Nguyen und Quan Nguyen</p>
        <a href="#" class="footer__link" @click.prevent="$emit('show-impressum')">
        Impressum
        </a>
      </div>
    </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'SiteFooter',
    emits: ['show-impressum']
  };
  </script>

<style scoped lang="scss">
$bg-color: #2f2f2f;
$text-color: white;
$hover-color: #ccc;


.footer {
  background-color: $bg-color;
  color: $text-color;
  text-align: center;
  font-family: Arial, sans-serif;
  padding: 15px 20px;
  margin-top: auto;

  &__container {
  display: flex;
  flex-direction: column;
  align-items: center;
  }

  &__title {
    margin: 0 0 8px 0;

    h2 {
      font-size: 16px;
      font-weight: bold;
    }
  }

  &__text {
    margin: 2px 0;
    font-size: 16px;
    font-weight: bold;
  }

  &__link {
    color: $text-color;
    text-decoration: underline;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;

    &:hover {
      color: $hover-color;
    }
  }
}
</style>