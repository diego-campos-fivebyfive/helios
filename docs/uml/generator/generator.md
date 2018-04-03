
### Parâmetros iniciais

1. __level__: Nível da conta associada ao processo
2. __power__: Potência necessária (desejada)

### Schemas 

Estrutura de dados necessária para que o gerador efetue o processo.

Os schemas abaixo determinam a estrutura de cada tipo de componente, o gerador recebe uma 
coleção destes componentes para processamento.

```

inverter = [
    'id' => int,
    'active' => bool,
    'alternative' => int|null,
    'phase_number' => int,
    'phase_voltage' => float,
    'compatibility' => int,
    'nominal_power' => float,
    'max_power' => float,
    'min_power_selection' => float|null,
    'min_power_selection' => float|null,
    'mppt_parallel' => int,
    'mmpt_number' => int,
    'mppt_min' => int,
    'in_protections' => int|null,
    'max_dc_voltage' => float,
    'mppt_max_dc_current' => float,
    'open_circuit_voltage' => float,
    'voltage_max_power' => float,
    'temp_coefficient_voc' => float,
    'short_circuit_current' => float,
    'arragements' => array (parâmetro exclusivo para saída),
    'stringboxes' => array (parâmetro exclusivo para saída)
]

string_box => [
    'inputs' => int,
    'outputs' => int
 ]

```
