<template lang="pug">
  Panel.panel
    Filters(
      slot='header',
      ref='filter',
      :setMemorialId='setMemorialId',
      v-on:getMemorialGroups='getMemorialGroups')
    Cells(
      slot='section',
      :filter='$refs.filter',
      v-on:updateMemorialRange='updateMemorialRange',
      :groups='groups')
</template>

<script>
  import Cells from './Cells'
  import Filters from './filters'

  export default {
    components: {
      Cells,
      Filters
    },
    data: () => ({
      groups: [],
      memorial: {}
    }),
    methods: {
      setMemorialId(id) {
        return new Promise((resolve, reject) => {
          if (!id) {
            return reject(new Error('id undefined'))
          }

          this.$router.push({
            path: `/memorial/${id}/config`
          })

          this.memorial.id = id
          return resolve()
        })
      },
      getMemorialGroups(params) {
        const uri = `admin/api/v1/memorial_ranges/${this.memorial.id}`

        this.axios.get(uri, { params })
          .then(response => {
            this.groups = response.data
          })
      },
      updateMemorialRange(range) {
        this.groups[range.family]
          .forEach(component => {
            if (component.id === range.id) {
              component.costPrice = range.costPrice
              component.ranges = range.powerRanges
            }
          })
      }
    },
    mounted() {
      this.memorial.id = this.$route.params.id
    }
  }

</script>

<style lang="scss" scoped>
  /* Form Style */
</style>
