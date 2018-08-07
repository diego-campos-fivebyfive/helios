<template lang="pug">
  .collection-badge(:style='getBadgeBackground()')
    img.collection-badge-logo(
      src='~theme/assets/media/logo-inverse.png')
    .collection-badge-label
      | Sices
      span.collection-badge-label-level
        | {{ level }}
</template>

<script>
  import titaniumCover from 'theme/assets/media/titanium.jpg'

  export default {
    props: {
      level: {
        type: String,
        required: true
      }
    },
    methods: {
      getBadgeBackground() {
        const badgesBackground = {
          black: '#6d6d6d',
          partner: '#e7b042',
          platinum: '#c6c6c6',
          premium: '#a3bd21',
          titanium: `url(${titaniumCover})`
        }

        return `background: ${badgesBackground[this.level]};`
      }
    }
  }
</script>

<style lang="scss">
  $badges-size: 28px;

  .collection-badge {
    border-radius: $ui-corner/2;
    display: inline-flex;
    font-size: $badges-size;
    height: 1.25em;
    padding: 0.25em;

    .collection-badge-logo {
      border-right: 0.03em solid $ui-white-regular;
      height: 100%;
      padding-right: $ui-space-x/8;
    }

    .collection-badge-label {
      color: $ui-white-regular;
      font-size: 0.3em;
      font-weight: 600;
      padding-left: $ui-space-x/8;
      text-align: left;
      text-transform: uppercase;

      .collection-badge-label-level {
        display: block;
        font-size: 1.25em;
      }
    }
  }
</style>
