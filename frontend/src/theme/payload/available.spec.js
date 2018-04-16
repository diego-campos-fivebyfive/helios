import available from './available'

describe('available()', () => {
  it('should returns false if at least one item is rejected', () => {
    const payload = [
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
    ]
  
    expect(available(payload)).toBe(false)
  })
  
  it('should returns true if all items are not rejected', () => {
    const payload = [
      {
        name: 'name1',
        rejected: false
      },
      {
        name: 'name2',
        rejected: false
      }
    ]
  
    expect(available(payload)).toBe(true)
  })
})
