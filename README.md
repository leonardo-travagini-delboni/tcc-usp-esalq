# Dimensionador Solar Off Grid 🌞🔋  
### TCC – USP/ESALQ - MBA em Engenharia de Software
**Autor:** Leonardo Travagini Delboni
**Orientadora:** Elaine Barbosa de Figueiredo

---

## 📘 Sobre o Projeto

Este projeto tem como objetivo principal o desenvolvimento de uma **Aplicação Web Completa em Yii2 Framework baseado em PHP E Javascript junto a uma API REST segura e escalável** para o **dimensionamento de sistemas fotovoltaicos off-grid**, com base no consumo elétrico, coordenadas geográficas e banco de componentes solares (painéis, MPPTs e baterias) para **todo o território nacional brasileiro**.

Desenvolvido como Trabalho de Conclusão de Curso (TCC) para o **MBA em Engenharia de Software (USP/ESALQ)**, o sistema visa facilitar a expansão do abastecimentos elétrico para regiões desconectadas da rede elétrica (Sistema Interligado Nacional)

---

## ⚙️ Tecnologias Utilizadas

- **Yii2 Framework (PHP)** – Backend REST
- **HTML, CSS e JS (Web)** – Frontend Web
- **MySQL & MariaDB** – Banco de dados Relacional
- **MySQL Workbench & PHP My Admin** – Gerenciamento Prático do Database
- **Bearer Token** – Método de Autenticação segura
- **Postman** – Testes e Documentação da API
- **Composer** – Gerenciamento de dependências
- **Git** – Controle de versão
- **GitHub** – Repositório em Nuvem

---

## 🧠 Estrutura da API

### 🔐 Autenticação

| Rota                         | Método | Descrição                    |
|------------------------------|--------|------------------------------|
| `<meu-site>/api-site/signup` | POST   | Criação de novo usuário      |
| `<meu-site>/api-site/login`  | POST   | Login e obtenção de token    |
| `<meu-site>/api-site/logout` | POST   | Logout e expiração do token  |


Todas as requisições, com exceção da criação de novo usuário, necessitam de um Bearer Token válido. 

---

### ⚡ Consumo Elétrico (por usuário)

- Usuário cadastra seus equipamentos a serem supridos, além de suas informações técnicas e estimativa de utilização
- CRUD completo restrito ao próprio usuário logado
- Tabelas interligadas por `user_id`
- Rotas da API: `<meu-site>/api-consumo`

---

### 📍 Coordenadas

- Disponível apenas para leitura
- Modificações restritas a administradores
- Rotas da API: `<meu-site>/api-coordenada`

---

### 📦 Componentes (Admin Only)

| Entidade     | Rota                       | Observações                        |
|--------------|----------------------------|------------------------------------|
| Painéis      | `<meu-site>/api-painel`    | Apenas `component_id = 2`          |
| MPPTs        | `<meu-site>/api-mppt`      | Apenas `component_id = 3` (filtro) |
| Baterias     | `<meu-site>/api-bateria`   | Apenas `component_id = 4` (filtro) |

- Admins podem: `create`, `update`, `delete`.  
- Usuários comuns podem: `index`, `view`.

---

### 📊 Dimensionamento Solar

- Cada dimensionamento está ligado a um `user_id`
- Consome dados de coordenadas, consumo, e componentes
- CRUD protegido por autenticação
- Rota: `<meu-site>/api-dimensionamento`

---

## 🔐 Segurança

- Autenticação via `Bearer Token`
- Filtro de acesso por usuário
- Verificações `is_admin` para rotas sensíveis
- Atualização automática do `token_expiration`

---

## 📹 Vídeos Explicativos

Acesse os vídeos demonstrativos do sistema:

### 🎯 Tutorial Completo da Plataforma

| Vídeo | Descrição | Link |
|-------|-----------|------|
| **1️⃣ Cadastro na Plataforma** | Como se cadastrar no sistema | [![YouTube](https://img.shields.io/badge/YouTube-FF0000?style=flat&logo=youtube&logoColor=white)](https://youtu.be/H2BWcAhJkkk) |
| **2️⃣ Cenário 1 - Validação da Metodologia** | Validação por cenário da referência bibliográfica | [![YouTube](https://img.shields.io/badge/YouTube-FF0000?style=flat&logo=youtube&logoColor=white)](https://youtu.be/L0RJVvT57Wk) |
| **3️⃣ Cenário 2 - Estudo de Caso** | Cenário de iluminação Noturna de setor da USP/ESALQ | [![YouTube](https://img.shields.io/badge/YouTube-FF0000?style=flat&logo=youtube&logoColor=white)](https://youtu.be/JVR_joAfxV4) |
| **4️⃣  REST API e Postman** | Uso do Postman para validação e demonstração da REST API | [![YouTube](https://img.shields.io/badge/YouTube-FF0000?style=flat&logo=youtube&logoColor=white)](https://youtu.be/7T5CEUKHYmI) |

---

*Projeto desenvolvido como TCC para MBA em Engenharia de Software - USP/ESALQ*

---