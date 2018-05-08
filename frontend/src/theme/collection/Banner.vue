<template lang="pug">
  .collection-banner(:class='type')
    Icon.collection-banner-icon(v-if='icon', :name='icon', scale='1.7')
    h2.collection-banner-title {{ title }}
    p.collection-banner-text {{ text }}
</template>

<script>
  export default {
    props: ['title', 'text', 'type', 'icon',]
  }
</script>

<style lang="scss" scoped>
  .collection-banner {
    border-radius: 0.5rem;
    color: inherit;
    font-size: 1.5rem;
    padding: $ui-space-x/2 $ui-space-y/2;
    position: relative;
    margin-top: $ui-space-y;
    margin-bottom: $ui-space-y/2;
    margin-left: $ui-space-x/2;
    margin-right: $ui-space-x/2;
    text-align: center;

    svg {
      vertical-align: sub;
    }

    .collection-banner-icon {
      margin-right: 0.5rem;
      display: inline-block;
    }

    .collection-banner-title {
      display: inline-block;
    }

    .collection-banner-text {
      padding: $ui-space-y/2;
    }

    &.info {
      background-color: $ui-blue-extra-lighter;
      border: 1px solid $ui-blue-lighter;
      color: $ui-blue-darken;
    }
  }
</style>
