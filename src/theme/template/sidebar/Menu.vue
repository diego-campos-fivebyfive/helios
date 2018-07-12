<template lang="pug">
  ul.menu(:class='sidebarType')
    li(v-for='itemMenu in menu')
      Dropdown(
        v-if='itemMenu.subItems',
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
  import menuAdmin from './menuMapAdmin'
  import menuAccount from './menuMapAccount'

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
      this.menu = this.$global.user.sices
      ? menuAdmin
      : menuAccount
    },
    watch: {
      sidebarType() {}
    }
  }
</script>

<style lang="scss" scoped>
  $menu-head-common-y: 120px;
  $menu-head-collapse-y: 45px;

  ul {
    list-style: none;
    margin-right: - $ui-space-x/1.5;
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

  .collapse {
    max-height: calc(100vh - #{$menu-head-collapse-y})
  }

  .common {
    max-height: calc(100vh - #{$menu-head-common-y});
  }
 </style>
