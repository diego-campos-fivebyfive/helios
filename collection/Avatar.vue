<template lang="pug">
  .collection-avatar(:data-letters='firstLetters')
</template>

<script>
  export default {
    props: {
      userName: {
        type: String,
        required: true
      }
    },
    computed: {
      firstLetters() {
        return this.userName.match(/\b(\w)/g)
          .slice(0, 2)
          .join('')
      }
    }
  }
</script>

<style lang="scss" scoped>
  [data-letters]:before {
    background: $ui-blue-dark;
    border-radius: 50%;
    color: $ui-white-regular;
    content: attr(data-letters);
    display: inline-block;
    font-size: 1em;
    height: 2.5em;
    line-height: 2.5em;
    text-align: center;
    vertical-align: middle;
    width: 2.5em;
  }
</style>
