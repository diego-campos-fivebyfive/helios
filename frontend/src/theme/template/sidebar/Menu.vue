<template lang="pug">
  ul.menu
    li(v-for='item in menu')
      Dropdown(v-if='item.dropdown', :item='item')
      Item(v-else, :item='item')
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
    mounted() {
      this.axios.get('api/v1/application/menu').then(({ data }) => {
        const currentRoute = this.$router.history.current.path

        this.menu = Object.entries(data)
          .map(([name, item]) => (
            Object.assign(item, {
              active: currentRoute === item.link,
              name
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
