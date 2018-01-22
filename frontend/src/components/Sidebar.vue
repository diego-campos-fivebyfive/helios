<template lang="pug">
  div.sidebar
    nav.menu
      header.header
        img(src='@/assets/media/logo-small.png')
        span.title {{ user.name }}
        span {{ user.company }}
      ul
        li(v-for='item in menu')
          a(
            :href='item.link',
            v-if='!item.dropdown'
            )
            icon(:name='item.icon')
            | {{ item.name }}
            icon(class='arrow', name='angle-right')
          div.active(v-if='item.dropdown')
            button
              icon(:name='item.icon')
              | {{ item.name }}
              icon(v-if='item.open', class='arrow', name='angle-down')
              icon(v-if='!item.open', class='arrow', name='angle-left')
            ul
              li(v-for='subitem in item.subitems')
                a(:href='subitem.link')
                  icon(:name='subitem.icon')
                  | {{ subitem.name }}
                  icon(class='arrow', name='angle-right')
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
  $menu-bgColor: #080c17;
  $menu-bgColor_: #293846;
  $menu-bColor_: #00a7ec;
  $menu-color: #a7b1c2;
  $menu-color_: #ffffff;
  $sidebar-maxWidth: 220px;
  $sidebar-padX: 25px;
  $sidebar-padY: 15px;

  .sidebar {
    max-width: $sidebar-maxWidth;
  }

  .header {
    background: url('~@/assets/media/logo-cover.png');
    color: #ffffff;
    text-align: center;
    padding: $sidebar-padY/2 $sidebar-padX;

    .title {
      font-weight: 600;
    }

    span {
      display: block;
      padding: 2px;
    }
  }

  .menu {
    background-color: $menu-bgColor;

    a,
    button {
      color: $menu-color;
      display: block;
      font-weight: 600;
      padding: $sidebar-padY $sidebar-padX/3 $sidebar-padY $sidebar-padX;
      text-align: left;
      width: 100%;

      svg {
        margin-right: 10px;
        vertical-align: bottom;
        width: 1rem;

        &.arrow {
          float: right;
        }
      }

      &:hover {
        background-color: $menu-bgColor_;
        color: $menu-color_;
      }
    }

    ul {
      list-style: none;

      ul {
        padding-left: $sidebar-padX;

        a {
          padding-top: $sidebar-padY/2;
          padding-bottom: $sidebar-padY/2;
        }
      }
    }

    .active {
      border-left: 5px solid $menu-bColor_;
      background-color: $menu-bgColor_;
      color: $menu-color_;

      > a,
      > button {
        color: $menu-color_;
      }
    }
  }
</style>
