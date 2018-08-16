const available = payload =>
  payload.every(item => !item.rejected)

export default available
