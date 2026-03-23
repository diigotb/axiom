# CLAUDE.md — Projeto AXIOM
Fonte central de verdade do projeto. Consulte este arquivo antes de qualquer alteração no código.
**Última atualização: 2026-03-23**

---

## Dono do Projeto
**Rodrigo Toledo (RT)** — RT Marketing Estratégico

---

## O que é o AXIOM
Plataforma SaaS omnichannel construída como módulo do Perfex CRM. Centraliza comunicação (WhatsApp, Instagram, Facebook, futuramente e-mail), atende leads automaticamente com IA, gerencia pipeline de vendas e converte mais.

---

## Filosofia de Trabalho
- Antes de qualquer código, definir e alinhar. Só executar depois de aprovado.
- Somos uma equipe — mesma língua, sem surpresas.
- Sempre entender o porquê de cada decisão.
- Nunca copiar sistemas de terceiros (Inexxus, OmniU etc.) — tudo com identidade própria.
- Economizar uso: Claude.ai para planejar/alinhar, Devstral (Ollama local) para tarefas rotineiras, Claude Code só para tarefas complexas e críticas.

---

## Stack

| Componente | Tecnologia |
|---|---|
| Base | Perfex CRM + CodeIgniter 3 (PHP 8.1) |
| Banco principal | MySQL 8.0.30 (db_axiom) |
| WhatsApp | Evolution API (Node.js) + PostgreSQL |
| IA | Google Gemini 2.5 Flash |
| Ambiente local | Laragon 6.0 (Windows) |
| Repositório | https://github.com/diigotb/axiom (branch master) |
| Servidor produção | Oracle Cloud (146.235.44.131) — 1GB RAM |
| IDE | VS Code + Continue + Devstral (Ollama) |

---

## Estrutura de Arquivos

```
C:\laragon\www\axiom\                          ← Raiz Perfex CRM
C:\laragon\www\axiom\modules\axiomchannel\     ← Módulo AXIOM
C:\laragon\www\evolution\                      ← Evolution API porta 8080
```

### Arquivos-chave do módulo

| Arquivo | Função |
|---|---|
| `modules/axiomchannel/controllers/Axiomchannel.php` | Controller principal |
| `modules/axiomchannel/models/Axiomchannel_model.php` | Model principal |
| `modules/axiomchannel/assets/css/axiomchannel.css` | Design System CSS |
| `modules/axiomchannel/assets/css/axiom_admin.css` | CSS do layout admin dark |
| `modules/axiomchannel/assets/js/axiom_admin.js` | JS do layout admin |
| `modules/axiomchannel/config/routes.php` | Rotas do módulo |
| `modules/axiomchannel/axiomchannel.php` | Menu, hooks, ativação |
| `modules/axiomchannel/libraries/AxiomChannel_Gemini.php` | Integração Gemini |
| `modules/axiomchannel/libraries/AxiomChannel_Evolution.php` | Integração Evolution API |
| `modules/axiomchannel/views/spa/index.php` | SPA principal |
| `application/controllers/admin/Axiom_dashboard.php` | Dashboard Geral /admin |
| `CLAUDE.md` | Este arquivo |

---

## Convenções do Módulo AxiomChannel
- Prefixo de tabelas: `tblaxch_` (usar `db_prefix() . 'axch_...'` no código)
- Prefixo CSS: `ax-` em todas as classes do módulo
- Assets CSS: `module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css')`
- Toda view começa com `init_head()` e termina com `init_tail()`
- AJAX: sempre verificar `$this->input->is_ajax_request()` antes de responder JSON
- CSRF: incluir `[CSRF_NAME]: CSRF_TOKEN` em todos os fetch POST
- `ADD COLUMN IF NOT EXISTS` NÃO suportado nesta versão do MySQL — verificar colunas antes de adicionar

---

## Credenciais (armazenadas em tbloptions)

| Chave | Onde encontrar |
|---|---|
| `axch_gemini_key` | tbloptions no banco db_axiom |
| `axch_google_client_id` | tbloptions no banco db_axiom |
| `axch_google_client_secret` | tbloptions no banco db_axiom |
| `axch_meta_app_id` | tbloptions no banco db_axiom |
| `axch_meta_app_secret` | tbloptions no banco db_axiom |
| `axch_meta_verify_token` | axiom_meta_webhook |
| `axch_evolution_key` | tbloptions no banco db_axiom |
| `axch_evolution_url` | http://localhost:8080 (local) / configurar em produção |

