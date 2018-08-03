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
  import menuMap from '@/app/theme/menu'

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
    watch: {
      sidebarType() {}
    },
    mounted() {
      this.setMenu()
    },
    methods: {
      hasAccess(allowedRoles, userRoles) {
        if (allowedRoles === '*') {
          return true
        }

        return allowedRoles.some(allowedRole => (
          userRoles.some(userRole => (
            userRole === allowedRole
          ))
        ))
      },
      serializeMenu(menu, userRoles) {
        const serializeNode = node =>
          Object.entries(node)
            .reduce((acc, [menuItemName, menuItem]) => {
              if (menuItem.dropdown) {
                const subItems = serializeNode(menuItem.subItems)
                if (
                  Object.keys(subItems).length
                  && this.hasAccess(menuItem.allowedRoles, userRoles)
                ) {
                  acc[menuItemName] = Object.assign(menuItem, {
                    subItems
                  })
                }

                return acc
              }

              if (this.hasAccess(menuItem.allowedRoles, userRoles)) {
                acc[menuItemName] = menuItem
                return acc
              }

              return acc
            }, {})

        return serializeNode(menu)
      },
      setMenu() {
        window.$global.getUser
          .then(user => {
            this.menu = this.serializeMenu(menuMap, user.roles)
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
  $menu-head-common-y: 120px;
  $menu-head-collapse-y: 62px;

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
