const available = payload =>
  payload.reduce((acc, item) => (
    acc && !item.rejected
  ), true)

export default available
