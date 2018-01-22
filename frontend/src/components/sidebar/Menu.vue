<template lang="pug">
  ul.menu
    li(v-for='item in menu')
      Dropdown(v-if='item.dropdown', :item='item')
      Item(v-else, :item='item')
</template>

<script>
  import Item from './Item'
  import Dropdown from './Dropdown'

  const menu = new Promise(resolve => {
    resolve([
      {
        name: 'Dashboard',
        link: 'link1',
        icon: 'dashboard'
      },
      {
        name: 'Componentes',
        icon: 'cube',
        dropdown: true,
        subitems: [
          {
            name: 'Módulos',
            link: 'sublink1',
            icon: 'th'
          },
          {
            name: 'Inversores',
            link: 'sublink2',
            icon: 'exchange'
          }
        ]
      }
    ])
  })

  export default {
    components: {
      Item,
      Dropdown
    },
    data: () => ({
      menu: []
    }),
    mounted() {
      menu.then(data => {
        this.menu = data
      })
    }
  }
</script>

<style lang="scss" scoped>
  $ui-gray-n: #a7b1c2;
  $ui-gray-l: #293846;
  $ui-blue-n: #00a7ec;
  $ui-white-n: #ffffff;

  ul {
    list-style: none;
  }

  li {
    color: $ui-gray-n;
    display: block;
    font-weight: 600;
    text-align: left;
    width: 100%;

    &:hover {
      background-color: $ui-gray-l;
    }
  }

  .active {
    background-color: $ui-gray-l;
    border-left: 5px solid $ui-blue-n;
  }
 </style>
