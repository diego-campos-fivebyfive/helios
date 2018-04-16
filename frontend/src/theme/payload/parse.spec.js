import parse from './parse'

describe('parse()', () => {
  it('should builds an object tree putting the payload items into their right path', () => {
    const payload = [
      {
        name: 'attr3',
        value: 'valueX',
        path: ['attr1', 'attr2']
      },
      {
        name: 'attr4',
        value: 'valueY',
        path: ['attr1', 'attr2']
      },
      {
        name: 'attr6',
        value: 'valueZ',
        path: ['attr5']
      }
    ]

    const expected = {
      attr1: {
        attr2: {
          attr3: 'valueX',
          attr4: 'valueY'
        }
      },
      attr5: {
        attr6: 'valueZ'
      }
    }

    expect(parse(payload)).toEqual(expected)
  })
})
