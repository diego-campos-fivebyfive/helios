<template lang="pug">
  transition(name='fade')
    router-link.item(
      :to='item.link',
      :style='item.customStyle',
      :class='[{ "item-dropdown": itemDropdown }, sidebarType, platform]')
      Icon.icon-ui(
        :name='item.icon',
        :scale='platform == "mobile" ? 0.7 : 1')
      transition(name='fade')
        span(:style='labelPosition.top')
          | {{ item.name }}
      Badge.badge(
        v-if='item.content || item.contentAsync',
        :content='item.content',
        :contentAsync='item.contentAsync',
        labelType='warning')
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
      labelPosition: {},
      platform: process.env.PLATFORM !== 'web' ? 'mobile' : ''
    }),
    watch: {
      sidebarType() {}
    }
  }
</script>

<style lang="scss" scoped>
  $item-dropdown-x: 145px;
  $dropdown-border-size: 4px;

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

      &.common {
        padding: $ui-space-y/2 $ui-space-x/1.5 $ui-space-y/2 $ui-space-x*2;
      }
    }

    &.common {
      position: relative;
      white-space:nowrap;

      span {
        opacity: 1;
        transition: 0.2s;
      }
    }

    &.collapse:not(.item-dropdown) {
      position: relative;
      white-space:nowrap;

      span {
        opacity: 0;
        transition: 0.2s;
      }

      .icon-arrow {
        display: none;
      }
    }
  }

  .router-link-exact-active {
    background-color: $ui-gray-dark;
    box-shadow: inset $dropdown-border-size 0 0 0 $ui-orange-dark;
    color: $ui-white-regular;
    transition: 0.6s;
  }

  .icon-ui {
    margin-right: $ui-space-x/3;
    vertical-align: bottom;
    width: 1rem;
  }

  .badge {
    float: right;
    font-size: 0.75rem;
    line-height: 1.5em;
    margin-right: $ui-space-y * 1.5;
  }

  .mobile {
    .item {
      color: $ui-gray-medium;
      padding: $ui-space-x / 1.2;

      &:hover {
        background: $ui-gray-lighter;
      }

      svg {
        color: $ui-gray-medium;
      }
    }

    .router-link-exact-active {
      background-color: $ui-gray-lighter;
      color: $ui-gray-medium;
    }
  }

  .fade-enter-active {
    transition: 0.2s;
  }

  .fade-enter {
    opacity: 0;
  }
</style>
