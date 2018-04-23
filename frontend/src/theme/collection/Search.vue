<template lang="pug">
  form.collection-search
    input.collection-search-input
    Button.collection-search-button(
      type='primary-common',
      label='Search',
      pos='last')

</template>

<script>
  import Button from '@/theme/collection/Button'

  export default {
    components: {
      Button
    }
  }
</script>

<style lang="scss" scoped>
  .collection-search {
    height: 2.25rem;
    width: 20rem;
    position: relative;
    display: inline-block;

    .collection-search-input {
      background-color: $ui-white-regular;
      background-image: none;
      border: 1px solid $ui-blue-light;
      color: inherit;
      padding: 0.50rem;
      transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;
    }

    .collection-search-button {
      display: inline-block;
      vertical-align: middle;
    }
  }
</style>
