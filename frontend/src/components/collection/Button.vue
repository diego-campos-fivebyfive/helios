<template lang="pug">
  a(v-if='link', :href='link', :class='[type, pos, { labeled: label }]')
    icon(:name='icon')
    | {{ label }}
  button(v-else, :class='[type, pos, { labeled: label }]')
    icon(:name='icon')
    | {{ label }}
</template>

<script>
  export default {
    props: ['type', 'icon', 'link', 'label', 'pos']
  }
</script>

<style lang="scss" scoped>
  a,
  button {
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
