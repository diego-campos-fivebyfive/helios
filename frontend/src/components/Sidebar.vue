<template lang="pug">
  div.sidebar
    img(src='@/assets/media/logo.png')
    span {{ user.name }}
    span {{ user.company }}
    nav.menu
      ul
        li(v-for='item in menu')
          a(v-if='!item.dropdown')
            icon(:name='item.icon')
            | {{ item.name }}
          div(v-if='item.dropdown')
            button
              icon(:name='item.icon')
              | {{ item.name }}
            ul
              li(v-for='subitem in item.subitems')
                a
                  icon(:name='subitem.icon')
                  | {{ subitem.name }}
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
        icon: 'dashboard'
      },
      {
        name: 'Componentes',
        icon: 'cube',
        dropdown: true,
        subitems: [
          {
            name: 'Módulos',
            icon: 'th'
          },
          {
            name: 'Inversores',
            icon: 'exchange'
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
      display: block;
      font-weight: 600;
      text-align: left;
      width: 100%;

      &:hover {
        background-color: $menu-bgcolor_;
        color: $menu-color_;
      }
    }

    ul {
      list-style: none;
    }
  }
</style>
