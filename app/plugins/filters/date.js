import moment from 'moment'

const dateTimeEntryFormat = 'YYYY-MM-DD, hh:mm a'

const dateTime = {
  'Brazil': {
    full: 'DD/MM/YYYY, HH:mm'
  },
  'United States': {
    full: 'MM/DD/YYYY, hh:mm a'
  }
}

const userCountry = 'Brazil' // from localStorage

export const toDateTime = (date, format = 'full') =>
  moment(date, dateTimeEntryFormat)
    .format(dateTime[userCountry][format])
