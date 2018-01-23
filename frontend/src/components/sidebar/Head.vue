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
  $ui-color: #ffffff;
  $ui-space-x: 25px;
  $ui-space-y: 15px;

  .header {
    background: url('~@/assets/media/logo-cover.png');
    color: $ui-color;
    display: block;
    text-align: center;
    padding: $ui-space-y/2 $ui-space-x;

    .title {
      font-weight: 600;
    }

    span {
      display: block;
      padding: 2px;
    }
  }
</style>
