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
