import Confirm from './Confirm'
import Button from './Button'

const Simple = `
  <div>
    <Button
      :action='() => $refs.confirm.show()'
      label='Open confirm'
      class='default-bordered'>
    </Button>

    <Confirm ref='confirm'>
      <div slot='header'>
        Confirmação
      </div>
      <div slot='content'>
        Confirma a exclusão deste item?
      </div>
    </Confirm>
  </div>
`

export default {
  components: { Confirm, Button },
  models: {
    Simple
  }
}
