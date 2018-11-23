<template lang="pug">
  div(:class='`sidebar-${sidebarType}`')
    .svgBackground
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
  import { insertBackground } from '../triangles'

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
    mounted() {
      this.svgBackground()
    },
    methods: {
      showInfo() {
        return (this.sidebarType === 'common')
      },
      svgBackground() {
        const params = {
          'element': '.svgBackground',
          'width': 220,
          'height': 119,
          'scale': 4,
          'duration': 30000
        }
        insertBackground(params)
      }
    }
  }
</script>

<style lang="scss" scoped>
  $collapse-logo-x: 60px;
  $common-info-y: 38px;
  $sidebarWidht: 220px;
  $sidebarHeight: 120px;

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
      width: $sidebarWidht;
      height: $sidebarHeight;
      padding-top: $ui-space-y / 2;
    }

    .info {
      min-height: $common-info-y;
    }
  }

  .svgBackground {
    background: #da8c3f;
    position: absolute;
  }
</style>
