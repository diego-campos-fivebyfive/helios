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
    props: {
      sidebarType: {
        type: String,
        required: true
      }
    },
    mounted() {
      const uri = 'api/v1/application/menu'
      this.axios.get(uri).then(({ data }) => this.menu = data)
    },
    watch: {
      sidebarType() {}
    }
  }
</script>

<style lang="scss" scoped>
  $menu-head-height: 120px;

  ul {
    list-style: none;
    margin-right: - $ui-space-x/1.5;
    max-height: calc(100vh - #{$menu-head-height});
    overflow-y: scroll;
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
