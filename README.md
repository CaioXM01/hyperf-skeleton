<p align="center">
  <img alt="Hyperf Logo" src="https://hyperf.wiki/3.1/logo.png" width="200px" />
</p>

# Projeto Hyperf Hexagonal Architecture


Este projeto faz parte de um desafio PHP construído com a arquitetura hexagonal e utilizando o framework Hyperf.

## 🚀 Regra de Negócio

A aplicação trata de um serviço de transações financeiras entre dois tipos de usuários: comuns e lojistas. Ambos possuem uma carteira com dinheiro e podem realizar transferências entre si. O foco será no fluxo de transferência entre dois usuários.

### Requisitos:

-   Para ambos tipos de usuário, precisamos do Nome Completo, CPF, e-mail e Senha. CPF/CNPJ e e-mails devem ser únicos no sistema. Sendo assim, seu sistema deve permitir apenas um cadastro com o mesmo CPF ou endereço de e-mail.
-   Usuários podem enviar dinheiro (efetuar transferência) para lojistas e entre usuários.
-   Lojistas **só recebem** transferências, não enviam dinheiro para ninguém.
-   Validar se o usuário tem saldo antes da transferência.
-   Antes de finalizar a transferência, deve-se consultar um serviço autorizador externo, use este mock para simular (https://run.mocky.io/v3/5794d450-d2e2-4412-8131-73d0293ac1cc).
-   A operação de transferência deve ser uma transação (ou seja, revertida em qualquer caso de inconsistência) e o dinheiro deve voltar para a carteira do usuário que envia.
-   No recebimento de pagamento, o usuário ou lojista precisa receber notificação (envio de email, sms) enviada por um serviço de terceiro e eventualmente este serviço pode estar indisponível/instável. Use este mock para simular o envio (https://run.mocky.io/v3/54dc2cf1-3add-45b5-b5a9-6bf7e7f1f4a6).
-   Este serviço deve ser RESTFul.

## 🛠️ Por que escolhi o Hyperf?

O Hyperf é um framework PHP baseado em Swoole e projetado para desenvolvimento de aplicativos corporativos de alto desempenho. Ele oferece suporte para programação reativa, facilitando a construção de aplicativos escaláveis e de baixa latência. Além disso, possui uma arquitetura modular e flexível que se alinha bem com os princípios da arquitetura hexagonal.

## 🎯 Arquitetura Hexagonal e Organização dos Arquivos

A arquitetura hexagonal, também conhecida como Ports and Adapters, é uma abordagem que visa separar as preocupações da aplicação em camadas distintas: domínio, aplicação e infraestrutura. A estrutura de arquivos do projeto é organizada de acordo com essa arquitetura:

```php
|- app/ /* Estrutura principal do projeto */
    |- Application/ /* Responsável por adaptar as requisições para a camada de domínio */
        |- Controllers/ /* Controladores que lidam com as requisições HTTP */
        |- Middleware/ /* Middlewares para manipulação de dados nas requisições */
    |- Domain/ /* Contém as regras de negócio do projeto */
        |- HttpClients/ /* Interfaces para clientes HTTP, fornecendo dependências para o domínio */
        |- Entities/ /* Entidades que representam informações de valor para o domínio */
        |- Repository/ /* Interfaces dos repositórios de dados, fornecendo dependências para o domínio */
        |- Services/ /* Lógicas e regras de negócio com valor para o domínio */
    |- Infrastructure/ /* Comunicação com dependências externas */
        |- HttpClients/ /* Implementações concretas de clientes HTTP */
        |- Database/ /* Comunicação com o banco de dados */
            |- Model/ /* Modelos de dados */
            |- Repository/ /* Implementações concretas dos repositórios de dados */
|- config/ /* Arquivos de configuração (rotas, injeção de dependências, database, etc.) */
```

## 🏃 Como Executar

### Pré-requisitos

1. Certifique-se de ter o Docker instalado em sua máquina.
2. Clone este projeto: `git clone https://github.com/CaioXM01/hyperf-skeleton.git`
3. Navegue até a pasta do projeto: `cd hyperf-skeleton`

### Execução da Aplicação

Uma vez instalado, você pode executar o servidor imediatamente usando o comando abaixo.

```bash
docker-compose up -d
```

Isso iniciará o servidor na porta `9501` e o vinculará a todas as interfaces de rede. Você pode então acessar a API em `http://localhost:9501/`.

#### Migration
Pode ser necessario fazer um migration

```bash
docker container exec -it hyperf-skeleton php bin/hyperf.php migrate
```

Caso queira encerrar o servidor, você pode executar o comando abaixo.

```bash
docker-compose down
```

## 🛣️ Rotas

A aplicação possui as seguintes rotas:

- `GET /users`: Obtém todos os usuários.
- `GET /users/{id}`: Obtém um usuário pelo ID.
- `POST /users`: Registra um novo usuário.
    
    Payload:
    ```php
        {
            "name": "Teste lojista",
            "email": "testelojista@teste.com",
            "document": "22222222222222222",
            "password": "123456",
            "balance": 2000,
            "type": "shopkeeper" //common || shopkeeper
        }
    ```
- `POST /transaction`: Realiza uma transação financeira.

    Antes de realizar uma transferencia você deve criar pelo menos 2 usuários no banco de dados.
    
    Payload:
    ```php
        {
            "value": 100.0,
            "payer": 1,
            "payee": 2
        }
    ```
- `POST /transaction/chargeback/{id}`: Realiza o estorno da transação.
    
    Payload:
    ```php
        {
            "chargeback_reason": "teste"
        }
    ```
- `GET /transaction`: Obtem todas as transações.


#### User
    
    int $id;
    string $name;
    string $email;
    string $document;
    string $password;
    float $balance;
    string $type;
    date $created_at;
    date $updated_at;

#### Transaction
    
    string $id;
    float $value;
    int $payer_id;
    int $payee_id;
    string $chargeback_reason;
    date $notified_at;
    date $transferred_at;
    date $chargeback_at;
    date $created_at;
    date $updated_at;

## 📝 Como Testar

Você pode testar a aplicação usando o PHPUnit. Certifique-se de que todas as dependências estão instaladas e o container docker rodando.

```bash
docker container exec -it hyperf-skeleton composer test
```

### Avaliação objetiva

Script docker adaptado para a avaliação objetiva do projeto 

```
docker run -it --rm -v ./:/app -w /app jakzal/phpqa phpmd app text cleancode,codesize,controversial,design,naming,unusedcode
```



# Possíveis Melhorias Futuras

- **Implementação de Tokens JWT**: Adicionar autenticação baseada em tokens JWT para melhorar a segurança da API e permitir que os usuários façam login e protejam as rotas sensíveis.

- **Melhorias na Validação de Dados**: Reforçar a validação de dados, incluindo a validação de formatos de CPF/CNPJ e e-mails, utilizando bibliotecas especializadas ou criando validadores personalizados.

- **Implementação de Webhooks**: Adicionar suporte para webhooks, permitindo que serviços externos recebam notificações instantâneas sobre eventos importantes, como transações concluídas ou atualizações de saldo.

- **Melhorias na Experiência do Usuário**: Fornecer respostas mais descritivas e amigáveis para o usuário, além de documentação clara sobre como usar os endpoints.

- **Otimização de Desempenho**: Realizar ajustes de desempenho na API, incluindo otimizações de consulta ao banco de dados, cache de dados frequentemente acessados e implementação de paginadores para resultados de consulta volumosos.

- **Implementação de Testes de Integração**: Desenvolver testes de integração automatizados para garantir que todas as funcionalidades da API estejam funcionando conforme o esperado, incluindo casos de uso envolvendo transferências de dinheiro e notificações de pagamento.

- **Adicionar mais Testes Unitários**: Aumentar a cobertura dos testes unitários (atualmente só é testado a função principal de transação), garantindo a integridade e o comportamento esperado de cada componente da API.

- **Monitoramento e Logging Avançados**: Implementar ferramentas de monitoramento e logging avançados para rastrear o desempenho da API, identificar problemas de forma proativa e melhorar a resolução de problemas.

Essas melhorias ajudarão a tornar a API mais robusta, segura e escalável, proporcionando uma melhor experiência para os usuários e facilitando o gerenciamento do sistema como um todo.
