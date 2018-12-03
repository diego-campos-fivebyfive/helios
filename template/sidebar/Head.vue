<template lang="pug">
  div(:class='`sidebar-${sidebarType}`')
    Cover.cover(
      :width='sidebarWidth',
      :height='sidebarHight',
      :speed='23000',
      :scale='4')
    router-link.header(to='/')
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
    data:() => ({
      sidebarWidth: parseInt(styles['ui-sidebar-common-x'], 10),
      sidebarHight: parseInt(styles['ui-sidebar-head-common-y'], 10)
    }),
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
      padding-top: $ui-space-y / 2;
    }

    .info {
      min-height: $common-info-y;
    }
  }

  .cover {
    background: $ui-orange-dark;
    position: absolute;
  }
</style>
