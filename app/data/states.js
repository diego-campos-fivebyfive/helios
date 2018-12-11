export const countryRegions = {
  S: { abbrev: 'S', name: 'Sul' },
  SE: { abbrev: 'SE', name: 'Sudeste' },
  CO: { abbrev: 'CO', name: 'Centro-Oeste' },
  N: { abbrev: 'N', name: 'Norte' },
  NE: { abbrev: 'NE', name: 'Nordeste' }
}

/* NOTE: regions are asc sorted by total states in regions */

export const countryStates = {
  PR: { uf: 'PR', name: 'Paraná', region: countryRegions['S'] },
  RS: { uf: 'RS', name: 'Rio Grande do Sul', region: countryRegions['S'] },
  SC: { uf: 'SC', name: 'Santa Catarina', region: countryRegions['S'] },
  ES: { uf: 'ES', name: 'Espírito Santo', region: countryRegions['SE'] },
  MG: { uf: 'MG', name: 'Minas Gerais', region: countryRegions['SE'] },
  RJ: { uf: 'RJ', name: 'Rio de Janeiro', region: countryRegions['SE'] },
  SP: { uf: 'SP', name: 'São Paulo', region: countryRegions['SE'] },
  DF: { uf: 'DF', name: 'Distrito Federal', region: countryRegions['CO'] },
  GO: { uf: 'GO', name: 'Goías', region: countryRegions['CO'] },
  MT: { uf: 'MT', name: 'Mato Grosso', region: countryRegions['CO'] },
  MS: { uf: 'MS', name: 'Mato Grosso do Sul', region: countryRegions['CO'] },
  AC: { uf: 'AC', name: 'Acre', region: countryRegions['N'] },
  AP: { uf: 'AP', name: 'Amapá', region: countryRegions['N'] },
  AM: { uf: 'AM', name: 'Amazonas', region: countryRegions['N'] },
  PA: { uf: 'PA', name: 'Pará', region: countryRegions['N'] },
  RO: { uf: 'RO', name: 'Rondônia', region: countryRegions['N'] },
  RR: { uf: 'RR', name: 'Roraíma', region: countryRegions['N'] },
  TO: { uf: 'TO', name: 'Tocantins', region: countryRegions['N'] },
  AL: { uf: 'AL', name: 'Alagoas', region: countryRegions['NE'] },
  BA: { uf: 'BA', name: 'Bahia', region: countryRegions['NE'] },
  CE: { uf: 'CE', name: 'Ceará', region: countryRegions['NE'] },
  MA: { uf: 'MA', name: 'Maranhão', region: countryRegions['NE'] },
  PB: { uf: 'PB', name: 'Paraíba', region: countryRegions['NE'] },
  PE: { uf: 'PE', name: 'Pernambuco', region: countryRegions['NE'] },
  PI: { uf: 'PI', name: 'Piauí', region: countryRegions['NE'] },
  RN: { uf: 'RN', name: 'Rio Grande do Norte', region: countryRegions['NE'] },
  SE: { uf: 'SE', name: 'Sergipe', region: countryRegions['NE'] }
}

/* NOTE: states are asc sorted by total states in regions */
