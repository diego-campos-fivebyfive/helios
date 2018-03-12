<template lang="pug">
  .wrapper
    ModalForm(
      ref='modalForm',
      v-on:getCoupons='getCoupons')
      ContentForm(
        slot='section',
        ref='contentForm')
      Button(
        slot='buttons',
        v-on:click.native='send',
        icon='save',
        type='primary-strong',
        label='Salvar',
        pos='single')
    Panel.panel
      Head(
        slot='header',
        v-on:showModalForm='showModalForm')
      Content(
        slot='section',
        :coupons='coupons',
        v-on:getCoupons='getCoupons',
        v-on:showModalForm='showModalForm')
      Paginator(
        slot='footer',
        :pagination='pagination',
        v-on:paginate='getCoupons')
</template>

<script>
  import Content from './Content'
  import ContentForm from './ContentForm'
  import Head from './Head'
  /*import ModalForm from './ModalForm'*/

  export default {
    components: {
      Content,
      ContentForm,
      Head,
      /*ModalForm*/
    },
    data: () => ({
      coupons: [],
      pagination: {}
    }),
    methods: {
      showModalForm(coupon) {
        this.$refs.modalForm.show()

        if (coupon) {
          this.$refs.contentForm.setForm('edit', coupon)
        } else {
          this.$refs.contentForm.setForm('create', { account: {} })
        }
      },
      getCoupons(pageNumber = 1) {
        const uri = `/api/v1/coupon?page=${pageNumber}`
        this.axios.get(uri).then(response => {
          this.coupons = response.data.results
          this.pagination = response.data.page
        })
      }
    },
    mounted() {
      this.getCoupons()
    }
  }
</script>

<style lang="scss" scoped>
  /* Coupon Style */
</style>
