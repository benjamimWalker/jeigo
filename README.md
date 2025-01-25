# 🌸 Jeigo: A API de Dicionário Profícua 🌸

Bem-vindo ao **Jeigo**, um projeto desenvolvido para resolver o desafio proposto e demonstrar habilidades como Back-end Developer. Este é um aplicativo que interage com a **Free Dictionary API**, gerenciando palavras e possibilitando funcionalidades como histórico, favoritos e listagem de termos em inglês.
>  This is a challenge by [Coodesh](https://coodesh.com/)
---

> Link para minha apresentação em vídeo no YouTube: [https://youtu.be/qxUf2KvJOQ4](https://youtu.be/qxUf2KvJOQ4)

## ✨ Tecnologias Utilizadas

O projeto foi desenvolvido utilizando as seguintes tecnologias:

- **PHP**
- **Laravel**
- **Docker**
- **MySQL**
- **Redis**
- **Nginx**

---

## 🚀 Como Instalar e Usar o Projeto

1. **Clone o repositório:**
   ```bash
   git clone git@github.com:benjamimWalker/jeigo.git
   ```

2. **Configure as variáveis de ambiente:**
   ```bash
   cp .env.example .env
   ```

3. **Suba os containers Docker:**
   ```bash
   docker compose up -d
   ```

4. **Instale as dependências do Composer:**
   ```bash
   docker compose exec app composer install
   ```

5. **Rode as migrações do banco de dados:**
   ```bash
   docker compose exec app php artisan migrate
   ```

6. **Importe palavras para o banco de dados:**
   ```bash
   docker compose exec app php artisan app:import-words
   ```

7. **Acesse o projeto:**
    - Base URL: [http://localhost](http://localhost)
    - Documentação da API: [http://localhost/api/documentation#/](http://localhost/api/documentation#/)

8. **Execute os testes:**
   ```bash
   docker compose exec app php artisan test
   ```

---

## 📝 Meu Processo de Desenvolvimento

### 1️⃣ **Início do Projeto**

- Criei o projeto Laravel e configurei o Docker com MySQL, Redis e Nginx para gerenciar as dependências do ambiente.

---

### 2️⃣ **Análise da API e Modelagem**

- Estudei a estrutura da Free Dictionary API e os requisitos do desafio.
- Modelei o banco de dados com as seguintes tabelas principais:
    - **words** (para armazenar palavras).
    - **user_word_favorite** (pivot para palavras favoritas por usuário).
    - **user_word_history** (pivot para histórico de palavras visualizadas por usuário).

---

### 3️⃣ **Criação do Script de Importação de Palavras**

- Desenvolvi rapidamente um script para importar palavras da Free Dictionary API para o banco, já que ela era mais independente das demais features

---

### 4️⃣ **Autenticação de Usuário**

- Implementei a autenticação utilizando UUIDs para os IDs de usuários, conforme os exemplos fornecidos.
- Encontrei problemas ao tentar usar SQLite para testes, então optei por usar o banco principal também para os testes.

---

### 5️⃣ **Listagem de Palavras**

- Criei uma listagem simples e, em seguida, adicionei paginação com **Cursor Pagination** do Eloquent.
- Configurei o cache utilizando Redis. Estratégia:
    - Salvar resultados baseados no termo pesquisado, quantidade de itens e cursor.
    - Validade de cache: 30 dias.
    - Utilizei eventos de model para invalidar o cache automaticamente ao alterar, criar ou deletar dados no banco.
- Para isso, utilizei `flush()`. Se outra feature usasse cache, teria adotado **tagging de cache** (mais tarde precisei usar, mas com Redis há ressalvas, o ideal para isso seria Memcached).
- Testei e documentei a funcionalidade.

---

### 6️⃣ **Listagem de Favoritos**

- A listagem de palavras favoritas seguiu a mesma estratégia inicial da listagem de palavras.
- Identifiquei que **Cursor Pagination** não funcionou neste endpoint inicialmente, então usei paginação normal durante um tempo.
- Para invalidação de cache, utilizei o pacote: [Laravel Relationship Events](https://github.com/chelout/laravel-relationship-events).
- Considerei usar **Memcached**, mas mantive Redis devido ao tempo limitado do desafio, já que só tenho experiência com Redis.
- Fiz a documentação e testes.

---

### 7️⃣ **Favoritar Palavras**

- Desenvolvi o endpoint para salvar palavras como favoritas.
- Usei `syncWithoutDetaching` para evitar duplicações ao favoritar a mesma palavra múltiplas vezes.
- Substituí eventos `belongsToManyAttached` por `belongsToManySynced` devido à mudança no método usado.

---

### 8️⃣ **Histórico de Palavras Visualizadas**

- Implementei a funcionalidade para listar o histórico com paginação.
- Encontrei comportamentos estranhos ao usar **Cursor Pagination** tanto em favoritos quanto em histórico. Criei um método genérico para evitar duplicação de código.
- Documentei e testei a feature.

---

### 9️⃣ **Detalhes de Palavras**

- Desenvolvi o endpoint principal para retornar o significado de uma palavra utilizando o endpoint `[GET] /entries/en/:word` da Free Dictionary API.
- Criei um **Service** que atua como um wrapper da API externa, integrando-a ao meu projeto.

---

### 🔧 **Ajustes Finais**

- Finalizei com ajustes menores, testes e validações.

---

## 🌟 Features

| Feature                            | Status   |
|------------------------------------|----------|
| Login de usuário                   | ✅        |
| Signup de usuário                  | ✅        |
| Listagem de palavras               | ✅        |
| Detalhes de palavras               | ✅        |
| Listagem de favoritos              | ✅        |
| Perfil do usuário                  | ✅        |
| Histórico de palavras visualizadas | ✅        |
| Favoritar palavras                 | ✅        |
| Apagar favoritos                   | ✅        |
| Documentação da API                | ✅        |
| Testes automatizados               | ✅        |

---

## 📚 Documentação da API

A documentação completa está disponível em: [http://localhost/api/documentation#/](http://localhost/api/documentation#/)

---

## 🧪 Testes

Para rodar os testes automatizados, execute:
```bash
   docker compose exec app php artisan test
```

---

## 💡 Considerações Finais

- Escolhi o Redis como sistema de cache, mas considerei que o **Memcached** poderia ser mais adequado para cache com tagging.
- Encontrei limitações com **Cursor Pagination**, mas busquei soluções viáveis considerando o tempo do desafio. E no final misteriosamente ele voltou a funcionar
- Observei vários acontencimentos estranhos que me atrasaram um pouco, não foi surpresa, acontece em todo projeto.

---
