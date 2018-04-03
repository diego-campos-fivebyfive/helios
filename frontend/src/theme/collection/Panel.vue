<template lang="pug">
  .collection-panel
    header.collection-panel-header
      slot(name='header')
    section.collection-panel-section
      slot(name='section')
    footer
      slot(name='footer')
</template>

<script>
  /* Panel Script */
</script>

<style lang="scss">
  .collection-panel {
    background-color: $ui-white-regular;
    color: $ui-text-main;
    display: block;
    float: left;
    margin: $ui-space-y $ui-space-x/2;
    width: calc(100% - #{$ui-space-x});
  }

  .collection-panel-header {
    border-top: $ui-space-y/5 solid $ui-divider-color;
    padding: $ui-space-y $ui-space-x/2;
    text-align: right;

    @include clearfix;

    * {
      vertical-align: middle;
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
      display: inline;
      font-size: 1.25rem;
      font-weight: 400;
      margin: 0 $ui-space-x;
      opacity: 0.8;
    }

    .menu {
      display: inline;
    }
  }

  .collection-panel-section {
    border-top: $ui-space-y/10 solid $ui-divider-color;
    padding: $ui-space-y $ui-space-x/2;
  }
</style>
