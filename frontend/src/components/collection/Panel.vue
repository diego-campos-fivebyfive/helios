<template lang="pug">
  .panel
    header
      slot(name='header')
    section
      slot(name='section')
    footer
      slot(name='footer')
</template>

<script>

</script>

<style lang="scss" scoped>
  .panel {
    background-color: $ui-white-regular;
    display: block;
    margin: $ui-space-y $ui-space-x/2;
    max-width: 100%;
  }

  header {
    border-top: $ui-space-y/5 solid $ui-divider-color;
    color: $ui-text-main;
    padding: $ui-space-y $ui-space-x/2;
    text-align: right;

    @include clearfix;

    * {
      display: inline;
      vertical-align: middle;
    }
  }

  .title {
    float: left;
    font-size: 1.75rem;
    font-weight: 300;
    text-align: left;

    .sub {
      display: block;
      font-size: 1rem;
      font-style: italic;
      font-weight: 400;
      padding-top: $ui-space-y/1.5;
    }
  }

  .info {
    font-size: 1.25rem;
    font-weight: 400;
    margin: 0 $ui-space-x;
    opacity: 0.8;
  }

  section {
    border-top: $ui-space-y/10 solid $ui-divider-color;
  }
</style>
