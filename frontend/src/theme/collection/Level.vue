<template lang="pug">
  .collection-level(:style='getLevelBackground()')
    img.collection-level-logo(
      src='~@/theme/assets/media/logo-inverse.png')
    .collection-level-label
      .label-level Sices
      .label-level {{ $global.user.level }}
</template>

<script>
  import titaniumCover from '@/theme/assets/media/titanium.jpg'

  export default {
    methods: {
      getLevelBackground() {
        const { level: userLevel } = this.$global.user
        const levelsBackground = {
          black: '#6d6d6d',
          partner: '#e7b042',
          platinum: '#c6c6c6',
          premium: '#a3bd21',
          titanium: `url(${titaniumCover})`
        }

        return `background: ${levelsBackground[userLevel]};`
      }
    }
  }
</script>

<style lang="scss" scoped>
  $level-size: 28px;

  .collection-level {
    border-radius: $ui-corner/2;
    display: inline-flex;
    font-size: $level-size;
    height: 1.25em;
    padding: 0.25em;

    .collection-level-logo {
      border-right: 0.03em solid $ui-white-regular;
      height: 100%;
      padding-right: $ui-space-x/8;
    }

    .collection-level-label {
      padding-left: $ui-space-x/8;

      .label-level {
        color: $ui-white-regular;
        display: block;
        font-size: 0.3em;
        font-weight: 600;
        text-align: left;
        text-transform: uppercase;

        &:last-child {
          font-size: 0.35em;
        }
      }
    }

  }
</style>
