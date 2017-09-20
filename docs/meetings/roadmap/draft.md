:: SICES - ADMINISTRAÇÃO
ROLES
Platform:
ROLE_PLATFORM_MASTER - (Mauro)
ROLE_PLATFORM_ADMIN  - (Jackson)
ROLE_PLATFORM_AGENT  - (Comercial)
Regras de Cadastro

Somente MASTER E ADMIN podem cadastrar, a opção de roles é só ADMIN e AGENT.
Cadastro de MASTER somente via equipe de desenvolvimento.
:: INTEGRADORES - CONTAS
Account
ROLE_OWNER_MASTER - Dono da conta (principal), Administrador
ROLE_OWNER    - Administrador (Cadastrado pelo dono da conta)
ROLE_USER     - Usuário (Cadastrado por um dos anteriores)

ACCOUNT_STATUS
PENDING     - Pendente (Cadastro iniciado mas email não verificado)   LV
VERIFIED    - Verificado (Email verificado)
CONFIRMED   - Confirmado (Conta confirmada por um dos users ROLE_PLATFORM) LA
ACTIVATED   - Ativado (Conta ativada, senha de acesso configurada)
LOCKED      - Bloqueado (Conta bloqueada, todos os acessos bloqueado)

Links
- (LV) Link de verificação: Apenas para validar a existência do email do integrador
- (LA) Link de ativação: Link de acesso à configuração de senha e acesso à plataforma.

----------------------------------------------------------------------------------------------------------------------------

- quando ocorrer o cadastro pela plataforma (registrar-se), deve ser enviado o (LV).
-- contas criadas por este recurso devem aparecer para todos os ROLE_PLATFORM_*
-- um intregrador pode ser vinculado a qualquer ROLE_PLATFORM_*
-- ao aprovar:
--- caso o aprovador seja MASTER ou ADMIN, pode ser selecionado outro user ROLE_PLATFORM_ ou mantido o mesmo.
--- caso o aprovador seja AGENT, deve vincular a ele mesmo.
- user/sices e master/sices podem cadastrar um novo integrador.
-- podem cadastrar e não aprovar (não envia email).
-- podem cadastrar e aprovar (neste caso, envia link de config de senha de acesso ao dono da conta - LA (status=CONFIRMED)).
- MASTER e ADMIN podem vincular/trocar um vínculo entre outros ROLE_PLATFORM_* e integrador.

Na lista de integradores
- Filtros de status
- Filtro de vínculo
- Campo de busca (cnpj, email, nome, etc)
Devem aparecer (tabela):
Nome Fantasia, Cnpj, Email (dono), Status, [Actions]

No mais detalhes:
- Todos os dados preenchidos no cadastro
- Lista de usuários vinculados à conta com status(role)
--- Nome, Email, Role

ACCOUNT_STATUS
PENDING - 0
VERIFIED - 1
CONFIRMED - 2
ACTIVATED - 3
LOCKED - 4
