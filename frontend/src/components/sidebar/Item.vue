<template lang="pug">
  a.item(:href='item.link', :class='{ subitem: subitem }')
    icon.icon-ui(:name='item.icon')
    | {{ item.name }}
    icon.icon-arrow(name='angle-right')
</template>

<script>
  export default {
    props: ['item', 'subitem']
  }
</script>

<style lang="scss" scoped>
  .item {
    color: inherit;
    display: block;
    padding: $ui-space-y $ui-space-x/1.5 $ui-space-y $ui-space-x;
    transition: all 300ms;
    width: 100%;

    &.subitem {
      padding: $ui-space-y/2 $ui-space-x/1.5 $ui-space-y/2 $ui-space-x*2;
    }

    &:hover {
      color: $ui-white-regular;
    }
  }

  .icon-ui {
    margin-right: $ui-space-x/3;
    vertical-align: bottom;
    width: 1rem;
  }

  .icon-arrow {
    float: right;
  }
</style>
