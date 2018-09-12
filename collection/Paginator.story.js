import Paginator from './Paginator'

const Pagination = `
  <div>
    <Paginator :pagination='{
      total: 1000,
      current: 1,
      links: {
        prev: true,
        self: "#?page=1",
        next: "#?page=2"
      }
    }'/>

    <Paginator :pagination='{
      total: 10,
      current: 5,
      links: {
        prev: true,
        self: "#?page=5",
        next: "#?page=6"
      }
    }'/>
  </div>
`
const TotalAndCurrent = `
  <div>
    <Paginator :total='5' :current='1'/>
    <Paginator :total='5' :current='2'/>
  </div>
`

export default {
  components: { Paginator },
  models: {
    'Pagination': Pagination,
    'Total e current': TotalAndCurrent
  }
}
