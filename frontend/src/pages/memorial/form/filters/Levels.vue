<template lang="pug">
  Select(
    :field='{ label: "Nível de Desconto" }',
    :options='options',
    v-on:update='$emit("updateLevelQuery", $event)')
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
            .map(([key, value]) => ({
              value: key,
              text: value
            }))
        })
    }
  }
</script>

<style lang="scss" scoped>
  /* Levels Style */
</style>
