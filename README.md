# 📍 Sistema de Consulta de CEP — ViaCEP

Aplicação web desenvolvida em **PHP** que realiza requisições HTTP para uma API pública, recebe dados em **JSON**, processa-os e apresenta de forma dinâmica ao usuário.

---

## 📖 Sobre o Projeto

O sistema permite que o usuário informe um **CEP** e, ao clicar em **CONSULTAR**, o **PHP** executa uma requisição **GET** para a API pública **ViaCEP**, decodifica a resposta JSON e exibe os dados do endereço formatados na tela.

### Fluxo de funcionamento

```
Usuário digita CEP: 01001000  →  [ CONSULTAR ]
        │
        ▼
PHP realiza:  GET → https://viacep.com.br/ws/01001000/json/
        │
        ▼
API retorna JSON:
{
  "cep": "01001-000",
  "logradouro": "Praça da Sé",
  "bairro": "Sé",
  "localidade": "São Paulo",
  "uf": "SP",
  ...
}
        │
        ▼
PHP interpreta o JSON e apresenta:
     CEP:         01001-000
     Logradouro:  Praça da Sé
     Bairro:      Sé
     Cidade:      São Paulo
     Estado:      SP
```

---

## 🛠️ Tecnologias Utilizadas

| Tecnologia | Função |
|------------|--------|
| **PHP** | Backend — faz a requisição HTTP e interpreta o JSON |
| **HTML5** | Estrutura da página e formulário |
| **CSS3** | Estilização e layout responsivo |
| **cURL / file_get_contents** | Execução da requisição GET à API |
| **API ViaCEP** | Fonte pública dos dados de endereço |

---

## 📁 Estrutura do Projeto

```
├── index.php    # Página principal: formulário, lógica PHP e resultados
├── style.css    # Folha de estilo (layout e design)
└── README.md    # Documentação do projeto
```

---

## ▶️ Como Executar

### Requisitos
- PHP 7.4 ou superior
- Extensão `curl` habilitada (recomendada)

### Passo a passo

1. **Clone o repositório:**

   ```bash
   git clone https://github.com/SEU-USUARIO/seu-projeto.git
   cd seu-projeto
   ```

2. **Inicie o servidor embutido do PHP:**

   ```bash
   php -S localhost:8000
   ```

3. **Abra no navegador:**

   ```
   http://localhost:8000
   ```

4. **Informe um CEP** (ex.: `01001000`) e clique em **CONSULTAR**.

---

## ✅ Funcionalidades

- [x] Página inicial com **título do sistema**
- [x] **Formulário** com campo de pesquisa e botão de consulta
- [x] **Validação** do CEP (deve conter 8 dígitos)
- [x] **Requisição GET** para a API ViaCEP via PHP (cURL)
- [x] **Interpretação do JSON** com `json_decode`
- [x] **Área de resultados** com dados formatados
- [x] Tratamento de **erros** (CEP inválido, não encontrado, falha de conexão)
- [x] Layout **responsivo** (desktop e mobile)

---

## 📚 APIs de Referência

O projeto utiliza a **API ViaCEP**:

- **Endpoint:** `https://viacep.com.br/ws/{cep}/json/`
- **Método:** `GET`
- **Formato de resposta:** `JSON`
- **Necessita chave de API:** Não

---

## 👨‍🎓 Autor - Renato Aparecido da Silva - 2° MTec DS/AMS

Desenvolvido como atividade acadêmica de **Desenvolvimento Web**.
