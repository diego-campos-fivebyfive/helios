<template lang="pug">
  ul.menu(:class='`sidebar-${sidebarType}`')
    li(
      :class='platform',
      v-for='itemMenu in menu')
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
  import menuMap from '@/../theme/menu'

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
      menu: [],
      platform: process.env.PLATFORM !== 'web' ? 'mobile' : ''

    }),
    watch: {
      sidebarType() {}
    },
    mounted() {
      this.setMenu()
    },
    methods: {
      hasAccess(allowedRoles, userRoles) {
        if (!allowedRoles) {
          throw new Error('allowedRoles not defined')
        }

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
        const userRoles = JSON.parse(localStorage.getItem('userRoles'))
        this.menu = this.serializeMenu(menuMap, userRoles)
      }
    }
  }
</script>

<style lang="scss" scoped>
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
    max-height: calc(100vh - #{$ui-sidebar-head-collapse-y});
  }

  .sidebar-common {
    max-height: calc(100vh - #{$ui-sidebar-head-common-y});
  }

  @media screen and (max-width: $ui-size-md) {
    .sidebar-common {
      max-height: calc(100vh - #{$ui-mainbar-mobile-y});
    }
  }

  .mobile {
    box-shadow: 0 1px 0 $ui-gray-lighter;
  }
 </style>
