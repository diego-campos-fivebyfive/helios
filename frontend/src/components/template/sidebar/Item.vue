<template lang="pug">
  a.item(:href='item.link', :class='{ "sub-item": subItem }')
    Icon.icon-ui(:name='item.icon')
    | {{ item.name }}
    Icon.icon-arrow(name='angle-right')
</template>

<script>
  export default {
    props: ['item', 'subItem']
  }
</script>

<style lang="scss" scoped>
  .item {
    color: inherit;
    display: block;
    padding: $ui-space-y $ui-space-x/1.5 $ui-space-y $ui-space-x;
    transition: all 300ms;
    width: 100%;

    &.sub-item {
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
