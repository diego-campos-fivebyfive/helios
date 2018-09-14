import Modal from './Modal'
import Button from './Button'

const Simple = `
<div>
    <Button
      :action='() => $refs.modal.show()'
      label='Open modal'
      class='default-bordered'>
    </Button>

    <Modal ref='modal'>
      <div slot='header'>
        Modal title
      </div>
      <div slot='section' :style='sectionMargin'>
        <ul>
          <li>lorem ipsum dolor sit amet consectetuer adipiscing elit</li>
          <li>lorem ipsum dolor sit amet consectetuer adipiscing elit</li>
          <li>lorem ipsum dolor sit amet consectetuer adipiscing elit</li>
          <li>lorem ipsum dolor sit amet consectetuer adipiscing elit</li>
        </ul>
      </div>
    </Modal>
  </div>
`

export default {
  components: { Modal, Button },
  models: {
    Simple
  },
  computed: {
    sectionMargin() {
      return {
        margin: '30px'
      }
    }
  }
}
