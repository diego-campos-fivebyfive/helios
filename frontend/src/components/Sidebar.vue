<template lang="pug">
  div.sidebar
    img(src='@/assets/media/logo.png')
    span {{ user.name }}
    span {{ user.company }}
    nav.menu
      ul
        li(v-for='item in menu')
          div(v-if='item.dropdown')
            button {{ item.name }}
            ul
              li(v-for='subitem in item.subitems')
                a {{ subitem.name }}
          a(v-if='!item.dropdown') {{ item.name }}
</template>

<script>
  const user = new Promise(resolve => {
    resolve({
      name: 'Rafael Kendrik',
      company: 'I9 Solar'
    })
  })

  const menu = new Promise(resolve => {
    resolve([
      {
        name: 'Dashboard',
        icon: 'fa-dashboard'
      },
      {
        name: 'Componentes',
        icon: 'fa-cube',
        dropdown: true,
        subitems: [
          {
            name: 'Módulos',
            icon: 'fa-th'
          },
          {
            name: 'Inversores',
            icon: 'fa-exchange'
          }
        ]
      }
    ])
  })

  export default {
    data: () => ({
      user: {},
      menu: []
    }),
    mounted() {
      user.then(data => {
        this.user = data
      })
      menu.then(data => {
        this.menu = data
      })
    }
  }
</script>

<style lang="scss" scoped>
  $menu-bgcolor: #080c17;
  $menu-bgcolor_: #293846;
  $menu-color: #a7b1c2;
  $menu-color_: #ffffff;

  .menu {
    background-color: $menu-bgcolor;

    a,
    button {
      color: $menu-color;
      font-weight: 600;

      &:hover {
        color: $menu-color_;
      }
    }

    ul {
      list-style: none;
    }
  }
</style>
