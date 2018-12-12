import moment from 'moment'
import 'moment/locale/pt-br'

const userCountry = 'Brazil' // from localStorage
const userLanguage = localStorage.getItem('userLanguage')

const dateTimeEntryFormat = 'YYYY-MM-DD, hh:mm a'

const dateTime = {
  'Brazil': {
    full: 'DD/MM/YYYY, HH:mm'
  },
  'United States': {
    full: 'MM/DD/YYYY, hh:mm a'
  }
}

export const toDateTime = (date, format = 'full') =>
  moment(date, dateTimeEntryFormat)
    .format(dateTime[userCountry][format])

export const toTimeAgo = date => {
  const created = moment(date, 'YYYY-MM-DD HH:mm:ss')
  const now = moment().locale(userLanguage.replace('_', '-'))
  const duration = moment.duration(-Math.abs(now.diff(created)))
  return duration.humanize(true)
}
