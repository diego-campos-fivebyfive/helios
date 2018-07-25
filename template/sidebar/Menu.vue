<template lang="pug">
  ul.menu(:class='`sidebar-${sidebarType}`')
    li(v-for='itemMenu in menu')
      Dropdown(
        v-if='itemMenu.dropdown',
        :sidebarType='sidebarType',
        :dropdown='itemMenu')
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
      this.setMenu()
    },
    methods: {
      hasAccess(menuItem) {
        if (menuItem.allowedRoles === '*') {
          return true
        }

        return menuItem.allowedRoles.some(allowedRole => (
          this.user.roles.some(userRole => (
            userRole === allowedRole
          ))
        ))
      },
      serializeMenu(menu) {
        const serializeNode = node =>
          Object.entries(node)
            .reduce((acc, [menuItemName, menuItem]) => {
              if (menuItem.dropdown) {
                const subItems = serializeNode(menuItem.subItems)

                if (Object.keys(subItems).length) {
                  acc[menuItemName] = Object.assign(menuItem, {
                    subItems
                  })
                }

                return acc
              }

              if (this.hasAccess(menuItem)) {
                acc[menuItemName] = menuItem
                return acc
              }

              return acc
            }, {})

        return serializeNode(menu)
      },
      setMenu() {
        // promise user
        const menuMap = this.user.sices
          ? menuAdmin
          : menuAccount

        this.menu = this.serializeMenu(menuMap)
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
