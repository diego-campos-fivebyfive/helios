FRONTEND
========

1. Collection Components

  - 1.1. Button

    - 1.1.1. Properties

      - **icon**: Attribute used to create an icon inside the button:
        - Valid values: Use font awesome icon names avoiding use of `fa-` prefix.

      - **label**: Text content or label to the button or link:
        - Valid values: Text String.

      - **link**: Define the component as `a` tag if has value, else as button:
        - Valid values: Link, not required.

      - **pos**: Element position in inline group, with no space and border rounded:
        - Valid values: first (left rounded), middle (no rounded), last (right rounded), single (full rounded).

      - **type**: Type of button, define colors and patterns:
        - Valid values: primary-common, primary-strong.


1. Solving `Eslint Plugin Vue` Identation Bug:

  - 1.1. copy [this](https://github.com/vuejs/eslint-plugin-vue/edit/master/lib/utils/indent-common.js) file content.
  
  - 1.2. clean the node_modules file:
  ```
  $ > node_modules/eslint-plugin-vue/lib/utils/indent-common.js
  ```
  
  - 1.3. open the file and past the content
  ```
  $ nano node_modules/eslint-plugin-vue/lib/utils/indent-common.js
  ```