> ⚠️ Credenciais reais armazenadas apenas no banco (tbloptions). Nunca commitar valores reais aqui.

---

## Identidade Visual

| Elemento | Cor | Uso |
|---|---|---|
| Azul marinho | `#1B3A4B` | Sidebar, headers |
| Verde petróleo | `#2D7A6B` | Botões, accent principal |
| Dourado | `#F5A623` | Logo (triângulo) |
| Azul | `#4A90D9` | Logo (triângulo) |

**Logo AXIOM** = triângulo composto por 3 triângulos menores nas cores acima.
**Cores proibidas:** verde neon, roxo ou qualquer cor que lembre Inexxus/OmniU.

### Paleta de cores dos estágios
`['#E53E3E','#DD6B20','#D69E2E','#38A169','#2D7A6B','#3182CE','#5A67D8','#805AD5','#D53F8C','#1B3A4B','#4A5568','#718096','#F6AD55','#68D391','#63B3ED','#B794F4']`

---

## Banco de Dados — Tabelas do Módulo (db_axiom)

- `tblaxch_devices` — dispositivos WhatsApp (ai_enabled adicionado)
- `tblaxch_contacts` — contatos (channel, external_id, channel_data, ast_stage_id)
- `tblaxch_messages` — mensagens (channel, external_message_id, sent_by_ai)
- `tblaxch_transfers` — transferências de atendimento
- `tblaxch_queue` — fila de atendimento
- `tblaxch_pipelines` — pipelines do CRM
- `tblaxch_pipeline_stages` — estágios do pipeline
- `tblaxch_crm_leads` / `tblaxch_pipeline_history` — leads e histórico
- `tblaxch_assistants` — config IA (assistant_types, appointment_*, default_contract_template_id, emoji_enabled)
- `tblaxch_knowledge_base` — base de conhecimento por categorias
- `tblaxch_assistant_stages` — etapas do fluxo (media_id, media_send_position: before/with/after_message)
- `tblaxch_knowledge_media` — biblioteca de mídia (id, assistant_id, file_type, original_name, file_path, file_size, mime_type, media_label, description)
- `tblaxch_appointments` — agendamentos (contact_id, device_id, start/end_datetime, status, google_event_id)
- `tblaxch_google_calendar` — OAuth Google (device_id, google_account, calendar_id, access/refresh_token)
- `tblaxch_contracts` — contratos com assinatura digital (sign_token, signer_name/cpf/ip, document_hash)
- `tblaxch_contract_templates` — templates (device_id, name, content, variables)
- `tblaxch_automations` — automações (type ENUM: birthday/invoice/followup/inactive/appointment/satisfaction)
- `tblaxch_automation_log` — log de disparos
- `tblaxch_staff_config` — atendentes (device_id adicionado para vincular staff ao device)
- `tblaxch_transfer_log` — log de transferências
- `tblaxch_meta_connections` — Facebook/Instagram (device_id, page_id, token, instagram_account_id, connection_type ENUM: facebook/instagram/both)
- `tblaxch_staff_preferences` — preferências (theme, sidebar_position, accent_color)
- `tblstaff_meta` — metadados de staff (staffid, meta_key, meta_value)

---

## Meta API (Facebook + Instagram)
- Webhook URL: `/admin/axiomchannel/meta_webhook`
- Verify Token: `axiom_meta_webhook`
- Versão API: `v18.0`
- App em modo teste — testador: diigotb93@gmail.com

---

## Evolution API
- Diretório: `C:\laragon\www\evolution\`
- Versão: 2.3.7
- Porta: 8080 → `http://localhost:8080`
- Manager: `http://localhost:8080/manager`
- API Key local: `429683C4C977415CAAFCCE10F7D57E11`
- Banco: PostgreSQL (`postgresql://postgres:axiom2025@localhost:5432/evolution_api`)
- **Iniciar:** `C:\laragon\www\evolution\evolution.bat`
- **Nota:** Evolution API vai para servidor separado (não vai para o servidor Oracle atual)

---

## PHP/MySQL Binários (Laragon)
- PHP: `C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe`
- MySQL: `C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root db_axiom`

---

## Hierarquia de Acesso

### Admin (Rodrigo / gestor)
- Vê todos os devices
- Dashboard consolidado da equipe
- Configurações do sistema
- Vincula staff a device

### Atendente
- Vê só o device vinculado (via `device_id` em `tblaxch_staff_config`)
- Dashboard pessoal
- Só suas conversas e leads
- Pode ativar/desativar IA do próprio device

