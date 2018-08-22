<template lang="pug">
  .collection-card
    figure.collection-card-image
      slot(name='image')
    .collection-card-content
      h3.collection-card-content-title
        slot(name='title')
      .collection-card-content-info
        slot(name='info')
    nav.collection-card-actions
      slot(name='actions')
</template>

<script>
  export default {

  }
</script>

<style lang="scss">
  $card-img-size: 180px;
  $font-title-size: 16px;

  .collection-card {
    background-color: $ui-white-regular;
    border: 0.03em solid $ui-gray-light;
    color: $ui-text-main;
    display: block;
    padding: 0.05em;

    .collection-card-image {
      img {
        height: 100%;
      }

      background-color: $ui-gray-lighter;
      display: block;
      height: $card-img-size;
      margin: auto;
      text-align: center;
    }

    .collection-card-content {
      border-bottom: 1px dashed $ui-gray-regular;
      margin: $ui-space-y $ui-space-x / 2;
      padding-bottom: $ui-space-y;
    }

    .collection-card-content-title {
      display: block;
      font-size: $font-title-size;
      font-weight: 600;
      padding-bottom: $ui-space-y / 2;
      width: 100%;
    }

    .collection-card-content-info {
      display: block;
      font-size: 85%;
      padding: $ui-space-y / 2 0;
      width: 100%;
    }

    .collection-card-actions {
      display: block;
      margin: $ui-space-y 0;
      padding: $ui-space-y / 2 $ui-space-x / 4;
      width: 100%;
    }

    li {
      margin-top: 5px;
    }

  }
</style>
