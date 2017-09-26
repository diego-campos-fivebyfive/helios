PROTHEUS INTEGRATION
====================

## Base request

  - 1.1 **Headers**:

    - NRANGEINI: Inicial Recno
    - NRANGEFIN: Final Recno
    - NPAGESEEK: Page number (pagination)

## Ambiences

  - **Homolog**: http://restprod.sices.eu:1904/rest
  - **Production**: http://rest.sices.eu:1902/rest

## Routes

  - 1.1 **Client - GET**: `tlistcli`
    - Homolog: Error `invalid class TJSONCLI`
      ```
      curl -X GET -H 'Content-Type:application/json' -H 'NRANGEINI:100' -H 'NRANGEFIN:100' -H 'NPAGESEEK:1' http://rest.sices.eu:1902/rest/tlistcli
      ```
    - Production: Timeout
      ```
      curl -X GET -H 'Content-Type:application/json' -H 'NRANGEINI:100' -H 'NRANGEFIN:100' -H 'NPAGESEEK:1' http://restprod.sices.eu:1904/rest/tlistcli
      ```

  - 1.2 **Product - GET**: `tlistprod`
    - Homolog: OK
      ```
      curl -X GET -H 'Content-Type:application/json' -H 'NPAGESEEK: 2' http://rest.sices.eu:1902/rest/tlistprod
      ```
    - Production: Timeout 
      ```
      curl -X GET -H 'Content-Type:application/json' -H 'NRANGEINI:100' -H 'NRANGEFIN:100' -H 'NPAGESEEK:1' http://restprod.sices.eu:1904/rest/tlistprod
      ```

  - 1.3 **Order - POST**: `jsorcisquik`
    - Homolog: OK, `... { STATUS: processado com sucesso} ...`
      ```
      curl -X POST -H 'Content-Type:application/json' -d @$SICES_PATH/docs/integrations/protheus/mock/post-order.json http://rest.sices.eu:1902/rest/jsorcisquik
      ```
    - Production: Not tested yet
      ```
      
      ```


