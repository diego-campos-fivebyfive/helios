<template lang="pug">
  .tabbar
    label.tabbar-item(v-for='tab in tabs')
      router-link.tabbar-item-button(:to='tab.to')
        Icon(v-if='tab.icon', :name='tab.icon')
        .tabbar-item-label
          | {{ tab.title }}
      Badge(v-if='tab.badge', :badge='tab.badge')
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
  .tabbar {
    background-color: $ui-white-regular;
    border-top: 1px solid $ui-gray-light;
    bottom: 0;
    display: flex;
    font-weight: 400;
    height: $ui-tabbar-mobile-y;
    left: 0;
    margin: 0;
    padding: 0;
    position: fixed;
    right: 0;
    white-space: nowrap;
    width: 100%;

    .badge {
      position: absolute;
      right: 0;
      margin-right: 0px;
    }
  }

  .tabbar-item {
    flex-basis: 0;
    flex-grow: 1;
    font-weight: 400;
    position: relative;
  }

  .tabbar-item-radio {
    background-color: transparent;
    border: 0;
    bottom: 0;
    height: 100%;
    left: 0;
    margin: 0;
    outline: none;
    padding: 0;
    position: absolute;
    right: 0;
    top: 0;
    vertical-align: top;
    width: 100%;
    z-index: 1;
  }

  .tabbar-item-button {
    background-clip: padding-box;
    border-top: none;
    border: none;
    box-sizing: border-box;
    color: $ui-gray-regular;
    display: inline-block;
    font-weight: 400;
    font: inherit;
    height: $ui-tabbar-mobile-y;
    letter-spacing: 0;
    line-height: $ui-tabbar-mobile-y;
    margin: 0;
    overflow: hidden;
    padding: 0;
    position: relative;
    text-align: center;
    text-decoration: none;
    vertical-align: top;
    white-space: nowrap;
    width: 100%;

    svg {
      font-size: 24px;
      height: 28px;
      line-height: 26px;
      margin: 0;
      padding: 0;
    }

    .tabbar-item-label {
      display: block;
      font-size: 10px;
      font-weight: 400;
      line-height: 1;
      margin: -20px;
    }
  }

  .router-link-exact-active {
    background: $ui-gray-lighter;
    border-top: 1px solid $ui-blue-dark;
    color: $ui-blue-dark;
  }
</style>