**Implementação:** `axch_get_device_scope()` em `helpers/axiomchannel_helper.php`

---

## Dois Dashboards Separados

### Dashboard Geral (`/admin`)
Visão 360° do negócio: faturamento, clientes, projetos, tarefas, leads, contratos, dispositivos, agenda.

### Dashboard AXIOM (`/axiomchannel/spa`)
Focado em comunicação e IA: conversas por canal, performance IA, pipeline, automações, tempo de resposta.

---

## Menu Lateral AxiomChannel
1. Todas as Conversas
2. Dispositivos
3. CRM Pipeline
4. Assistente IA
5. Agendamentos
6. Contratos

---

## O que Está Implementado

### Chat e Comunicação
- Inbox de conversas com polling automático
- Chat ao vivo WhatsApp (envio/recebimento via Evolution API)
- Webhook Evolution API recebendo mensagens
- Copiloto de vendas (painel lateral Gemini)
- Barra de status com estágios clicáveis

### Pipeline e CRM
- Kanban com drag and drop + histórico
- Wizard IA (perguntas → Gemini cria estágios automaticamente)
- Paleta 16 cores para estágios
- Leads com valor, atribuição de staff, notas
- `move_lead_stage()` com histórico

### Assistente IA
- Toggle IA ativa por device
- Base de conhecimento por categorias (product, faq, objection, info, sales_tip)
- Fluxo de qualificação com mídia por etapa
- Biblioteca de mídia drag-and-drop
- `_process_incoming_message()` completo
- Saudação só na 1ª mensagem, histórico 10 msgs para contexto
- `_clean_for_whatsapp()`, `_split_message()`, `_send_stage_media()`

### Integrações
- Google Calendar OAuth completo
- Meta OAuth Facebook + Instagram
- Meta webhook (Messenger + Instagram Direct)

### Layout
- SPA funcionando em `/axiomchannel/spa`
- Dashboard dark theme com cards de métricas
- Header com logo, busca, temperatura, notificações
- Sidebar com ícones + nomes
- Painel personalização (Escuro/Claro)
- Mobile responsive com bottom nav
- Design System com classes `ax-*`

### Outros
- Agendamentos + Google Calendar
- Contratos + assinatura digital (`sign.php` público)
- Permissões por device (admin vê tudo, atendente vê só o device dela)
- `axch_get_device_scope()`

---

## Problemas Pendentes

### Prioridade 1 — Layout
1. `/admin` ainda mostra Perfex padrão cinza — precisa do novo layout dark
2. Páginas internas do AxiomChannel voltam para layout antigo
3. Saudação mostra "Olá!" sem nome e sem Bom dia/tarde/noite correto (GMT-3)
4. Temperatura imprecisa — trocar API
5. Tema Claro: textos ficam brancos no fundo branco (devem ficar pretos)
6. Botão "Aplicar tema" não funciona
7. Remover "Cor de destaque" do painel (manter só Escuro/Claro)

### Prioridade 2 — Funcionalidades
1. Chat + CRM + Copiloto na mesma tela (sem abrir nova página)
2. Kanban integrado no layout novo sem sair do sistema
3. Motor de automações (cron + 6 automações)
4. Transferência inteligente por nome/departamento
5. Mensagens do WhatsApp às vezes não aparecem no chat
6. Envio de mídia retorna `[mídia]` como texto

---

## Regras de Desenvolvimento
1. Antes de qualquer código: definir e alinhar no Claude.ai
2. Nunca copiar código da Inexxus/OmniU
3. Prefixo `ax-` em todas as classes CSS do módulo
4. Prefixo `tblaxch_` em todas as tabelas
5. Sempre `php -l` antes de commitar
6. Commits com mensagem descritiva em português
7. Mobile-first em toda tela
8. Um problema por vez — não misturar layout e funcionalidade
9. Prompts cirúrgicos para economizar consumo
10. **NÃO** redirecionar `/admin` para o SPA (quebra o sistema)
11. **NÃO** adicionar hooks de redirect global
12. **NÃO** criar interceptadores de links globais
13. A solução correta para layout é aplicar wrapper do novo layout em cada view

---

## Ferramentas de Desenvolvimento

| Ferramenta | Uso |
|---|---|
| Claude.ai | Planejar, alinhar, criar prompts |
| Devstral (Ollama local) | Executar tarefas rotineiras no projeto |
| Claude Code | Só tarefas complexas e críticas |
| VS Code + Continue | IDE principal com Devstral integrado |

