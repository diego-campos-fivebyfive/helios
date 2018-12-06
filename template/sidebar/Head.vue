<template lang="pug">
  div(:class='`sidebar-${sidebarType}`')
    transition(name='fade')
      router-link.header(to='/')
        Cover.cover(
          :width='sidebarHeaderWidth()',
          :height='sidebarHeaderHeight()',
          :speed='23000',
          :scale='4')
        transition(name='fade')
          img.logo(
            src='~theme/assets/media/logo-small.png',
            alt='Sices Solar Logo')
        transition(name='fade')
          .info(v-if='showInfo()')
            span.name
              | {{ user.name }}
            span
              | {{ user.company }}
</template>

<script>
  import styles from 'theme/assets/style/main.scss'

  export default {
    props: {
      sidebarType: {
        type: String,
        required: true
      }
    },
    computed: {
      user() {
        return {
          name: localStorage.getItem('userName'),
          company: localStorage.getItem('userCompany')
        }
      }
    },
    watch: {
      sidebarType() {}
    },
    methods: {
      showInfo() {
        return (this.sidebarType === 'common')
      },
      sidebarHeaderWidth() {
        return parseInt(styles['ui-sidebar-common-x'], 10)
      },
      sidebarHeaderHeight() {
        return parseInt(styles['ui-sidebar-head-common-y'], 10)
      }
    }
  }
</script>

<style lang="scss" scoped>
  $collapse-logo-x: 60px;
  $common-info-y: 38px;

  .header {
    position: relative;
    color: $ui-white-regular;
    display: block;
    text-align: center;
  }

  .name {
    display: block;
    font-weight: 600;
    padding: $ui-space-y / 4;
  }

  .logo {
    position: relative;
    padding-top: $ui-space-x / 10;
    transition: width 1s;
    transition-delay: 1s;
  }

  .sidebar-collapse {
    .header {
      position: relative;
      height: $ui-sidebar-head-collapse-y;
      transition: all 0.7s ease;

      .cover {
        height: $ui-sidebar-head-collapse-y;
        transition: all 0.8s ease;
      }
    }

    .logo {
      //max-width: $collapse-logo-x;
      max-width: 100%;
      //padding: $ui-space-y;
      padding: 8px 15px 0;
      transition: all 0.8s ease;
    }

    .info {
      display: none;
      transition-delay: 0.3s;
    }
  }

  .sidebar-common {
    .header {
      position: relative;
      height: $ui-sidebar-head-common-y;
      transition: all 0.9s ease;

      .cover {
        height: $ui-sidebar-head-common-y;
        transition: all 0.8s ease;
      }
    }

    .info {
      position: relative;
      min-height: $common-info-y;
      transition-delay: 0.3s;
    }
  }

  .cover {
    background: $ui-orange-dark;
    position: absolute;
  }

  .fade-enter-active {
    transition: all 1s ease;
  }

  .fade-enter {
    opacity: 0;
  }
</style>
