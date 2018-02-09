<template lang="pug">
  header.bar
    h1.title Inversores
    h2.info {{ date }}
    nav.menu
      Button(
        type='default-common',
        icon='sign-out',
        link='/logout',
        refresh='true',
        label='Sair',
        pos='first')
</template>

<script>
  const getDate = (vm) => {

    const months = [
      'janeiro',
      'fevereiro',
      'março',
      'abril',
      'maio',
      'junho',
      'julho',
      'agosto',
      'setembro',
      'outubro',
      'novembro',
      'dezembro'
    ]

    const daysInTheWeek = [
      'domingo',
      'segunda-feira',
      'terça-feira',
      'quarta-feira',
      'quinta-feira',
      'sexta-feira',
      'sábado',
    ]

    const date = new Date()
    const year = date.getFullYear()
    const month = months[date.getMonth()]
    const day = date.getDate()
    const dayInTheWeek = daysInTheWeek[date.getDay()]

    vm.date = `${dayInTheWeek}, ${day} de ${month} de ${year}`

    setTimeout(() => {
      getDate(vm)
    }, 500)
  }

  export default {
    data: () => ({
      date: ''
    }),
    mounted() {
      getDate(this)
    }
  }
</script>

<style lang="scss" scoped>
  .bar {
    background-color: $ui-white-regular;
    border-bottom: 1px solid $ui-divider-color;
    color: $ui-text-main;
    display: block;
    padding: $ui-space-y $ui-space-x;
    text-align: right;
    width: 100%;

    @include clearfix;

    * {
      vertical-align: middle;
    }
  }

  .title {
    display: inline-block;
    float: left;
    font-size: 2rem;
    font-weight: 300;
    text-align: left;
  }

  .info {
    display: inline-block;
    font-size: 1rem;
    font-weight: 400;
    margin: $ui-space-y/3 $ui-space-x;
    opacity: 0.8;
  }

  .menu {
    display: inline-block;
  }
</style>
