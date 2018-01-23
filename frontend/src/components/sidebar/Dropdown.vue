<template lang="pug">
  div
    button.item(v-on:click='itemPress')
      icon.icon-ui(:name='item.icon')
      | {{ item.name }}
      icon.icon-arrow(v-if='open', name='angle-down')
      icon.icon-arrow(v-else, name='angle-left')
    ul(v-show='open')
      li(v-for='subitem in item.subitems')
        Item(:item='subitem', :subitem='true')
</template>

<script>
  import Item from './Item'

  export default {
    props: ['item'],
    data: () => ({
      open: false
    }),
    components: {
      Item
    },
    methods: {
      itemPress() {
        this.open = !this.open
      }
    }
  }
</script>

<style lang="scss" scoped>
  $ui-space-y: 15px;
  $ui-space-x: 25px;
  $ui-white-n: #ffffff;

  .item {
    color: inherit;
    font-weight: inherit;
    padding: $ui-space-y $ui-space-x/1.5 $ui-space-y $ui-space-x;
    text-align: inherit;
    transition: all 300ms;
    width: 100%;

    &:hover {
      color: $ui-white-n;
    }
  }

  .icon-ui {
    margin-right: $ui-space-x/2;
    vertical-align: bottom;
    width: 1rem;
  }

  .icon-arrow {
    float: right;
  }

  ul {
    list-style: none;
    padding-bottom: $ui-space-y/2;
  }
</style>
