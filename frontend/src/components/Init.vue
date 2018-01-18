<template>
  <div>
    <q class="phrase">{{ phrase }}</q>
  </div>
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

<style scoped>
  .phrase {
    font-size: 0.95em;
  }
</style>
