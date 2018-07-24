<template lang="pug">
  .collection-banner(:class='type')
    Icon.collection-banner-icon(v-if='icon', :name='icon', scale='1.75')
    h2.collection-banner-title {{ title }}
    p.collection-banner-text {{ message }}
</template>

<script>
  export default {
    props: {
      title: {
        type: String,
        required: true
      },
      message: {
        type: String,
        required: true
      },
      icon: {
        type: String,
        required: true
      },
      type: {
        type: String,
        required: true
      }
    }
  }
</script>

<style lang="scss" scoped>
  .collection-banner {
    background-color: $ui-orange-light;
    border-radius: 0.5rem;
    color: $ui-white-regular;
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

    &.warning {
      background-color: $ui-orange-light;
      color: $ui-white-regular;
    }

    &.info {
      background-color: $ui-blue-lighter;
      color: $ui-blue-darken;
    }

    &.default {
      background-color: $ui-white-regular;
      color: $ui-gray-dark;
    }

    &.danger {
      background-color: $ui-red-light;
      color: $ui-white-regular;
    }
  }
</style>
