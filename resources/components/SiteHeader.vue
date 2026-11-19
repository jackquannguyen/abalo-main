<template>
    <header>
  
      <nav id="navigation">
        <ul class="menu">
          <li v-for="item in menuList" :key="item.title">
  
            <template v-if="item.children">
              <a href="#" @click.prevent="toggleSubmenu(item.title)">
                {{ item.title }} {{ openSubmenu === item.title ? '▲' : '▼' }}
              </a>
              <ul class="submenu" :class="{ show: openSubmenu === item.title }">
                <li v-for="child in item.children" :key="child.title">
                  <a :href="child.link">{{ child.title }}</a>
                </li>
              </ul>
            </template>
  
            <template v-else>
              <a :href="item.link">{{ item.title }}</a>
            </template>
  
          </li>
        </ul>
      </nav>
    </header>
  </template>
  
  <script>
  export default {
    name: 'SiteHeader',
    data() {
      return {
        openSubmenu: null,
        menuList: [
          { title: "Home", link: "/" },
          { title: "Kategorien", link: "/articles" },
          { title: "Verkaufen", link: "/newarticle" },
          {
            title: "Unternehmen",
            children: [
              { title: "Philosophie", link: "/philosophie" },
              { title: "Karriere", link: "/karriere" }
            ]
          }
        ]
      };
    },
    methods: {
      toggleSubmenu(title) {
        this.openSubmenu = this.openSubmenu === title ? null : title;
      }
    }
  };
  </script>