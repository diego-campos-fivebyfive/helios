<template lang="pug">
  div(:class='`sidebar-${sidebarType}`')
    router-link.header(to='/')
      Cover.cover(
        :width='sidebarHeaderWidth()',
        :height='sidebarHeaderHeight()',
        :speed='23000',
        :scale='4')
      img.logo(
        src='~theme/assets/media/logo-small.png',
        alt='Sices Solar Logo')
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
    overflow: hidden;
  }

  .name {
    display: block;
    font-weight: 600;
    padding: $ui-space-y / 4;
  }

  .logo {
    position: relative;
  }

  .sidebar-collapse {
    .logo {
      max-width: $collapse-logo-x;
      padding: $ui-space-y;
    }
  }

  .sidebar-common {
    .header {
      position: relative;
      width: $ui-sidebar-common-x;
      height: $ui-sidebar-head-common-y;
    }

    .info {
      position: relative;
      min-height: $common-info-y;
    }
  }

  .cover {
    background: $ui-orange-dark;
    position: absolute;
  }
</style>
