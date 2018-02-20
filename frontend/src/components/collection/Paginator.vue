<template lang="pug">
  nav
    span(v-if='pagination.links.prev') Anterior
    ul
      li(v-for='pageNumber in pagination.total')
        button(v-on:click='paginate(pageNumber)')
          | {{ pageNumber }}
    span(v-if='pagination.links.next') Próxima
</template>

<script>
  export default {
    props: [
      'pagination'
    ],
    methods: {
      paginate(pageNumber) {
        this.$emit('paginate', pageNumber)
      }
    }
  }
</script>

<style lag="scss">

</style>
