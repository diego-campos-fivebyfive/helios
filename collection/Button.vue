<template lang="pug">
  router-link.collection-button(
    v-if='link',
    :to='link',
    :title='title',
    :class='[pos, { labeled: label }]')
    slot
    | {{ label }}
  a.collection-button(
    v-else-if='redirect',
    :href='redirect',
    :title='title'
    :class='[pos, { labeled: label }]')
    slot
    | {{ label }}
  button.collection-button(
    v-else,
    type='button',
    v-on:click='action',
    :title='title'
    :class='[pos, { labeled: label }]')
    slot
    | {{ label }}
</template>

<script>
  export default {
    props: {
      action: {
        type: Function,
        required: false,
        default: () => {}
      },
      pos: {
        type: String,
        required: false,
        default: 'single'
      },
      redirect: {
        type: String,
        required: false
      },
      label: {
        type: String,
        required: false
      },
      title: {
        type: String,
        required: false
      },
      link: {
        type: String,
        required: false
      },
      type: {
        type: String,
        required: false,
        default: 'common'
      }
    }
  }
</script>

<style lang="scss">
  .collection-button {
    display: inline-block;
    font-size: 1.1rem;
    padding: $ui-space-y/2 $ui-space-x/2;
    vertical-align: middle;

    svg {
      vertical-align: sub;
    }

    &.primary-common {
      background-color: $ui-blue-light;
      color: $ui-white-regular;

      &:hover {
        background-color: $ui-blue-dark;
      }

      &:active {
        box-shadow: inset 3px 3px 3px $ui-blue-darken;
      }
    }

    &.primary-bordered {
      background-color: $ui-blue-light;
      color: $ui-white-regular;
      border: 1px solid $ui-blue-light;

      &:hover {
        background-color: $ui-blue-dark;
      }

      &:active {
        box-shadow: inset 3px 3px 3px $ui-blue-darken;
      }
    }

    &.primary-strong {
      background-color: $ui-blue-dark;
      color: $ui-white-regular;

      &:hover {
        background-color: $ui-blue-darken;
      }

      &:active {
        box-shadow:
          inset 1px 1px 4px $ui-blue-darken,
          inset -1px -1px 4px $ui-blue-darken;
      }
    }

    &.danger-common {
      background-color: $ui-red-lighter;
      color: $ui-white-regular;

      &:hover {
        background-color: $ui-red-light;
      }

      &:active {
        box-shadow:
          inset 1px 1px 4px $ui-red-dark,
          inset -1px -1px 4px $ui-red-dark;
      }
    }

    &.danger-bordered {
      background-color: $ui-red-lighter;
      color: $ui-white-regular;
      border: 1px solid $ui-red-light;

      &:hover {
        background-color: $ui-red-light;
      }

      &:active {
        box-shadow:
          inset 1px 1px 4px $ui-red-dark,
          inset -1px -1px 4px $ui-red-dark;
      }
    }

    &.default-common {
      background-color: $ui-white-regular;
      color: $ui-gray-regular;
    }

    &.default-bordered {
      background-color: $ui-white-regular;
      border: 1px solid $ui-gray-light;
      color: $ui-text-main;

      &:hover {
        background-color: $ui-gray-light;
        border-color: $ui-gray-light;
      }

      &:active {
        box-shadow:
          inset 1px 1px 4px $ui-gray-regular,
          inset -1px -1px 4px $ui-gray-regular;
      }
    }

    &.labeled {
      svg {
        margin-right: $ui-space-x/3;
      }
    }

    &.first {
      border-top-left-radius: $ui-corner;
      border-bottom-left-radius: $ui-corner;
    }

    &.middle {
      border-radius: 0;
    }

    &.last {
      border-top-right-radius: $ui-corner;
      border-bottom-right-radius: $ui-corner;
    }

    &.small {
      padding: $ui-corner * 2 $ui-corner * 3;
      font-size: .850rem;
      line-height: $ui-corner / 2;

      svg {
        margin-right: $ui-corner;
      }
    }

    &.single {
      border-radius: $ui-corner;
    }
  }
</style>
