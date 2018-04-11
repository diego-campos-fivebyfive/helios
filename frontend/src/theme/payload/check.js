const checkPayload = payload => {
  /* eslint-disable no-use-before-define, no-restricted-syntax */
  const isResolved = (obj, key) => {
    const val = obj[key]

    if (val === Object(val)) {
      return isValid(val)
    }

    return (key === 'rejected' && val)
      ? !isInvalidField(obj)
      : true
  }

  const isValid = obj => {
    for (const key in obj) {
      if (!isResolved(obj, key)) return false
    }

    return true
  }

  return isValid(payload)
  /* eslint-enable no-use-before-define, no-restricted-syntax */
}

export default {
  checkPayload
}
