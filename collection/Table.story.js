import Table from './Table'

const Stripped = `
  <Table class='stripped'>
    <tr slot='head'>
      <th>ID</th>
      <th>Name</th>
      <th>Description</th>
      <th>Created at</th>
      <th>Updated at</th>
      <th>Actions</th>
    </tr>
    <tr slot='rows' v-for='i in 5'>
      <td>{{ i }}</td>
      <td>{{ user.name }}</td>
      <td>{{ user.description }}</td>
      <td>{{ user.createdAt }}</td>
      <td>{{ user.updatedAt }}</td>
      <td slot='actions'>
        ...
      </td>
    </tr>
  </Table>
`

const Bordered = `
  <Table class='bordered'>
    <tr slot='head'>
      <th>ID</th>
      <th>Name</th>
      <th>Description</th>
      <th>Created at</th>
      <th>Updated at</th>
      <th>Actions</th>
    </tr>
    <tr slot='rows' v-for='i in 5'>
      <td>{{ i }}</td>
      <td>{{ user.name }}</td>
      <td>{{ user.description }}</td>
      <td>{{ user.createdAt }}</td>
      <td>{{ user.updatedAt }}</td>
      <td slot='actions'>
        ...
      </td>
    </tr>
  </Table>
`

const StrippedAndBordered = `
  <Table class='stripped bordered'>
    <tr slot='head'>
      <th>ID</th>
      <th>Name</th>
      <th>Description</th>
      <th>Created at</th>
      <th>Updated at</th>
      <th>Actions</th>
    </tr>
    <tr slot='rows' v-for='i in 5'>
      <td>{{ i }}</td>
      <td>{{ user.name }}</td>
      <td>{{ user.description }}</td>
      <td>{{ user.createdAt }}</td>
      <td>{{ user.updatedAt }}</td>
      <td slot='actions'>
        ...
      </td>
    </tr>
  </Table>
`

export default {
  data: () => ({
    user: {
      name: "jhon",
      description: "Description of John",
      createdAt: "10/10/2018 11:20",
      updatedAt: "10/10/2018 11:20"
    }
  }),
  components: { Table },
  models: {
    Stripped,
    Bordered,
    "Bordered and stripped": StrippedAndBordered
  }
}
