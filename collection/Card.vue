<template lang="pug">
  .collection-card
    figure.collection-card-img
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
    display: block;
    border: 0.03em solid $ui-gray-light;
    color: $ui-text-main;
    padding: 0.05em;

    .collection-card-content {
      margin: $ui-space-y $ui-space-x / 2;
      border-bottom: 1px dashed $ui-gray-regular;
      padding-bottom: $ui-space-y;
    }

    .collection-card-img {
      display: block;
      background-color: $ui-gray-lighter;
      height: $card-img-size;
      width: 100%;
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
    //  border: inset $ui-gray-darken 0.03em;
      margin: $ui-space-y 0;
      padding: $ui-space-y / 2 $ui-space-x / 4;
      width: 100%;
    }

    li {
      margin-top: 5px;
    }

    ul {
      list-style: none;
    }

  }
</style>
