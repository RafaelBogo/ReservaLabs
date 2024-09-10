# Reserva de Laboratórios - Guia do Usuário

## Descrição

O sistema de Reserva de Laboratórios foi desenvolvido como parte de um trabalho de Oficina de Desenvolvimento Web. A aplicação permite que usuários comuns façam reservas e que administradores gerenciem laboratórios e reservas. Este guia tem como objetivo ajudar os usuários a entenderem como utilizar o sistema.

## Funcionalidades

### Cadastro de Usuário

1. **Acesse a Página de Cadastro:**
   - Navegue até a página de cadastro do sistema.

2. **Preencha os Campos:**
   - **Nome:** Insira seu nome completo.
   - **E-mail:** Insira um e-mail válido.
   - **Senha:** Crie uma senha segura.
   - **Tipo de Usuário:** Selecione "Usuário Comum" ou "Administrador".

3. **Confirmação de Cadastro:**
   - Após preencher o formulário, um código de verificação será enviado para o seu e-mail.
   - Insira o código de verificação na página de confirmação para concluir o cadastro.

### Login

1. **Acesse a Página de Login:**
   - Navegue até a página de login do sistema.

2. **Insira suas Credenciais:**
   - **E-mail:** Insira o e-mail cadastrado.
   - **Senha:** Insira a senha criada durante o cadastro.

3. **Acesse o Sistema:**
   - Clique em "Entrar" para acessar o sistema.

### Realizar Reserva

1. **Acesse a Página de Reservas:**
   - Após fazer login, navegue até a página de reservas.

2. **Preencha os Detalhes da Reserva:**
   - **Laboratório:** Selecione o laboratório desejado.
   - **Data:** Escolha a data da reserva.
   - **Hora de Início:** Insira a hora de início da reserva.
   - **Hora de Fim:** Insira a hora de término da reserva.
   - **Descrição:** Adicione uma descrição para a reserva.

3. **Conclua a Reserva:**
   - Clique em "Reservar" para finalizar a reserva.
   - Um e-mail de confirmação será enviado com os detalhes da reserva.

### Visualizar Reservas

1. **Acesse a Página de Visualização de Reservas:**
   - Navegue até a página "Minhas Reservas" para ver suas reservas.

2. **Ver Detalhes das Reservas:**
   - A página exibirá uma lista com todas as suas reservas recentes.

### Excluir Reserva

1. **Acesse a Página de Visualização de Reservas:**
   - Navegue até a página "Minhas Reservas".

2. **Selecione a Reserva para Exclusão:**
   - Clique no botão "Excluir" ao lado da reserva que deseja remover.
   - A reserva será removida do sistema.

### Gerenciamento de Laboratórios (Administrador)

1. **Cadastrar Novo Laboratório:**
   - Navegue até a página "Cadastrar Laboratório".
   - Preencha os detalhes do novo laboratório (nome, número de computadores, bloco e sala).
   - Clique em "Salvar" para adicionar o laboratório.

2. **Visualizar Reservas de um Laboratório:**
   - Navegue até a página "Ver Reservas de Laboratório".
   - Selecione o laboratório desejado para ver todas as reservas.
  
### Exclusão de Usuários e Laboratórios (Administrador)

**Excluir Laboratório:**

   - Navegue até a página "Excluir" no menu de administração.
   - Selecione um laboratório da lista.
   - Clique em "Excluir Laboratório" para removê-lo do sistema.
   - Isso também removerá todas as reservas associadas ao laboratório.
     
**Excluir Usuário:**

   - Na mesma página "Excluir", selecione um usuário da lista.
   - Clique em "Excluir Usuário" para removê-lo do sistema.
   - Todas as reservas feitas por esse usuário também serão excluídas.
---

## Importação do Banco de Dados

Para importar o banco de dados, siga os passos abaixo:

1. Abra o MySQL Workbench e conecte-se ao seu servidor.
2. Vá para `Server > Data Import`.
3. Selecione "Import from Self-Contained File" e escolha o arquivo `reserva_laboratorios.sql`.
4. Selecione ou crie um banco de dados para importar.
5. Clique em "Start Import" para importar o banco de dados.
