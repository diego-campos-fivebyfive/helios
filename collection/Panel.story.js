import Panel from './Panel'
import Button from './Button'
import Table from './Table'

const Types = `
  <div :style="container">
    <!-- Simple panel -->
    <Panel>
      <div slot='header'>
        Simple panel header
      </div>
      <div slot='section'>
        Loren Ipsun it doilor et conseqt
      </div>
      <div slot='footer' :style="footer">
        Some other information
      </div>
    </Panel>
    <!-- Simple panel -->

    <!-- With title class -->
    <Panel>
      <div slot='header' class='title'>
        With class 'title'
      </div>
      <div slot='section'>
        Loren Ipsun it doilor et conseqt
      </div>
    </Panel>
    <!-- With title class -->

    <!-- With span and class sub -->
    <Panel>
      <div slot='header' class='title'>
       With class 'sub' <span class='sub'>saved at 18h00</span>
      </div>
      <div slot='section'>
        Loren Ipsun it doilor et conseqt
      </div>
    </Panel>
    <!-- With span and class sub -->

    <!-- Using header slot as a filter -->
    <Panel>
      <div slot='header' class='menu'>
        <label> With filters </label>
        <Button
          :action='() => {}'
          label='Exibir em grade'
          class='default-bordered'>
        </Button>
        <Button
          :action='() => {}'
          label='Exibir em lista'
          class='default-bordered'>
        </Button>
      </div>
      <div slot='section'>
        <Table class='bordered'>
          <tr slot='head'>
            <th>ID</th>
            <th>Message</th>
          </tr>
          <tr slot='rows'>
            <td>1</td>
            <td>Hey!</td>
          </tr>
        </Table>
      </div>
    </Panel>
    <!-- Using header slot as a filter -->
  </div>
`

export default {
  components: { Panel, Button, Table },
  models: {
    Types
  },
  computed: {
    container() {
      return `
        background: #d6d6d6;
        padding: 5px;
        height: 680px;
      `
    },
    footer() {
      return `
        padding: 5px;
        font-size: 10px;"
      `
    }
  }
}
