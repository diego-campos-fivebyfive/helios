import Notification from './Notification'
import Button from './Button'

const Types = `
  <div>
    <Button
      :action='() => $refs.notification.notify(
        "This is a simple message!")'
      label='Notify primary-common'
      class='default-bordered'>
    </Button>

    <Button
      :action='() => $refs.notification.notify(
        "This is a DANGER message!",
        "danger-common")'
      label='Notify primary-common'
      class='default-bordered'>
    </Button>

    <Notification ref='notification'/>
  </div>
`

export default {
  components: { Notification, Button },
  models: {
    Types
  }
}
