<template lang="pug">
  .dropdown(
    :class='[{ "dropdown-active": open }, sidebarType]')
    button.dropdown-toogle(
      type='button',
      v-on:click='toogleList',
      v-on:mouseover='showLabel')
      Icon.icon-ui(:name='dropdown.icon')
      span.dropdown-toogle-label(:style='style.labelTopPosition')
        | {{ dropdown.name }}
      Icon.icon-arrow(v-show='open', name='angle-down')
      Icon.icon-arrow(v-show='!open', name='angle-left')
    ul(v-show='open', :style='style.listTopPosition')
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
      open: false,
      style: {
        labelTopPosition: '',
        listTopPosition: ''
      }
    }),
    methods: {
      updateElementPosition(event, element) {
        if (event.target.type === 'button') {
          const targetPosition = event.target.getBoundingClientRect()
          this.$set(this.style, element, `top: ${targetPosition.y}px`)
        }
      },
      toogleList(event) {
        this.updateElementPosition(event, 'listTopPosition')
        this.open = !this.open
      },
      showLabel(event) {
        this.updateElementPosition(event, 'labelTopPosition')
      }
    },
    watch: {
      sidebarType() {}
    }
  }
</script>

<style lang="scss" scoped>
  $dropdown-border-size: 4px;

  ul {
    list-style: none;
  }

  .icon-arrow {
    float: right;
  }

  .dropdown {
    &.collapse {
      position: relative;

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
          display: none; /*inline-block*/
        }
      }

      .icon-arrow {
        margin-right: -$ui-space-y/2;
        margin-top: $ui-space-y/10;
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
    border-left: $dropdown-border-size solid $ui-blue-light;
    color: $ui-white-regular;

    &.collapse {
      .icon-arrow {
        display: none;
      }
    }
  }

  .dropdown-toogle {
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
</style>
