<template lang="pug">
  .dropdown(
    :class='[{ "dropdown-active": open }, sidebarType]')
    button.toogle(type='button', v-on:click='toogle')
      Icon.icon-ui(:name='dropdown.icon')
      span {{ dropdown.name }}
      Icon.icon-arrow(v-show='open', name='angle-down')
      Icon.icon-arrow(v-show='!open', name='angle-left')
    ul(v-show='open')
      li(v-for='item in dropdown.subItems')
        Item(:item='item', :itemDropdown='true', :sidebarType='sidebarType')
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
      open: false
    }),
    methods: {
      toogle() {
        this.open = !this.open
      }
    }
  }
</script>

<style lang="scss" scoped>
  $dropdown-border-size: 4px;

  .dropdown {
    &.collapse {
      position: relative;

      span {
        display: none;
      }

      .icon-arrow {
        display: none;
      }

      ul {
        background-color: $ui-gray-dark;
        left: $ui-sidebar-collapse-x - $dropdown-border-size;
        position: absolute;
        top: 0;
      }
    }

    &.common {
      padding-bottom: $ui-space-y/2;
    }
  }

  .dropdown-active {
    background-color: $ui-gray-dark;
    border-left: $dropdown-border-size solid $ui-blue-light;
    color: $ui-white-regular;
  }

  .toogle {
    color: inherit;
    font-weight: inherit;
    padding: $ui-space-y $ui-space-x/1.5 $ui-space-y $ui-space-x;
    text-align: inherit;
    transition: all 300ms;
    width: 100%;

    &:hover {
      color: $ui-white-regular;
    }
  }

  .icon-ui {
    margin-right: $ui-space-x/3;
    vertical-align: bottom;
    width: 1rem;
  }

  .icon-arrow {
    float: right;
  }

  ul {
    list-style: none;
  }
</style>
