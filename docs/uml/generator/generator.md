
### Parâmetros iniciais

1. __level__: Nível da conta associada ao processo (utilizado em processo que antecede o gerador)
2. __power__: Potência necessária (desejada)
3. __fdi_min__: FDI mínimo
4. __fdi_max__: FDI máximo
5. __phase_number__: Número de fases da rede (1, 2, ou 3)
6. __phase_voltage__: Tensão da rede (220, 380)
7. __inverters__: Coleção de inversores 
8. __string_boxes__: Coleção de string boxes
9. __module__: Módulo utilizado no sistema

### Schemas 

Estrutura de dados necessária para que o gerador efetue o processo.
Os schemas abaixo determinam a estrutura de cada tipo de componente:

```

inverter = [
    'id' => int,
    'active' => bool,
    'alternative' => int|null,
    'phase_number' => int,
    'phase_voltage' => int,
    'compatibility' => int,
    'nominal_power' => float,
    'min_power_selection' => float|null,
    'max_power_selection' => float|null,
    'mppt_parallel' => bool,
    'mmpt_number' => int,
    'mppt_min' => int,
    'in_protection' => bool,
    'max_dc_voltage' => float,
    'mppt_max_dc_current' => float
]

'module' => [
    'id' => int,
    'max_power' => float
    'voltage_max_power' => float
    'open_circuit_voltage' => float
    'short_circuit_current' => float
    'temp_coefficient_voc' => float
]

string_box => [
    'id' => int,
    'inputs' => int,
    'outputs' => int
 ]

```


### Input

Formato de entrada de dados para os cálculos do gerador:

```

    [
        'module' => array de dados do módulo, no formato (Schema::module) | required
        'inverters' => array de inversores, cada um no formato (Schema::inverter) | required
        'string_boxes' => array de string box, cada um no formato (Schema::string_box) | default : []
        'power' => Potência inicial requerida (desejada) | default : 0
        'fdi_min' => FDI mínimo | default : 0.75
        'fdi_max' => FDI máximo | default : 1.3
        'phase_voltage' => Tensão da rede | default : 220
        'phase_number' => Número de fases da rede | default : 1
    ]

```


### Output

Formato de saída de dados após o cálculo do gerador:

``` 
    
    [
        'module' => array idêntico ao de entrada
        'inverters' => array de inversores resultantes do processo
        'arragements' => array de arranjos de mppt
        'string_boxes' => array de string boxes resultantes do processo
    ]
    
```
