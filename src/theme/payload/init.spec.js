import init from './init'

const setAttr = (field, key, value) => {
  field[key] = value
  return field
}

describe('init()', () => {
  it('should returns an empty array if receives an empty schema', () => {
    const data = {}

    const schema = {}

    expect(init(schema, data, setAttr)).toEqual([])
  })

  it('should returns each attribute value in the array', () => {
    const data = {}

    const schema = {
      attr1: {},
      attr2: {},
      attr3: {}
    }

    const expected = [
      expect.any(Object),
      expect.any(Object),
      expect.any(Object)
    ]

    expect(init(schema, data, setAttr)).toEqual(expected)
  })

  it('should includes empty attributes in the payload array', () => {
    const data = {}

    const schema = {
      attr1: {
        attr2: {}
      },
      attr3: {
        attr4: {
          attr5: {},
          attr6: {}
        }
      },
      attr7: {},
      attr8: {}
    }

    const expected = [
      expect.any(Object),
      expect.any(Object),
      expect.any(Object),
      expect.any(Object),
      expect.any(Object)
    ]

    expect(init(schema, data, setAttr)).toEqual(expected)
  })

  it('should includes attributes when it contains an attribute that is not object in the payload array', () => {
    const data = {}

    const schema = {
      attr1: {
        attr2: {
          attrX: 'attrX',
          attr3: {}
        }
      }
    }

    const expected = [
      expect.objectContaining({})
    ]

    expect(init(schema, data, setAttr)).toEqual(expected)
  })

  it('should returns the leafs and keep their attributes in the payload array', () => {
    const data = {}

    const schema = {
      attr1: {
        type: 'type1'
      }
    }

    const expected = [
      expect.objectContaining({
        type: 'type1'
      })
    ]

    expect(init(schema, data, setAttr)).toEqual(expected)
  })

  it('should returns the key as name attribute to leaf in the payload array', () => {
    const data = {}

    const schema = {
      attr1: {
        attr2: {}
      }
    }

    const expected = [
      expect.objectContaining({
        name: 'attr2'
      })
    ]

    expect(init(schema, data, setAttr)).toEqual(expected)
  })

  it('should includes all the leaf\'s parents key in a path array attribute, ordened by the composition order', () => {
    const data = {}

    const schema = {
      attr1: {
        attr2: {
          attr3: {},
          attr4: {
            attr5: {}
          }
        }
      }
    }

    const expected = [
      expect.objectContaining({
        path: ['attr1', 'attr2']
      }),
      expect.objectContaining({
        path: ['attr1', 'attr2', 'attr4']
      })
    ]

    expect(init(schema, data, setAttr)).toEqual(expected)
  })

  it('should rewrite the reserved attribute in the leaf object if it is already defined', () => {
    const data = {}

    const schema = {
      attr1: {
        name: 'attrX',
        path: ['pathX'],
        type: 'typeX'
      }
    }

    const expected = [
      expect.objectContaining({
        name: 'attr1',
        path: []
      })
    ]

    expect(init(schema, data, setAttr)).toEqual(expected)
  })

  it('should includes to the leaf value attribute the current data value', () => {
    const data = {
      attr1: {
        attr2: 'attrX'
      }
    }

    const schema = {
      attr1: {
        attr2: {}
      }
    }

    const expected = [
      expect.objectContaining({
        value: 'attrX'
      })
    ]

    expect(init(schema, data, setAttr)).toEqual(expected)
  })

  it('should includes to the leaf value attribute a null value if the current data value is not defined', () => {
    const data = {
      attr1: 'attrX'
    }

    const schema = {
      attr1: {},
      attr2: {}
    }

    const expected = [
      expect.objectContaining({
        value: 'attrX'
      }),
      expect.objectContaining({
        value: null
      })
    ]

    expect(init(schema, data, setAttr)).toEqual(expected)
  })
})
