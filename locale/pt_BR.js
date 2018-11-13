export default {
  theme: {
    weekdays: {
      sunday: 'domingo',
      monday: 'segunda-feira',
      tuesday: 'terça-feira',
      wednesday: 'quarta-feira',
      thrusday: 'quinta-feira',
      friday: 'sexta-feira',
      saturday: 'sábado'
    },
    months: {
      january: 'janeiro',
      february: 'fevereiro',
      march: 'março',
      april: 'abril',
      may: 'maio',
      june: 'junho',
      july: 'julho',
      august: 'agosto',
      september: 'setembro',
      october: 'outubro',
      november: 'novembro',
      december: 'dezembro'
    },
    requireField(field) {
      return `Campo ${field} requirido`
    },
    getFullDate: ({ dayInTheWeek, day, month, year }) =>
      `${dayInTheWeek}, ${day} de ${month} de ${year}`
    ,
    collection: {
      operations: 'Operações',
      confirm: 'Confirmar',
      close: 'Fechar',
      next: 'Próximo',
      previous: 'Anterior'
    },
    template: {
      refreshing: 'Recarregando',
      pullToRefresh: 'Puxe para atualizar',
      releaseToReload: 'Solte para recarregar',
      signOut: 'Sair'
    }
  }
}
