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

  /* Mocked Data */
  const memorials = [
    {
      name: 'Memorial Editada',
      createdAt: '2017-09-21 09:36:26',
      expiredAt: '2017-11-16 12:14:53',
      publishedAt: '2017-09-21 10:41:49',
      status: 2
    },
    {
      name: 'Memorial Editada [clone:248]',
      createdAt: '2017-11-16 12:14:23',
      expiredAt: '2017-11-16 17:36:40',
      publishedAt: '2017-09-21 10:41:49',
      status: 2
    },
    {
      name: 'Memorial Editada [clone:248] [clone:254] [clone:257]',
      createdAt: '2017-09-21 09:36:26',
      expiredAt: '',
      publishedAt: '2018-02-02 16:55:33',
      status: 1
    },
    {
      name: 'Memorial Editada [clone:248] [clone:254] [clone:257] [clone:258]',
      createdAt: '2018-03-29 22:41:31',
      expiredAt: '',
      publishedAt: '',
      status: 0
    }
  ]
  /* End Mocked Data */

  export default {
    components: {
      List
    },
    data: () => ({
      memorials: []
    }),
    methods: {
      getMemorials() {
        this.memorials = memorials
          .map(memorial => ({
            name: memorial.name,
            createdAt: this.formatDate(memorial.createdAt),
            expiredAt: this.formatDate(memorial.expiredAt),
            publishedAt: this.formatDate(memorial.publishedAt),
            status: this.getStatusClassName(memorial.status),
            class: this.getStatusClassName(memorial.status)
          }))
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
