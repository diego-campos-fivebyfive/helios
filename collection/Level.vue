<template lang="pug">
  .collection-level(:class='label')
    img.collection-level-logo(
      src='~theme/assets/media/logo-inverse.png')
    .collection-level-text
      span.collection-level-text-title
        | Sices
      span.collection-level-text-label
        | {{ label }}
</template>

<script>
  import titaniumCover from 'theme/assets/media/titanium.jpg'

  export default {
    props: {
      label: {
        type: String,
        required: true
      }
    }
  }
</script>

<style lang="scss" scoped>
  $levels-size: 28px;

  @each $label, $color in $level-colors {
    .#{$label} {
      background: $color;
    }
  }

  .collection-level {
    border-radius: $ui-corner / 2;
    font-size: $levels-size;
    height: 1.25em;
    padding: 0.25em;
    text-align: left;

    .collection-level-logo {
      border-right: 0.03em solid $ui-white-regular;
      float: left;
      height: 100%;
      padding-right: $ui-space-x / 8;
    }

    .collection-level-text {
      color: $ui-white-regular;
      float: right;
      font-weight: 600;
      padding-left: $ui-space-x / 8;
      text-transform: uppercase;
    }

    .collection-level-text-title {
      display: block;
      font-size: 0.3em;
    }

    .collection-level-text-label {
      display: block;
      font-size: 0.4em;
    }
  }
</style>
