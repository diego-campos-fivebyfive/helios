<template lang="pug">
  router-link.item(
    :to='item.link',
    :style='item.customStyle',
    :class='[{ "item-dropdown": itemDropdown }, sidebarType]')
    Icon.icon-ui(:name='item.icon')
    span(:style='labelPosition.top')
      | {{ item.name }}
    Badge(v-if='item.badge', :badge='item.badge')
</template>

<script>
  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      itemDropdown: {
        type: Boolean,
        required: true
      },
      sidebarType: {
        type: String,
        required: true
      }
    },
    data: () => ({
      labelPosition: {}
    }),
    watch: {
      sidebarType() {}
    }
  }
</script>

<style lang="scss" scoped>
  $item-dropdown-x: 145px;

  .icon-arrow {
    left: $ui-sidebar-common-x - $ui-space-x;
    position: absolute;
    top: $ui-space-y;
  }

  .item {
    color: inherit;
    display: block;
    padding: $ui-space-y $ui-space-x/1.5 $ui-space-y $ui-space-x;
    position: relative;
    transition: all 300ms;
    width: 100%;

    &:hover {
      color: $ui-white-regular;
    }

    &.item-dropdown {
      color: $ui-sidebar-color;

      &.collapse {
        min-width: $item-dropdown-x;

        &:hover {
          background-color: $ui-gray-darken;
        }
      }

      .icon-arrow {
        display: none;
      }

      &:hover {
        color: $ui-white-regular;
      }

      &.common, &.mobile {
        padding: $ui-space-y / 2 $ui-space-x / 1.5 $ui-space-y / 2 $ui-space-x * 2;
      }
    }

    &:not(.item-dropdown) {
      &.collapse {
        position: relative;

        span {
          background-color: $ui-gray-darken;
          display: none;
          left: $ui-sidebar-collapse-x;
          padding: $ui-space-y+$ui-space-y/9 $ui-space-x;
          position: fixed;
          top: 0;
          white-space: nowrap;
        }

        .icon-arrow {
          display: none;
        }
      }
    }
  }

  .router-link-exact-active {
    background-color: $ui-gray-dark;
    border-left: $ui-space-x / 6.25 solid $ui-blue-light;
    color: $ui-white-regular;
  }

  .icon-ui {
    margin-right: $ui-space-x/3;
    vertical-align: bottom;
    width: 1rem;
  }
</style>
