<template lang="pug">
  .collection-dropdown
    Button.collection-dropdown-button(
      v-on:click.native='dropdown',
      type='primary-common',
      icon='arrow-down',
      label='Operações',
      pos='single')
    nav.collection-dropdown-content(
      v-show='open')
      ul.collection-dropdown-content-separator(v-for='group in groups')
        li(v-for='button in group')
          Button(
            :pos='button.position',
            :key='button.icon',
            :icon='button.icon',
            :label='button.label',
            type='default-common')
</template>

<script>
  export default {
    props: [
      'groups'
    ],
    data: () => ({
      open: false
    }),
    methods: {
      dropdown() {
        this.open = !this.open
      }
    }
  }
</script>

<style lang="scss" scoped>
  .collection-dropdown {
    .collection-dropdown-content {
      background-color: $ui-white-regular;
      border: medium none;
      border-radius: 3px;
      box-shadow: 0 0 3px rgba(86, 96, 117, 0.7);
      display: block;
      float: left;
      opacity: 1;
      position: absolute;
      padding: $ui-space-x/2 0;
      text-align: left;
      text-shadow: none;
      z-index: 1000;

      li {
        display: block;
      }

      .collection-dropdown-content-separator {
        border-bottom: 1px solid $ui-gray-light;
      }
    }

    .collection-dropdown-button {
      cursor: pointer;
      display: block;
      text-align: center;
      width: 100%;
    }
  }
</style>
