export default {
  theme: {
    weekdays: {
      sunday: 'domingo',
      monday: 'lunes',
      tuesday: 'martes',
      wednesday: 'miércoles',
      thrusday: 'jueves',
      friday: 'viernes',
      saturday: 'sábado'
    },
    months: {
      january: 'enero',
      february: 'febrero',
      march: 'marzo',
      april: 'abril',
      may: 'mayo',
      june: 'junio',
      july: 'julio',
      august: 'augosto',
      september: 'septiembre',
      october: 'octubre',
      november: 'noviembre',
      december: 'diciembre'
    },
    requireField(field) {
      return `El campo ${field} es obligatorio`
    },
    getFullDate: ({ dayInTheWeek, day, month, year }) =>
      `${dayInTheWeek}, ${day} de ${month} de ${year}`
    ,
    collection: {
      operations: 'Operaciones',
      confirm: 'Confirmar',
      close: 'Cerrar',
      next: 'Próximo',
      previous: 'Anterior'
    },
    template: {
      refreshing: 'Recargando',
      pullToRefresh: 'Tira para actualizar',
      releaseToReload: 'Suelte para recargar',
      signOut: 'Cerrar sesión',
      myData: 'Mis datos'
    }
  }
}
