const available = (payload, setAttr) =>
  payload.every(item => {
    if(
      item.required
      && !item.value
      && !item.rejected
    ) {
      if (setAttr) {
        setAttr(item, 'rejected', true)
      }

      return false
    }

    return !item.rejected
  })

export default available
