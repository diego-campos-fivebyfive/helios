<template lang="pug">
  .tabbar
    label.tabbar-item(v-for='tab in tabs')
      router-link.tabbar-item-button(:to='tab.to')
        Icon(v-if='tab.icon', :name='tab.icon')
        .tabbar-item-label
          | {{ tab.title }}
      Badge(
        v-if='tab.content || tab.contentAsync',
        :content='tab.content',
        :contentAsync='tab.contentAsync',
        labelType='warning')
</template>

<script>
  export default {
    props: {
      tabs: {
        type: Array,
        required: true
      }
    }
  }
</script>

<style lang="scss" scoped>
  $tab-icon-height: 28px;

  .tabbar {
    background-color: $ui-white-regular;
    display: flex;
    font-weight: 400;
    height: $ui-tabbar-mobile-y;
    position: fixed;
    width: 100%;
  }

  .tabbar-item {
    flex-basis: 0;
    flex-grow: 1;
    font-weight: 400;
    position: relative;
  }

  .tabbar-item-button {
    color: $ui-gray-regular;
    display: inline-block;
    border-top: 1px solid $ui-gray-light;
    font: inherit;
    height: $ui-tabbar-mobile-y;
    letter-spacing: 0;
    line-height: 1;
    margin: 0;
    overflow: hidden;
    text-align: center;
    width: 100%;

    svg {
      height: $tab-icon-height;
    }

    .tabbar-item-label {
      display: block;
      font-size: 0.65em;
    }
  }

  .router-link-exact-active {
    background: $ui-gray-lighter;
    border-top: 1px solid $ui-blue-dark;
    color: $ui-blue-dark;
  }
</style>
