<template lang="pug">
  Select(
    :field='{ label: "Nível de Desconto" }',
    :options='options',
    :selected='true',
    v-on:update='$emit("updateQuery")')
</template>

<script>
  import Select from '@/theme/collection/Select'

  export default {
    components: {
      Select
    },
    data: () => ({
      options: []
    }),
    mounted() {
      this.axios.get('admin/api/v1/memorials/levels')
        .then(response => {
          this.options = Object.entries(response.data)
            .map(item => ({
              value: item[0],
              text: item[1]
            }))
        })
    }
  }
</script>

<style lang="scss" scoped>
  /* Head */
</style>
