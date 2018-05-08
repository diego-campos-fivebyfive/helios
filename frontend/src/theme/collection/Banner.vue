<template lang="pug">
  .collection-banner(:class='type')
    Icon.collection-banner-icon(v-if='icon', :name='icon', scale='1.75')
    h2.collection-banner-title {{ title }}
    p.collection-banner-text {{ message }}
</template>

<script>
  export default {
    props: [
      'title',
      'message',
      'type',
      'icon']
  }
</script>

<style lang="scss" scoped>
  $banner-blue-bg: #d9edf7;

  .collection-banner {
    border-radius: 0.5rem;
    color: inherit;
    font-size: 1.5rem;
    padding: $ui-space-y/2;
    position: relative;
    margin: $ui-space-y;
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
      background-color: $banner-blue-bg;
      border: 1px solid $ui-blue-lighter;
      color: $ui-blue-darken;
    }
  }
</style>
