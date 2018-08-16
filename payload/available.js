const available = payload =>
  payload.every(item => {
    if(
      item.required
      && !item.value
      && !item.reject
    ) {
      return false
    }

    return !item.rejected
  })

export default available
