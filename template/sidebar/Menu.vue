<template lang="pug">
  ul.menu(:class='`sidebar-${sidebarType}`')
    li(v-for='itemMenu in menu', v-if='hasRoles(itemMenu)')
      Dropdown(
        v-if='itemMenu.dropdown',
        :sidebarType='sidebarType',
        :dropdown='itemMenu',
        :hasRoles='hasRoles')
      Item(
        v-else,
        :item='itemMenu',
        :itemDropdown='false',
        :sidebarType='sidebarType')
</template>

<script>
  import Item from './Item'
  import Dropdown from './Dropdown'
  import menuAdmin from './Admin'
  import menuAccount from './Account'

  export default {
    components: {
      Item,
      Dropdown
    },
    props: {
      sidebarType: {
        type: String,
        required: true
      }
    },
    data: () => ({
      menu: []
    }),
    computed: {
      user() {
        return this.$global.user
      }
    },
    watch: {
      sidebarType() {}
    },
    mounted() {
      this.setMenuType()
    },
    methods: {
      hasRoles(item) {
        if (item.allowedRoles === '*') {
          return true
        }

        const matchedRole = this.user.roles
          .find(role => item.allowedRoles
          .find(allowedRole => allowedRole === role))

        return Boolean(matchedRole)
      },
      setMenuType() {
        this.menu = this.user.sices
          ? menuAdmin
          : menuAccount
      }
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

  .sidebar-collapse {
    max-height: calc(100vh - #{$menu-head-collapse-y});
  }

  .sidebar-common {
    max-height: calc(100vh - #{$menu-head-common-y});
  }
 </style>
