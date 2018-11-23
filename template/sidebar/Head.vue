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
      insertBackground('.svgBackground', 220, 120)
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
      padding: $ui-space-y / 2 $ui-space-x;
    }

    .info {
      min-height: $common-info-y;
    }
  }
</style>
