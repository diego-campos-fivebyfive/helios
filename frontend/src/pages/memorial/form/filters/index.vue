<template lang="pug">
  .filters
    Button.prev(
      type='default-bordered',
      icon='arrow-left',
      label='voltar',
      pos='single')
    Memorials.col-memorial(v-on:updateMemorialQuery='updateMemorialQuery')
    Levels(v-on:updateLevelQuery='updateLevelQuery')
    Components(v-on:updateFamiliesQuery='updateFamiliesQuery')
    Button.copy(
      type='primary-common',
      icon='copy',
      label='Copiar',
      pos='single')
</template>

<script>
  import Components from './Components'
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
      Levels,
      Memorials
    },
    methods: {
      updateFamiliesQuery(selectedFamilyName, event) {
        if (event.target.checked) {
          this.queryParams.families = this.queryParams.families
            .concat(selectedFamilyName)
        } else {
          this.queryParams.families = this.queryParams.families
            .filter(eachFamilyName => (
              eachFamilyName !== selectedFamilyName
            ))
        }

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
