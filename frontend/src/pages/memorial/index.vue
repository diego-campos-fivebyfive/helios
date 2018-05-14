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
  const memorials = new Promise(resolve => resolve([
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
  ]))
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
        return this.memorials
      }
    },
    mounted() {
      memorials.then(data => {
        this.memorials = data
      })
    }
  }
</script>

<style lang="scss" scoped>
  /* Memorial Style */
</style>
