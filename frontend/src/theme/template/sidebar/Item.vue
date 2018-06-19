<template lang="pug">
  router-link.item(
    :to='item.link',
    :style='item.customStyle',
    :class='[{\
      "item-dropdown": itemDropdown,\
      "item-active": item.active },\
      sidebarType]',
    v-on:click.native='forceReload',
    v-on:mouseover.native='setLabelPosition')
    Icon.icon-ui(:name='item.icon')
    span(:style='labelPosition.top') {{ item.name }}
    Icon.icon-arrow(name='angle-right')
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
    methods: {
      forceReload() {
        const getInitPath = Promise.resolve(this.$router.history.current.path)

        const redirectToDifferentPath = initPath => {
          this.$router.push({ path: '/' })
          return initPath
        }

        const redirectToInitPath = initPath => {
          this.$router.push({ path: initPath })
        }

        getInitPath
          .then(redirectToDifferentPath)
          .then(redirectToInitPath)
      },
      setLabelPosition(event) {
        if (event.target.href) {
          const targetPosition = event.target.getBoundingClientRect()
          this.$set(this.labelPosition, 'top', `top: ${targetPosition.y}px`)
        }
      }
    },
    watch: {
      sidebarType() {}
    }
  }
</script>

<style lang="scss" scoped>
  $item-dropdown-x: 145px;

  .icon-arrow {
    float: right;
  }

  .item {
    color: inherit;
    display: block;
    padding: $ui-space-y $ui-space-x/1.5 $ui-space-y $ui-space-x;
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

      &.common {
        padding: $ui-space-y/2 $ui-space-x/1.5 $ui-space-y/2 $ui-space-x*2;
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

        &:hover {
          span {
            display: inline-block;
          }
        }

        .icon-arrow {
          display: none;
        }
      }
    }
  }

  .item-active {
    background-color: $ui-gray-dark;
    border-left: $ui-space-x/6.25 solid $ui-blue-light;
    color: $ui-white-regular;
  }

  .icon-ui {
    margin-right: $ui-space-x/3;
    vertical-align: bottom;
    width: 1rem;
  }
</style>
