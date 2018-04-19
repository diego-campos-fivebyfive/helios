<template lang="pug">
  a.header(href='/')
    img(
      src='@/theme/assets/media/logo-small.png',
      alt='Sices Solar Logo')
    span.title {{ $global.user.name }}
    span {{ $global.user.company }}
</template>

<script>
  export default {
    data: () => ({
      user: {}
    })
  }
</script>

<style lang="scss" scoped>
  .header {
    background: url("~@/theme/assets/media/logo-cover.png");
    color: $ui-white-regular;
    display: block;
    text-align: center;
    padding: $ui-space-y/2 $ui-space-x;
  }

  .title {
    font-weight: 600;
  }

  span {
    display: block;
    padding: 2px;
  }
</style>
