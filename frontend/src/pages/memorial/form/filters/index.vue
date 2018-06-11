<template lang="pug">
  .filters
    CopyModal(
      ref='copyModal',
      :level='queryParams.level')
    Button.prev(
      class='default-bordered',
      label='voltar')
      Icon(name='arrow-left')
    Memorials.col-memorial(v-on:updateMemorialQuery='updateMemorialQuery')
    Levels(v-on:updateLevelQuery='updateLevelQuery')
    Components(
      :families='queryParams.families',
      v-on:updateFamiliesQuery='updateFamiliesQuery')
    Button.copy(
      class='primary-common',
      label='Copiar',
      :action='show')
      Icon(name='copy')
</template>

<script>
  import Components from './Components'
  import CopyModal from '../CopyModal'
  import Levels from './Levels'
  import Memorials from './Memorials'

  export default {
    props: [
      'setMemorialId'
    ],
    data: () => ({
      queryParams: {
        families: [],
        level: 'titanium'
      }
    }),
    components: {
      Components,
      CopyModal,
      Levels,
      Memorials
    },
    methods: {
      show() {
        this.$refs.copyModal.show()
      },
      updateFamiliesQuery(value) {
        this.queryParams.families = value
        this.$emit('getMemorialGroups', this.queryParams)
      },
      updateLevelQuery(event) {
        this.queryParams.level = event.value
        this.$emit('getMemorialGroups', this.queryParams)
      },
      updateMemorialQuery(memorialId) {
        this.setMemorialId(memorialId)
          .then(() => {
            this.$emit('getMemorialGroups', this.queryParams)
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
  .filters {
    align-items: center;
    display: flex;
    justify-content: space-between;
    text-align: left;
  }

  .col-memorial {
    max-width: 150px;
  }

  .prev {
    margin-top: $ui-space-y*1.25;
  }

  .copy {
    margin-top: $ui-space-y*1.25;
  }
</style>
