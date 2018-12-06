<template lang="pug">
  .dropdown(
    :class='[{ "dropdown-active": dropdownActived }, sidebarType, platform]',
    v-on:mouseleave='closeDropdown')
    button.dropdown-toogle(
      type='button',
      v-on:click='openCommonDropdown',
      v-on:mouseover='openCollapseDropdown')
      Icon.icon-ui(:name='dropdown.icon')
      span.dropdown-toogle-label(:style='style.labelTopPosition')
        | {{ dropdown.name }}
      Icon.icon-arrow(v-show='showDropdown()', name='angle-down')
      Icon.icon-arrow(v-show='hideDropdown()', name='angle-left')
    ul(v-show='showDropdown()', :style='style.listTopPosition')
      li(v-for='item in dropdown.subItems')
        Item(
          :item='item',
          :itemDropdown='true',
          :sidebarType='sidebarType')
</template>

<script>
  import Item from './Item'

  export default {
    components: {
      Item
    },
    props: {
      dropdown: {
        type: Object,
        required: true
      },
      sidebarType: {
        type: String,
        required: true
      }
    },
    data: () => ({
      dropdownActived: false,
      style: {
        labelTopPosition: '',
        listTopPosition: ''
      },
      platform: process.env.PLATFORM !== 'web' ? 'mobile' : ''
    }),
    watch: {
      sidebarType() {
        this.hideDropdownOnCollapse()
      }
    },
    methods: {
      closeDropdown() {
        if (this.sidebarType === 'collapse') {
          this.dropdownActived = !this.dropdownActived
        }
      },
      hideDropdownOnCollapse() {
        if (this.sidebarType === 'collapse') {
          this.dropdownActived = false
        }
      },
      updateElementPosition(event, element) {
        if (event.target.type === 'button') {
          const targetPosition = event.target.getBoundingClientRect()
          this.$set(this.style, element, `top: ${targetPosition.y}px`)

          const ulElement = event.target.parentNode.querySelector('ul')
          ulElement.style.display = 'block'
          const ulHeight = ulElement.getBoundingClientRect().height
          ulElement.style.display = 'none'

          const listPosition = ulHeight - targetPosition.height
          const topPosition = targetPosition.y - listPosition

          if (ulHeight + targetPosition.y > document.body.clientHeight) {
            this.$set(this.style, element, `top: ${topPosition}px`)
          }
        }
      },
      openCommonDropdown(event) {
        if (this.sidebarType === 'common') {
          this.toogleList(event)
        }
      },
      showDropdown() {
        return this.dropdownActived
      },
      toogleList(event) {
        this.updateElementPosition(event, 'listTopPosition')
        this.dropdownActived = !this.dropdownActived
      },
      openCollapseDropdown(event) {
        if (
          this.sidebarType === 'collapse'
          && event.target.type === 'button'
        ) {
          this.toogleList(event)
        }
      },
      hideDropdown() {
        return !this.dropdownActived
      }
    }
  }
</script>

<style lang="scss" scoped>
  $dropdown-border-size: 4px;

  ul {
    list-style: none;
  }

  .icon-arrow {
    left: $ui-sidebar-common-x - $ui-space-x;
    position: absolute;
  }

  .dropdown-toogle {
    color: inherit;
    font-weight: inherit;
    padding: ($ui-space-y $ui-space-x / 1.5) $ui-space-y $ui-space-x;
    text-align: inherit;
    width: 100%;
    white-space:nowrap;

    &:hover {
      color: $ui-white-regular;
    }
  }

  .dropdown {
    position: relative;

    &.collapse {
      .dropdown-toogle {
        position: relative;

        &:before {
          content: "";
          height: 100%;
          left: 0;
          position: absolute;
          top: 0;
          width: 100%;
        }
      }

      .dropdown-toogle-label {
        background-color: $ui-gray-darken;
        display: none;
        left: $ui-sidebar-collapse-x;
        padding: $ui-space-y + $ui-space-y/6 $ui-space-x;
        position: fixed;
        top: 0;
        white-space: nowrap;
      }

      &:hover {
        .dropdown-toogle-label {
          display: none;
        }
      }

      .icon-arrow {
        display: none;
      }

      ul {
        background-color: $ui-gray-dark;
        left: $ui-sidebar-collapse-x;
        position: fixed;
        top: 0;
      }
    }
  }

  .dropdown-active {
    background-color: $ui-gray-dark;
    box-shadow: inset $dropdown-border-size 0 0 0 $ui-orange-light;
    color: $ui-white-regular;
    transition: 0.6s;

    &.collapse {
      .icon-arrow {
        display: none;
      }
    }
  }

  .icon-ui {
    margin-right: $ui-space-x/3;
    vertical-align: bottom;
    width: 1rem;
  }

  .mobile {
    .dropdown {
      ul {
        li {
          padding: $ui-corner 0;
        }
      }

      .dropdown-toogle {
        color: $ui-gray-medium;
        padding: $ui-space-x / 1.2;
      }

      &:hover {
        background: $ui-gray-lighter;
      }

      svg {
        color: $ui-gray-medium;
      }

      .router-link-exact-active {
        background-color: $ui-gray-lighter;
        color: $ui-gray-medium;
      }
    }

    .dropdown-active {
      background-color: $ui-gray-light;

      &.collapse {
        .icon-arrow {
          display: none;
        }
      }
    }
  }
</style>
