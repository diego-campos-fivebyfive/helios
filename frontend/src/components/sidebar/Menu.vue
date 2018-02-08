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
      this.axios.get('application/menu').then(response => {
        this.menu = response.data
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
      background-color: $ui-gray-regular;
    }
  }

  .active {
    background-color: $ui-gray-regular;
    border-left: 5px solid $ui-blue-light;
  }
 </style>