**Devstral:** `ollama pull devstral` / `ollama run devstral`

---

## Sincronização PC ↔ Notebook ↔ Servidor
- **PC:** `C:\laragon\www\axiom`
- **Notebook:** `C:\laragon\www\axiom` (clonado do GitHub)
- **Servidor:** `/var/www/axiom` (Oracle Cloud — 146.235.44.131)
- **Fluxo:** trabalha → `git add . && git commit -m "..." && git push`
- **No outro:** `git pull`
- Banco (`db_axiom`) e Evolution API ficam separados em cada máquina
- Em produção tudo será centralizado (Hetzner — pendente)

---

## Módulos Instalados no Perfex
`axiomchannel, backup, contactcenter, exports, goals, menu_setup, surveys, theme_style, ultimate_purple_theme, webhooks, custom_javascript, si_lead_filters, si_sms, si_todo, reminder, addon_statuses, turncrm`

---

## Mobile / App Futuro
- Cada tela construída mobile-first
- Breakpoint: `@media (max-width: 768px)`
- Sidebar vira bottom navigation no mobile
- Base do futuro app React Native ou Flutter

---

## Roadmap

### FASE 1 — Layout (ATUAL)
1. Corrigir saudação Bom dia/tarde/noite GMT-3
2. Corrigir temperatura (trocar API)
3. Tema Claro funcionando (textos pretos)
4. Remover cor de destaque, manter só Escuro/Claro
5. Botão Aplicar tema funcionando
6. Dashboard `/admin` com novo layout dark
7. Todas as páginas AxiomChannel com novo layout

### FASE 2 — AxiomChannel Completo
1. Chat + CRM + Copiloto numa tela só
2. Pipeline kanban integrado no layout novo
3. Automações com templates follow-up por status
4. Motor de automações (cron)
5. Transferência inteligente por nome/departamento
6. Quick Replies (botão ✨ sugere 3 respostas via IA)
7. Deal Pulse (score 0-100 do lead)
8. Client DNA (perfil e tags automáticas)
9. Templates de mensagem (digita `/` para listar)
10. Biblioteca de mídia no chat
11. Tempo de resposta no dashboard
12. Instagram DM + Facebook no inbox unificado
13. Google Meu Negócio — Missed Call Text Back
14. Google Reviews resposta automática

### FASE 3 — Crescimento
1. Axiom Ads — dashboard por anúncio (origem, dispositivo, etapa, nomes, conversão)
2. Mapeamento do Sucesso — metas, cálculos, IA Estrategista
3. Relatório do Gestor (BM + Google + Meta + Axiom Insight + plano de ação)
4. Ajuda Online — chat IA treinada no sistema AXIOM
5. Meta Conversions API — 5 eventos (Lead, InitiateCheckout, Schedule, Purchase, Exclusão)
6. Google Ads Conversions API
7. Disparo em massa inteligente
8. Auto Follow-up por estágio
9. Funil completo — do ad até a receita
10. Relatório PDF automático mensal
11. Invoice Follow-up (cobrança automática WhatsApp)
12. Visual Builder (chatbot drag-and-drop)
13. Integração Asaas + Mercado Pago

### FASE 4 — Ecossistema
1. Chrome Extension (CRM overlay no WhatsApp Web)
2. Módulo de Páginas (WordPress + Elementor)
3. Painel do Revendedor
4. Email no Unified Inbox
5. App mobile (React Native ou Flutter)

### FASE 5 — Expansão
1. Shop/WooCommerce integrado
2. Stripe para mercado americano
3. Local Service Ads (EUA)

---

## Funcionalidade Futura — Notificação Proativa para Colaborador
Quando lead muda para estágio específico (ex: "Atendimento Humano"), dispara WhatsApp para o colaborador responsável avisando que o lead precisa de atenção.

**Fluxo:** lead muda estágio → automação detecta → busca colaborador vinculado ao device → envia mensagem personalizada no WhatsApp dele.

**Evolução:** push notification nativa no app mobile.

**Onde encaixa:** `tblaxch_staff_config` (adicionar `whatsapp` do colaborador) + lógica em `move_lead_stage()`.

---

## Changelog

| Data | O que mudou |
|---|---|
| 2026-03-23 | Criação do CLAUDE.md unificado com contexto completo do projeto |
| 2026-03-23 | Servidor Oracle Cloud configurado (146.235.44.131) |
| 2026-03-23 | Builds do Perfex gerados via Grunt no servidor |
