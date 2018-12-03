export default {
  theme: {
    weekdays: {
      sunday: 'sunday',
      monday: 'monday',
      tuesday: 'tuesday',
      wednesday: 'wednesday',
      thrusday: 'thrusday',
      friday: 'friday',
      saturday: 'saturday'
    },
    months: {
      january: 'january',
      february: 'february',
      march: 'march',
      april: 'april',
      may: 'may',
      june: 'june',
      july: 'july',
      august: 'august',
      september: 'september',
      october: 'october',
      november: 'november',
      december: 'december'
    },
    requireField(field) {
      return `The field ${field} is required`
    },
    getFullDate: ({ dayInTheWeek, day, month, year }) => {
      const ordinaryDefault = 'th'
      const ordinaries = {
        1: 'st',
        2: 'nd',
        3: 'rd'
      }

      const dayOrdinaryChar = String(day).charAt(day.length - 1)
      const ordinary = ordinaries[dayOrdinaryChar] || ordinaryDefault

      return `${dayInTheWeek}, ${month} ${day}${ordinary} ${year}`
    },
    collection: {
      operations: 'Operations',
      confirm: 'Confirm',
      close: 'Close',
      next: 'Next',
      previous: 'Previous'
    },
    template: {
      refreshing: 'Refreshing',
      pullToRefresh: 'Pull down to refresh',
      releaseToReload: 'Release to reload',
      signOut: 'Sign out',
      myData: 'My data'
    }
  }
}
