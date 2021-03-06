FRONTEND
========

1. Collection Components

  - 1.1. Button

    - 1.1.1. Properties

      - **icon**: Attribute used to create an icon inside the button:
        - Valid values: Use font awesome icon names avoiding use of `fa-` prefix.

      - **label**: Text content or label to the button or link:
        - Valid values: Text String, not required.

      - **link**: Define the component as `a` tag if has value, else as button:
        - Valid values: Link, not required.

      - **pos**: Element position in inline group, with no space and border rounded:
        - Valid values: first (left rounded), middle (no rounded), last (right rounded), single (full rounded).

      - **type**: Type of button, define colors and patterns:
        - Valid values: primary-common, primary-strong.


2. Using Local Backend API

  Before you start webpack's dev server you must sign in and export `PHPSESSID` Session ID:

    ```
    $ export SICES_PHPSESSID=3fhdfl9r6dgautom48ls4eg064
    ```

3. Codding style

  Classes should define objects instead of functions:
  ```
  // Good
  .title {
    text-align: left;
  }

  // Bad
  .left {
    text-align: left;
  }
  ```

4. Structure

  Components
  |- Collection
  |- Template
  |- Pages

  - 4.1. Collection:

    - Uses no scoped styles
    - Uses BEM nomenclatures to own elements classes
    - All component's BEM element classes must be prefixed by `.collection-`
    - All BEM elements must not uses nesting
    - Does not use BEM nomenclatures to default style to external elements classes
    - Default styles to external elements classes must be placed inside own BEM classes
      - Default component's types that uses BEM elements inside, should be declared using placeholder
      ```
      // Bad
      .collection-table {
        .stripped {
          .collection-table-header {
            //some code
          }
        }
      }

      // Good
      %stripped {
        .collection-table-header {
          //some code
        }
      }

      .collection-table {
        &.stripped {
          @extend %stripped;
        }
      }
      ```

  - 4.2. Template:

    - All parts that are not pages
    - Uses scoped styles
    - Does not use BEM nomenclatures

  - 4.3. Modules:

    - All routed components (`router-vue`)
    - Uses scoped styles
    - Does not use BEM nomenclatures

    4.3.1. Application

      - Componentes "genéricos" da aplicação, são incluidos na pasta application, onde são alocados por grupo (pasta) e por dados (arquivo).
        Para inclusão do componente o importamos com o seguinte nome de váriavel: `Nome do arquivo no singular` + `Grupo`.

        ex.:
        ```javascript
        import AccountSelect from 'application/select/Accounts'

        export default {
           components: {
             AccountSelect
           }
        }
        ```

        Onde:
          O componente de select de `Accounts` encontrasse na pasta `select`, logo a variavel será: `AccountSelect`.

5. Forms

  `v-model` properties must be mapped inside a form data property:
    ```
    // Bad
    data: () => ({
      coupon: {}
    })

    // Bad
    data: () => ({
      coupon: {
        name: null
      }
    })

    // Bad
    data: () => ({
      form: {}
    })

    // Good
    data: () => ({
      form: {
        name: null
      }
    })
    ```
6 - Layout
  - **z-index**:
    - Root: 0 to 50
    - Navbar: 100 to 150
    - Sidebar: 200 to 250
    - Modals and floating components: 300 to 350


