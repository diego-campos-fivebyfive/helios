<template lang="pug">
  Select(
    :field='{ label: "Memorial" }',
    :options='options',
    :selected='true')
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
      this.axios.get('admin/api/v1/memorials')
        .then(response => {
          this.options = response.data.results
            .map(memorial => ({
              value: memorial.id,
              text: memorial.name
            }))
        })
    }
  }
</script>

<style lang="scss" scoped>
  /* Head */
</style>
