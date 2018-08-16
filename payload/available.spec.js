import available from './available'

const setAttr = (field, key, value) => {
  field[key] = value
  return field
}

describe('available()', () => {
  it('should returns false if at least one item is rejected', () => {
    const schemaOne = available([
      {
        name: 'name1',
        rejected: false
      },
      {
        name: 'name2',
        rejected: true
      },
      {
        name: 'name3',
        rejected: false
      }
    ], setAttr)

    const schemaTwo = available([
      {
        name: 'name1',
        rejected: false
      },
      {
        name: 'name2',
        rejected: false
      }
    ], setAttr)

    const expected = false

    expect(schemaOne).toBe(expected)
    expect(schemaTwo).not.toBe(expected)
  })

  it('should returns false when an item is required, has no value and is not rejected', () => {
    const schema = available([
      {
        name: 'name1',
        value: '',
        required: true,
        rejected: false
      }
    ], setAttr)

    const expected = false

    expect(schema).toBe(expected)
  })
})
