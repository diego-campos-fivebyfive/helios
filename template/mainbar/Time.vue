<template lang="pug">
  span.time {{ currentDate }}
</template>

<script>
  export default {
    computed: {
      currentDate() {
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
          'sábado'
        ]

        const date = new Date()
        const year = date.getFullYear()
        const month = months[date.getMonth()]
        const day = date.getDate()
        const dayInTheWeek = daysInTheWeek[date.getDay()]

        return `${dayInTheWeek}, ${day} de ${month} de ${year}`
      }
    }
  }
</script>

<style lang="scss">
  .time {
    display: inline-block;
    font-size: 1rem;
    font-weight: 400;
    opacity: 0.8;
  }
</style>
