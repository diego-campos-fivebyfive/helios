<template lang="pug">
  span.time
    | {{ currentDate }}
</template>

<script>
  import $locale from 'locale'

  export default {
    computed: {
      currentDate() {
        const months = Object.values($locale.theme.months)
        const daysInTheWeek = Object.values($locale.theme.weekdays)

        const date = new Date()

        return $locale.theme.getFullDate({
          year: date.getFullYear(),
          month: months[date.getMonth()],
          day: date.getDate(),
          dayInTheWeek: daysInTheWeek[date.getDay()]
        })
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
