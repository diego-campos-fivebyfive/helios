<template lang="pug">
  .collection-dropdown
    Button.collection-dropdown-button(
      :action='showDropdown',
      class='primary-common',
      :label='$locale.theme.collection.operations')
      Icon(name='arrow-down')
    nav.collection-dropdown-content(v-show='showDropdownContent')
      ul.collection-dropdown-content-separator(v-for='group in groups')
        li(v-for='button in group')
          Button(
            :pos='button.position',
            :key='button.icon',
            :label='button.label',
            class='default-common',
            :action='button.click')
            Icon(:name='button.icon')
</template>

<script>
  export default {
    props: {
      groups: {
        required: true,
      }
    },
    data: () => ({
      open: false
    }),
    methods: {
      showDropdown() {
        this.open = !this.open
      },
      showDropdownContent() {
        return this.open
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
      z-index: 5;

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
