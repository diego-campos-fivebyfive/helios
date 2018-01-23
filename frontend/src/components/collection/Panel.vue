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
  $ui-main-text: #676a6c;
  $ui-main-split: #e7eaec;

  $ui-white-n: #ffffff;
  $ui-space-y: 15px;
  $ui-space-x: 25px;

  .panel {
    background-color: $ui-white-n;
    display: block;
    margin: $ui-space-y $ui-space-x/2;
    max-width: 100%;
  }

  header {
    border-top: $ui-space-y/5 solid $ui-main-split;
    color: $ui-main-text;
    padding: $ui-space-y $ui-space-x/2;
    text-align: right;

    * {
      display: inline;
    }
  }

  .title {
    float: left;
    font-size: 1.75rem;
    font-weight: 300;
    @include clearfix;
  }

  .info {
    font-size: 1.25rem;
    font-weight: 600;
    opacity: 0.8;
  }
</style>
