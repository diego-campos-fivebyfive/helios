<template lang="pug">
  Panel.panel
    Filters(
      slot='header',
      ref='filter',
      :setMemorialId='setMemorialId',
      v-on:getMemorialGroups='getMemorialGroups')
    Cells(
      slot='section',
      :getQueryParams='getQueryParams',
      v-on:updateMemorialRange='updateMemorialRange',
      v-on:updateMemorialMarkup='updateMemorialMarkup',
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
      getQueryParams() {
        return this.$refs.filter.queryParams
      },
      updateMemorialRange(range) {
        this.groups[range.family]
          .forEach(component => {
            if (component.id === range.id) {
              /* eslint-disable */
              component.costPrice = range.costPrice
              component.ranges = range.powerRanges
              /* eslint-enable */
            }
          })
      },
      updateMemorialMarkup(ranges, markup) {
        this.groups[ranges.family]
          .forEach(component => {
            ranges.ranges.forEach(range => {
              if (range.id === component.id) {
                /* eslint-disable */
                component.ranges[ranges.powerRange].markup = markup
                component.ranges[ranges.powerRange].price = range.price
                /* eslint-enable */
              }
            })
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
