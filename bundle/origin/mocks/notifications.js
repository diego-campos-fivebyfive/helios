const notifications = {
  productCreated: {
    callback: 'product_created',
    body: {
      codes: ['ABC', 'DEF', 'HIJ', 'KLM', 'NOP']
    }
  },
  memorialCreated: {
    callback: 'memorial_created',
    body: {
      id: 1
    }
  }
}


module.exports = notifications
