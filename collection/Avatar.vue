<template lang="pug">
  span.collection-avatar
    | {{ firstLetters }}
</template>

<script>
  export default {
    props: {
      current: {
        type: String,
        required: true
      }
    },
    computed: {
      firstLetters() {
        return this.current.match(/\b(\w)/g)
          .slice(0, 2)
          .join('')
      }
    }
  }
</script>

<style lang="scss" scoped>
  .collection-avatar {
    align-items: center;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    vertical-align: middle;
  }

</style>
