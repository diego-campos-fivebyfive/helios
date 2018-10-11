<template lang="pug">
  .tabbar
    label.tabbar-item(v-for='tab in tabs')
      router-link.tabbar-item-button(:to='tab.to')
        Icon(:name='tab.icon')
        .tabbar-item-label
          | {{ tab.title }}
</template>

<script>
  export default {
    data: () => ({
    }),
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
    position: fixed;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    font-weight: 400;
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    bottom: 0;
    left: 0;
    right: 0;
    white-space: nowrap;
    margin: 0;
    padding: 0;
    height: 49px;
    background-color: #fafafa;
    border-top: 1px solid #ccc;
    width: 100%;
  }

  .tabbar-item {
    font-weight: 400;
    position: relative;
    -webkit-box-flex: 1;
    -webkit-flex-grow: 1;
    flex-grow: 1;
    -webkit-flex-basis: 0;
    flex-basis: 0;
    width: auto;
    border-radius: 0;
  }

  .tabbar-item-radio {
    position: absolute;
    right: 0;
    top: 0;
    left: 0;
    bottom: 0;
    padding: 0;
    border: 0;
    background-color: transparent;
    z-index: 1;
    vertical-align: top;
    outline: none;
    width: 100%;
    height: 100%;
    margin: 0;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
  }

  .tabbar-item-button {
    text-align: center;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    font-weight: 400;
    box-sizing: border-box;
    background-clip: padding-box;
    padding: 0;
    margin: 0;
    font: inherit;
    color: inherit;
    background: transparent;
    border: none;
    line-height: normal;
    cursor: default;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    position: relative;
    display: inline-block;
    text-decoration: none;
    padding: 0;
    height: 49px;
    letter-spacing: 0;
    color: #999;
    vertical-align: top;
    background-color: transparent;
    border-top: none;
    width: 100%;
    font-weight: 400;
    line-height: 49px;

    svg {
      font-size: 24px;
      padding: 0;
      margin: 0;
      line-height: 26px;
      height: 28px;
    }

      .tabbar-item-label {
        display: block;
        font-size: 10px;
        line-height: 1;
        margin: -20px;
        font-weight: 400;
    }

  }
  .router-link-exact-active {
    color: #00a7ec;
    border-top: 1px solid #00a7ec;
    background: whitesmoke;
  }
</style>
