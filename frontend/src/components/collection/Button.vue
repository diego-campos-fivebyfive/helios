<template lang="pug">
  router-link.button(
    v-if='link && !refresh',
    :to='link',
    :class='[type, pos, { labeled: label }]')
    Icon(v-if='icon', :name='icon')
    | {{ label }}
  a.button(
    v-else-if='link && refresh',
    :href='link',
    :class='[type, pos, { labeled: label }]')
    Icon(v-if='icon', :name='icon')
    | {{ label }}
  button.button(
    v-else,
    type='button',
    :class='[type, pos, { labeled: label }]')
    Icon(v-if='icon', :name='icon')
    | {{ label }}
</template>

<script>
  export default {
    props: ['type', 'icon', 'link', 'refresh', 'label', 'pos']
  }
</script>

<style lang="scss" scoped>
  .button {
    display: inline-block;
    font-size: 1.1rem;
    padding: $ui-space-y/2 $ui-space-x/2;
    vertical-align: middle;
  }

  svg {
    vertical-align: sub;
  }

  .primary-common {
    background-color: $ui-blue-light;
    color: $ui-white-regular;
  }

  .primary-strong {
    background-color: $ui-blue-dark;
    color: $ui-white-regular;
  }

  .danger-common {
    background-color: $ui-red-lighter;
    color: $ui-white-regular;
  }

  .default-common {
      background-color: $ui-white-regular;
      color: $ui-gray-regular;
  }

  .default-bordered {
      background-color: $ui-white-regular;
      border: 1px solid $ui-gray-light;
      color: $ui-text-main;
  }

  .labeled {
    svg {
      margin-right: $ui-space-x/3;
    }
  }

  .first {
    border-top-left-radius: $ui-corner;
    border-bottom-left-radius: $ui-corner;
  }

  .middle {
    border-radius: 0;
  }

  .last {
    border-top-right-radius: $ui-corner;
    border-bottom-right-radius: $ui-corner;
  }

  .single {
    border-radius: $ui-corner;
  }
</style>
