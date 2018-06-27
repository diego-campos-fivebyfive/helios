<template lang="pug">
  router-link.header(to='/', :class='sidebarType')
    img(
      src='@/theme/assets/media/logo-small.png',
      alt='Sices Solar Logo')
    .level {{ $global.user.level }}
    .header-info(:class='sidebarType')
      span.title {{ $global.user.name }}
      span {{ $global.user.company }}
</template>

<script>
  export default {
    data: () => ({
      user: {}
    }),
    props: {
      sidebarType: {
        type: String,
        required: true
      }
    },
    watch: {
      sidebarType() {}
    }
  }
</script>

<style lang="scss" scoped>
  $img-width: 60px;

  .header {
    background: url("~@/theme/assets/media/logo-cover.png");
    color: $ui-white-regular;
    display: block;
    text-align: center;

    .level {
      display: none;
    }

    &.collapse {
      img {
        max-width: $img-width;
        padding: $ui-space-y;
      }
    }

    &.common {
      padding: $ui-space-y/2 $ui-space-x;
    }
  }

  .header-info {
    &.collapse {
      display: none;
    }

    .title {
      font-weight: 600;
    }

    span {
      display: block;
      padding: 2px;
    }
  }
  @media (max-width: $ui-size-md){
    .header {
      &.collapse{
      background: #e7b042;
      padding: $ui-space-y/1.75 0;
        .level {
          display: block;
          font-weight: 600;
          font-size: 0.75em;
          text-transform: uppercase;
        }
        img {
          padding: 0 $ui-space-x/1.75;
        }
      }
    }
  }
</style>
