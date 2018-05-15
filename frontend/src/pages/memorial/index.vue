<template lang="pug">
  Panel.panel
    div(slot='header')
      nav.menu
        Button(
          type='primary-common',
          icon='plus-square',
          label='Memorial',
          pos='single')
    List(
      slot='section',
      :memorials='memorials')
</template>

<script>
  import List from './list'

  export default {
    components: {
      List
    },
    data: () => ({
      memorials: []
    }),
    methods: {
      getMemorials() {
        const uri = `admin/api/v1/memorials/`

        this.axios.get(uri).then(response => {
          this.memorials = response.data.results
            .map(memorial => ({
              name: memorial.name,
              createdAt: this.formatDate(memorial.createdAt),
              expiredAt: this.formatDate(memorial.expiredAt),
              publishedAt: this.formatDate(memorial.publishedAt),
              status: this.getStatusClassName(memorial.status),
              class: this.getStatusClassName(memorial.status)
            }))
        })
      },
      formatDate(date = null) {
        if (!date) {
          const defaultValue = ''
          return defaultValue
        }

        const moment = this.$moment(date, 'YYYY-MM-DD')
        return moment.format('DD/MM/YYYY')
      },
      getStatusName(statusCode) {
        const statuses = {
          0: 'pendente',
          1: 'publicado',
          2: 'expirado'
        }

        return statuses[statusCode]
      },
      getStatusClassName(statusCode) {
        const statuses = {
          0: 'pending',
          1: 'published',
          2: 'expired'
        }

        return statuses[statusCode]
      }
    },
    mounted() {
      this.getMemorials()
    }
  }
</script>

<style lang="scss" scoped>
  /* Memorial Style */
</style>
