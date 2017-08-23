
### Gerador de Sistemas ###

    URI: /api/generator
    Endpoints: 
        Method  |   Endpoint    |   Descrição
        POST        /generate       Gera um sistema conforme os parâmetros informados
        GET         /options        Retorna as opções disponíveis para uso no endpoint /generate


- /options : Devolve uma lista de opções.
- /options?option=roof_type : Devolve uma lista de opções conforme o filtro.
- /generate : Gera um sistema conforme os parâmetros informados.
     
#### Exemplo de saída para o endpoint /options ####

    {
        "defaults": {
            "address": null,
            "latitude": null,
            "longitude": null,
            "customer": null,
            "stage": null,
            "roof_type": "ROOF_ROMAN_AMERICAN",
            "source": "consumption",
            "power": 0,
            "consumption": 0,
            "use_transformer": true,
            "grid_voltage": "127/220",
            "grid_phase_number": "Biphasic",
            "module": 32433,
            "inverter_maker": 60627,
            "structure_maker": 61211,
            "string_box_maker": 61209,
            "errors": []
        },
        "roof_type": [
            "ROOF_ROMAN_AMERICAN",
            "ROOF_CEMENT",
            "ROOF_FLAT_SLAB",
            "ROOF_SHEET_METAL",
            "ROOF_SHEET_METAL_PFM"
        ],
        "grid_voltage": [
            "127/220",
            "220/380"
        ],
        "grid_phase_number": [
            "Monophasic",
            "Biphasic",
            "Triphasic"
        ],
        "module": 32433,
        "inverter_maker": 60627,
        "structure_maker": 61211,
        "string_box_maker": 61209
    }

#### Exemplo de saída para o endpoint /options?option=grid_voltage ####

    [
        "127/220",
        "220/380"
    ]

### Exemplo de saída para o endpoint /options?option=structure_maker ###

    [
        {
            "id": 61211,
            "context": "structure",
            "name": "Sices Solar",
            "enabled": true,
            "created_at": "2017-07-31T22:38:34+0000",
            "updated_at": "2017-07-31T22:38:35+0000"
        },
        {
            "id": 61212,
            "context": "structure",
            "name": "K2 System",
            "enabled": true,
            "created_at": "2017-07-31T22:38:48+0000",
            "updated_at": "2017-07-31T22:38:49+0000"
        }
    ]
    
### Exemplo de saída para o endpoint /options?option=inverter_maker&power=50 ###

    [
        {
            "id": 60627,
            "context": "inverter",
            "name": "ABB Group",
            "enabled": true,
            "created_at": "2017-01-09T21:28:32+0000",
            "updated_at": "2017-01-09T21:28:32+0000"
        },
        {
            "id": 60630,
            "context": "inverter",
            "name": "Fronius International GmbH",
            "enabled": true,
            "created_at": "2017-01-09T21:28:33+0000",
            "updated_at": "2017-01-09T21:28:33+0000"
        }
    ]
    
##### Atenção ##### 
- Caso não sejam fornecidos filtros a o sistema assumirá os valores disponíveis na chave "default".
- Quando aplicados os filtros power e grid_phase_number na mesma consulta, o sistema combinará ambos de forma que retorne somente os compatíveis encontrados.
- O retorno padrão de option para os itens: module, inverter_maker, structure_maker e string_box_maker é inteiro, diferente do retorno com filtros aplicados (lista de entidades).