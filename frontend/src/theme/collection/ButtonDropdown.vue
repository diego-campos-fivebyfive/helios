<template lang="pug">
  .collectio-dropdown
    Button.collectio-dropdown-button(
      type='primary-common',
      icon='arrow-down',
      label='Operações',
      pos='single',
      v-on:click.native='toggleDropdown($event)')
    nav.collectio-dropdown-content
      ul.collectio-dropdown-content-separator(v-for='group in groups')
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
    methods: {
      toggleDropdown (event) {
        event.currentTarget.classList.toggle('is-active')
      }
    }
  }
</script>

<style lang="scss" scoped>
  .collectio-dropdown {
    .collectio-dropdown-button {
      cursor: pointer;
      display: block;
      text-align: center;
      width: 100%;

      &.is-active {
        + .collectio-dropdown-content {
          display: block;
          opacity: 1;
          position: absolute;
          padding: $ui-space-x/2 0;
        }
      }
    }
    .collectio-dropdown-content {
      background-color: $ui-white-regular;
      border: medium none;
      border-radius: 3px;
      box-shadow: 0 0 3px rgba(86,96,117,0.7);
      display: none;
      float: left;
      list-style: none outside none;
      text-align: left;
      text-shadow: none;
      z-index: 1000;

      li {
        display: block;
      }

      .collectio-dropdown-content-separator:nth-last-child(2) {
        border-bottom: 1px solid $ui-gray-light;
      }
    }
  }
</style>
