import available from './available'

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
    ])

    const schemaTwo = available([
      {
        name: 'name1',
        rejected: false
      },
      {
        name: 'name2',
        rejected: false
      }
    ])

    const expected = false

    expect(schemaOne).toBe(expected)
    expect(schemaTwo).not.toBe(expected)
  })
})
