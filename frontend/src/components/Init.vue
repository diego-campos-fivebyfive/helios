<template lang="pug">
  div
    q.phrase {{ phrase }}
</template>

<script>
  const phrase = new Promise(resolve => {
    resolve('Mocked Fetch Request')
  })

  export default {
    data: () => ({
      phrase: ''
    }),
    mounted() {
      phrase.then(data => {
        this.phrase = data
      })
    }
  }
</script>

<style lang="scss" scoped>
  @import "~styles/elements.scss";

  .phrase {
    @extend %e-title;
    font-size: 1em;
  }
</style>
