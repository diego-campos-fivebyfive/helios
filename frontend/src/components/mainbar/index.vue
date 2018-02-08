<template lang="pug">
  header.bar
    h1.title Inversores
    h2.info
      | quinta-feira, 8 de fevereiro de 2018
    nav.menu
      Button(
        type='default-common',
        icon='sign-out',
        link='/logout',
        label='Sair',
        pos='first')
</template>

<script>

  export default {

  }
</script>

<style lang="scss" scoped>
  .bar {
    background-color: $ui-white-regular;
    border-bottom: 1px solid $ui-divider-color;
    color: $ui-text-main;
    display: block;
    padding: $ui-space-y $ui-space-x;
    text-align: right;
    width: 100%;

    @include clearfix;

    * {
      vertical-align: middle;
    }
  }

  .title {
    display: inline-block;
    float: left;
    font-size: 2rem;
    font-weight: 300;
    text-align: left;
  }

  .info {
    display: inline-block;
    font-size: 1rem;
    font-weight: 400;
    margin: $ui-space-y/3 $ui-space-x;
    opacity: 0.8;
  }

  .menu {
    display: inline-block;
  }
</style>
