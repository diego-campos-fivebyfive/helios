<template lang="pug">
  a.header
    img(src='@/assets/media/logo-small.png')
    span.title {{ user.name }}
    span {{ user.company }}
</template>

<script>
  const user = new Promise(resolve => {
    resolve({
      name: 'Rafael Kendrik',
      company: 'I9 Solar'
    })
  })

  export default {
    data: () => ({
      user: {}
    }),
    mounted() {
      user.then(data => {
        this.user = data
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
