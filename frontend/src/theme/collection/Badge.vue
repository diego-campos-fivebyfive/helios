<template lang="pug">
  .collection-badge(:style='getBadgeBackground()')
    img.collection-badge-logo(
      src='~@/theme/assets/media/logo-inverse.png')
    .collection-badge-label
      | Sices
      span {{ $global.user.level }}
</template>

<script>
  import titaniumCover from '@/theme/assets/media/titanium.jpg'

  export default {
    methods: {
      getBadgeBackground() {
        const { level: userLevel } = this.$global.user

        const badgesBackground = {
          black: '#6d6d6d',
          partner: '#e7b042',
          platinum: '#c6c6c6',
          premium: '#a3bd21',
          titanium: `url(${titaniumCover})`
        }

        return `background: ${badgesBackground[userLevel]};`
      }
    }
  }
</script>

<style lang="scss" scoped>
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

      span {
        display: block;
        font-size: 1.25em;
      }
    }
  }

  @media (max-width: $ui-size-md){
    .collection-badge {
      display: none;
    }
  }
</style>
