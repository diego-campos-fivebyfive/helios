<template lang="pug">
  ul.menu
    li(v-for='itemMenu in menu')
      Dropdown(
        v-if='itemMenu.dropdown',
        :dropdown='itemMenu',
        :sidebarType='sidebarType')
      Item(
        v-else,
        :item='itemMenu',
        :itemDropdown='false',
        :sidebarType='sidebarType')
</template>

<script>
  import Item from './Item'
  import Dropdown from './Dropdown'

  export default {
    components: {
      Item,
      Dropdown
    },
    data: () => ({
      menu: []
    }),
    computed: {
      sidebarType() {
        return this.$route.meta.sidebar || 'common'
      }
    },
    mounted() {
      this.axios.get('api/v1/application/menu').then(({ data }) => {
        const currentRoute = this.$router.history.current.path

        this.menu = Object.entries(data)
          .map(([itemMenuKey, itemMenu]) => (
            Object.assign(itemMenu, {
              active: currentRoute === itemMenu.link,
              key: itemMenuKey
            })
          ))
      })
    }
  }
</script>

<style lang="scss" scoped>
  ul {
    list-style: none;
  }

  li {
    color: $ui-sidebar-color;
    display: block;
    font-weight: 600;
    text-align: left;
    width: 100%;

    &:hover {
      background-color: $ui-gray-dark;
    }
  }

  .active {
    background-color: $ui-gray-dark;
    border-left: 5px solid $ui-blue-light;
  }
 </style>
