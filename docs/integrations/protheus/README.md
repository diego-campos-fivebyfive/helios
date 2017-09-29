PROTHEUS INTEGRATION
====================

## Ambiences

  - **Homolog**: http://restprod.sices.eu:1904/rest
  - **Production**: http://rest.sices.eu:1902/rest

## Routes

  - 1.1 **Client**: `tlistcli`

    - **Method**: GET

    - **Headers**:

      - NRANGEINI: Inicial Recno (initial register - ID)
      - NRANGEFIN: Final Recno (final register - ID)

    - **Requests**:

      - Homolog: OK
        ```bash
        curl -X GET -H 'Content-Type:application/json' -H 'NRANGEINI:1' -H 'NRANGEFIN:1' http://rest.sices.eu:1902/rest/tlistcli
        ```
      - Production: OK
        ```bash
        curl -X GET -H 'Content-Type:application/json' -H 'NRANGEINI:1' -H 'NRANGEFIN:1' http://restprod.sices.eu:1904/rest/tlistcli
        ```

    - **Create**: `jsorcisquik`

  - 1.2 **Product**: `tlistprod`

    - **Method**: GET

    - **Requests**:

      - Homolog: OK (NRANGEINI, NRANGEFIN not working)
        ```bash
        curl -X GET -H 'Content-Type:application/json' -H 'NRANGEINI:100' -H 'NRANGEFIN:100' -H 'NPAGESEEK: 1' http://rest.sices.eu:1902/rest/tlistprod
        ```
      - Production: Empty response
        ```bash
        curl -X GET -H 'Content-Type:application/json' -H 'NRANGEINI:100' -H 'NRANGEFIN:100' -H 'NPAGESEEK:1' http://restprod.sices.eu:1904/rest/tlistprod
        ```

  - 1.3 **Order**: `jsorcisquik`

    - **Method**: POST

    - **Body**:
      [post-order.json](mock/post-order.json)

    - **Requests**:

      - Homolog: OK, `... { STATUS: processado com sucesso } ...`
        ```bash
        curl -X POST -H 'Content-Type:application/json' -d @$SICES_PATH/docs/integrations/protheus/mock/post-order.json http://rest.sices.eu:1902/rest/jsorcisquik
        ```
      - Production: Not tested yet
        ```bash
        # not tested yet
        ```

        > **Note**: We should open a proccess notification router, to recieve protheus notification

  - 1.4 **Payment**: `tlistcpgt`

    - **Method**: GET

    - **Headers**:

      - NRANGEINI: Inicial Recno
      - NRANGEFIN: Final Recno
      - NPAGESEEK: Page number (pagination)

    - **Requests**:

      - Homolog: OK
        ```bash
        curl -X GET -H 'Content-Type:application/json' -H 'NRANGEINI: 1' -H 'NRANGEFIN:1' -H 'NPAGESEEK: 1' http://rest.sices.eu:1902/rest/tlistcpgt
        ```
      - Production: OK
        ```bash
        curl -X GET -H 'Content-Type:application/json' -H 'NRANGEINI: 1' -H 'NRANGEFIN:1' -H 'NPAGESEEK: 1' http://restprod.sices.eu:1904/rest/tlistcpgt
        ```

  - 1.5 **Sellers**: `tlistvnd`

    - **Method**: GET

    - **Headers**:

      - NRANGEINI: Inicial Recno
      - NRANGEFIN: Final Recno

    - **Requests**:

      - Homolog: OK
        ```bash
        curl -X GET -H 'Content-Type:application/json' -H 'NRANGEINI: 1' -H 'NRANGEFIN:1' http://rest.sices.eu:1902/rest/tlistvnd
        ```
      - Production: OK
        ```bash
        curl -X GET -H 'Content-Type:application/json' -H 'NRANGEINI: 1' -H 'NRANGEFIN:1' http://restprod.sices.eu:1904/rest/tlistvnd
        ```