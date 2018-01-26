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
        link: '#link1',
        icon: 'dashboard'
      },
      {
        name: 'Conta',
        link: '/account',
        icon: 'bookmark'
      },
      {
        name: 'Componentes',
        icon: 'cube',
        dropdown: true,
        subitems: [
          {
            name: 'Módulos',
            link: '#sublink1',
            icon: 'th'
          },
          {
            name: 'Inversores',
            link: '#sublink2',
            icon: 'exchange'
          }
        ]
      },
      {
        name: 'Configurações',
        icon: 'cube',
        dropdown: true,
        subitems: [
          {
            name: 'Dados da Empresa',
            link: '#sublink1',
            icon: 'th'
          },
          {
            name: 'Parâmetros',
            link: '#sublink2',
            icon: 'exchange'
          }
        ]
      },
      {
        name: 'Metricas',
        link: '/metric',
        icon: 'area-chart'
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
  ul {
    list-style: none;
  }

  li {
    color: $ui-sidebar-color;
    display: block;
    font-weight: 600;
    text-align: left;
    width: 100%;

    &:hover {
      background-color: $ui-gray-regular;
    }
  }

  .active {
    background-color: $ui-gray-regular;
    border-left: 5px solid $ui-blue-light;
  }
 </style>
