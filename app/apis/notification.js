import icon from 'theme/assets/media/logo.png'

const notify = ({ subject, body, time = 5000 }) => {
  const notification = new Notification(subject, {
    icon,
    body: body
      .toString()
      .substr(0, 200)
      .replace(/<br>|<\/br>/g, '\n')
      .replace(/<[^>]*>/g, '')
  })

  setTimeout(notification.close.bind(notification), time)
}

export const pushNotification = notificationParams => {
  if (Notification.permission === 'granted') {
    notify(notificationParams)
    return
  }

  if (Notification.permission !== 'denied') {
    Notification.requestPermission(permission => {
      if (permission === 'granted') {
        notify(notificationParams)
      }
    })
  }
}
