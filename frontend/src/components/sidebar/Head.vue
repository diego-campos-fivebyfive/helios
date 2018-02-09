<template lang="pug">
  router-link.header(to='/')
    img(src='@/assets/media/logo-small.png', alt='Sices Solar Logo')
    span.title {{ user.name }}
    span {{ user.company }}
</template>

<script>
  export default {
    data: () => ({
      user: {}
    }),
    mounted() {
      this.axios.get('api/v1/user').then(response => {
        this.user = response.data
      })
    }
  }
</script>

<style lang="scss" scoped>
  .header {
    background: url("~@/assets/media/logo-cover.png");
    color: $ui-white-regular;
    display: block;
    text-align: center;
    padding: $ui-space-y/2 $ui-space-x;
  }

  .title {
    font-weight: 600;
  }

  span {
    display: block;
    padding: 2px;
  }
</style>
