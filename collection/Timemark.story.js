import faker from 'faker'
import Timemark from './Timemark'

const Default = `
  <Timemark
    title="${faker.lorem.words(3)}"
    description="${faker.lorem.sentences(3)}"
    createdAt="2017-08-28 12:27:17"
    :showTimeAgo="true"
    :links="[{
      title: '${faker.finance.transactionType()}',
      href: '${faker.internet.url()}'
    },{
      title: '${faker.finance.transactionType()}',
      href: '${faker.internet.url()}'
    }]">
  </Timemark>
`

export default {
  components: { Timemark },
  models: {
    Default
  }
}
