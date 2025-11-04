# WorkMatch - API

## Descrição do Projeto

O **WorkMatch** é um sistema criado para facilitar a conexão entre pessoas que buscam serviços temporários e contratantes que necessitam desse tipo de mão de obra. Ele supre a dificuldade de localizar oportunidades de trabalho ágeis e informais, permitindo o cadastro de contratados e contratantes, publicação de ofertas, candidatura rápida e gerenciamento completo das contratações. O sistema também promove confiança entre as partes por meio de avaliações mútuas, validação de documentos e exibição de informações essenciais do perfil. Com recursos como filtragem de vagas por cidade, acompanhamento do status da candidatura e contato direto pelo WhatsApp, o WorkMatch torna o processo de contratação mais seguro, eficiente e acessível tanto para quem precisa trabalhar quanto para quem precisa contratar.

## Tecnologias Utilizadas

- **Laravel** (Backend)
- **PostgreSQL** (Banco de Dados)

## Requisitos

- **PHP** (>= 8.2)
- **Composer** (>= 2.7.7)
- **Node.js** (>= 22.21.1)
- **PostgreSQL** (>= 16.4)

### Extensões PHP Necessárias

Para rodar o Laravel corretamente, você deve garantir que as seguintes extensões PHP estejam habilitadas:

- **cURL** (extension=curl)
- **FileInfo** (extension=fileinfo)
- **OpenSSL** (extension=openssl)
- **PDO_PGSQL** (extension=pdo_pgsql)
- **PGSQL** (extension=pgsql)
- **Zip** (extension=zip)
- **MB_String** (extension=mbstring)

## Instalação

Siga os passos abaixo para configurar e rodar o projeto localmente.

### Passo 1: Clonar o Repositório

```bash
git clone https://github.com/G4BR13L-R/WorkMatchAPI.git
cd WorkMatchAPI
```

### Passo 2: Instalar Dependências

- Instale as dependências do PHP usando o Composer:
  ```bash
  composer install
  ```

- Instale as dependências do Node.js:
  ```bash
  npm install
  ```

### Passo 3: Configurar o Banco de Dados

1. Crie um banco de dados no PostgreSQL.
2. Renomeie o arquivo `.env.example` para `.env` e configure as credenciais de acesso ao banco de dados:
   
   ```bash
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=work_match
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   ```

3. Gere a chave do Laravel:
   ```bash
   php artisan key:generate
   ```

4. Execute as migrações do banco de dados:
   ```bash
   php artisan migrate
   ```

5. Execute as seeds do banco de dados:
   ```bash
   php artisan db:seed
   ```

### Passo 4: Executar o Projeto

Inicie o servidor do Laravel:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Desenvolvedor

- **Gabriel Silva de Rezende**
- CGM: 802.239

## Licença

Este projeto é desenvolvido para fins acadêmicos no **Centro Universitário da Grande Dourados**.