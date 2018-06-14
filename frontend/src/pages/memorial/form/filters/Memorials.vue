<template lang="pug">
  Select(
    :field='{ label: "Memorial" }',
    :options='options',
    v-on:update='$emit("updateMemorialQuery", $event.value)')
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
            .map(({ id, name }) => ({
              value: id,
              text: name
            }))
        })
    }
  }
</script>

<style lang="scss" scoped>
  /* Memorials Style */
</style>
