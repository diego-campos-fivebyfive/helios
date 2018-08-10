<template lang="pug">
  form.collection-search(
    v-on:keypress.enter.prevent='')
    input.collection-search-input(
      v-model='termSearch',
      v-on:keyup.enter='() => $emit("updateList")')
    Button.collection-search-button(
      :action='() => $emit("updateList")',
      class='primary-common',
      label='Pesquisar',
      pos='last')
</template>

<script>
  import Button from 'theme/collection/Button'

  export default {
    components: {
      Button
    },
    data: () => ({
      termSearch: ''
    })
  }
</script>

<style lang="scss" scoped>
  .collection-search {
    height: 2.25rem;
    position: relative;
    width: 100%;

    .collection-search-input {
      background-color: $ui-white-regular;
      background-image: none;
      border: 1px solid $ui-blue-light;
      color: inherit;
      padding: 0.5rem;
      transition:
        border-color 0.15s ease-in-out 0s,
        box-shadow 0.15s ease-in-out 0s;
    }

    .collection-search-button {
      display: inline-block;
      vertical-align: middle;
    }
  }
</style>
