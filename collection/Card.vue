<template lang="pug">
  .collection-card
    figure.collection-card-image(v-if='$slots.image')
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
  /* Card Script */
</script>

<style lang="scss">
  $card-img-size: 180px;

  .collection-card {
    background-color: $ui-white-regular;
    border: 1px solid $ui-gray-light;
    color: $ui-text-main;
    display: block;
    padding: $ui-space-y / 8;

    .collection-card-image {
      img {
        height: 100%;
      }

      display: block;
      height: $card-img-size;
      text-align: center;
    }

    .collection-card-content {
      border-bottom: 1px dashed $ui-gray-light;
      margin: $ui-space-y $ui-space-x / 2;
      padding-bottom: $ui-space-y;
    }

    .collection-card-content-title {
      display: block;
      font-size:  1.25rem;
      font-weight: 600;
      padding-bottom: $ui-space-y / 2;
      width: 100%;
    }

    .collection-card-content-info {
      display: block;
      font-size: 0.9rem;
      padding: $ui-space-y / 2 0;
      width: 100%;
    }

    .collection-card-actions {
      display: block;
      margin: $ui-space-y 0;
      padding: $ui-space-y / 2 $ui-space-x / 4;
      text-align: center;
      width: 100%;
    }
  }
</style>
