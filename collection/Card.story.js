import Card from './Card'
import Button from './Button'

const Simple = `
  <div>
    <div style='display: flex'>
      <Card :style='cardSize'>
        <img slot='image' :src='card.image'>
        <div slot='title'>
          Card title
        </div>
        <div slot='info'>
          {{ card.info }}
        </div>
        <div slot='actions'>
          <Button
            :action='() => {}'
            label='View'
            class='primary-bordered'>
          </Button>
          <Button
            :action='() => {}'
            label='Do something'
            class='default-bordered'>
          </Button>
          <Button
            :action='() => {}'
            label='Delete'
            class='danger-bordered'>
          </Button>
        </div>
      </Card>

      <Card :style='cardSize'>
        <div slot='title'>
          Card without image
        </div>
        <div slot='info' style='text-align: justify;'>
          {{ card.longInfo }}
        </div>
        <div slot='actions'>
          <Button
            :action='() => {}'
            label='I agree'
            class='primary-strong'>
          </Button>
          <Button
            :action='() => {}'
            label='I Desagree'
            class='danger-common'>
          </Button>
        </div>
      </Card>
    </div>

    <div style='display: flex'>
      <Card :style='primaryCard'>
        <div slot='title'>
          Primary card
        </div>
        <div slot='info' style='text-align: justify;'>
          {{ card.info }}
        </div>
        <div slot='actions'>
          That's it
        </div>
      </Card>
      <Card :style='successCard'>
        <div slot='title'>
          Success card
        </div>
        <div slot='info' style='text-align: justify;'>
          {{ card.info }}
        </div>
        <div slot='actions'>
          That's it
        </div>
      </Card>
    </div>
  </div>
`

export default {
  components: { Card, Button },
  models: {
    Simple
  },
  data: () => ({
    card: {
      info: `Some quick example text to build on the card title and make up the
      bulk of the card's content.`,
      longInfo: `Lorem ipsum dolor sit amet, consectetur adipiscing elit.
      Vestibulum ultrices tellus et felis rutrum consectetur.
      Aenean vitae porta purus, non fermentum diam. Vestibulum semper ante urna,
       ac venenatis lorem condimentum a. Aliquam non pulvinar nisi.
       Nulla facilisi. Duis sodales turpis velit, vitae lacinia ex pharetra
       sit amet. Nam nunc dui, vulputate ac gravida in, fringilla sed sapien.
       Sed euismod non velit a finibus. Nulla sed urna a mauris mollis aliquam.
       Donec sollicitudin est in euismod eleifend. Morbi interdum, ex at
       faucibus cursus, felis nibh rutrum justo, sed faucibus risus risus in
       nisl.mollis aliquam. Donec sollicitudin est in euismod eleifend. Morbi
       interdumeleifend. Morbi interdum `,
      image:
        "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22286%22%20height%3D%22180%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20286%20180%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_165ca3d056f%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A14pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_165ca3d056f%22%3E%3Crect%20width%3D%22286%22%20height%3D%22180%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22106.3984375%22%20y%3D%2296.3%22%3E286x180%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E"
    }
  }),
  computed: {
    cardSize() {
      return {
        width: '300px',
        margin: '20px'
      }
    },
    primaryCard() {
      return {
        width: '300px',
        color: 'whitesmoke',
        background: '#007bff',
        margin: '20px'
      }
    },
    successCard() {
      return {
        width: '300px',
        color: 'whitesmoke',
        background: '#43c70d',
        margin: '20px'
      }
    }
  }
}
