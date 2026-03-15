# Proposal: Structured Instructions UI for Assistant Edit

## Problem
The current `instructions` field is a single large textarea (~20 rows). Users must write everything in one block, which is:
- Overwhelming and confusing
- Hard to maintain
- Difficult to auto-fill from onboarding data
- Error-prone (easy to break structure/format)

## Analysis of Instruction_Example.txt

The instruction document follows a clear structure that maps to logical sections:

| Section | Content | Maps to Function | Onboarding Source |
|---------|---------|------------------|-------------------|
| **Context/First Contact** | How users arrive, first vs returning, reagendamento rules | — | flow_template, greeting |
| **Objetivo** | Business goal, what agent should achieve | — | objective, company_info |
| **Público-alvo** | Target audience | — | company_info |
| **Estilo de Comunicação** | Tone, agent name, char limit, language | — | tone, assistant_name, assistant_characteristics |
| **Script / Flow** | Step-by-step: greeting → questions → treatments → scheduling | update_leads (status 3,4,24) | flow_template, mandatory_info, opening_questions |
| **Exemplos** | Example user/agent dialogues | — | — |
| **Apresentação Empresa** | Company story | — | company_info |
| **Restrições** | What NOT to do, escalation | — | escalation_triggers |
| **Tratamentos/Serviços** | Service list, pricing table | — | services_list |
| **FAQs** | Q&A pairs | — | faq |
| **Endereço** | Address, hours | — | address, business_hours, phone |
| **Fluxo Agendamento** | Booking flow, function calls | get_horario_agenda, set_horario_agenda, set_reagendamento, verificar_agendamento_lead | — |
| **Misc** | Extra rules, links, group VIP | — | social_media |

---

## Proposed UI: Collapsible Section Blocks

Replace the single textarea with **collapsible sections**. Each section has:
- A clear label and short description
- One or more focused inputs (textarea, text, chips, etc.)
- Optional "preview" of how it will appear in the final instruction
- Badge showing "Auto-filled from onboarding" when populated from onboarding

### Section Structure (11 blocks)

| # | Section | Input Type | Onboarding Map | Required |
|---|---------|------------|----------------|----------|
| 1 | **Business & Objective** | Textarea | company_info, objective | ✓ |
| 2 | **Target Audience** | Textarea | — | — |
| 3 | **Agent Identity & Style** | Name, Tone, Char limit, Language | assistant_name, tone, assistant_characteristics | ✓ |
| 4 | **Greeting & First Contact** | Textarea + rules (checkboxes) | greeting | ✓ |
| 5 | **Conversation Script** | Steps / Flow builder or textarea | flow_template, opening_questions, mandatory_info | ✓ |
| 6 | **Services / Treatments** | List builder or textarea | services_list | — |
| 7 | **Pricing (Agent Reference)** | Table or textarea | — | — |
| 8 | **FAQs** | Q&A list or textarea | faq | — |
| 9 | **Restrictions & Escalation** | Textarea + chips | escalation_triggers | — |
| 10 | **Location & Contact** | Address, phone, hours, links | address, phone, business_hours, website, social_media | — |
| 11 | **Scheduling Flow** | Textarea (only if get_horario_agenda enabled) | — | — |
| 12 | **Extra Instructions** | Textarea | Catch-all | — |

---

## Two Paths to Choose From

### Path A: Tabbed / Accordion UI (Simpler)
- All sections in one page as collapsible accordions
- Each section = one or more inputs
- "Advanced: View/Edit Raw Instructions" toggle to see/edit the concatenated result
- On save: JS concatenates all fields in order → submits as single `instructions`
- **Pros**: Faster to implement, familiar UX
- **Cons**: Still some manual mapping

### Path B: Wizard-Style Edit (Similar to Onboarding)
- Step-by-step wizard for editing instructions (like onboarding wizard)
- Each step = one section
- Progress bar, Next/Back, Save per step or at end
- **Pros**: Very guided, less overwhelming
- **Cons**: More clicks, may feel slow for power users

### Path C: Hybrid (Recommended)
- **Default**: Accordion UI with all sections on one page
- **"Quick Setup from Onboarding"** button: one-click auto-fill from latest onboarding submission
- **"Advanced"** tab/link: raw textarea for power users who want full control
- Save always concatenates sections in the same order for OpenAI

---

## Recommended: Path C (Hybrid)

### Implementation Steps

1. **Database**
   - Option A: Keep single `instructions` column. On load: try to parse/split existing content into sections (heuristic or markers). On save: concatenate.
   - Option B: Add `instructions_structured` JSON column. Store `{ "section_1": "...", "section_2": "..." }`. On API/OpenAI: concatenate when building the prompt.
   - **Recommendation**: Option A initially (no DB migration). Use hidden `instructions` and build from visible section fields. Parsing existing content can be best-effort (regex/sentinels) or "start fresh" for new assistants.

2. **Section Fields (HTML)**
   - Add 12 form groups (or dynamic blocks) before the current instructions textarea
   - Each with `name="instructions_section_X"` or similar
   - Hide the big textarea by default; show only in "Advanced" mode

3. **Concatenation Logic (JS)**
   - Order: 1 → 2 → 3 → … → 12
   - Template per section, e.g.:
     ```
     Objetivo:
     {section_1}

     Público-alvo:
     {section_2}
     ...
     ```
   - Before submit: build full text, put into `instructions`, submit

4. **Onboarding → Instructions Mapping**
   - When "Fill from Onboarding" is clicked:
     - `company_info` → section 1 (Business & Objective)
     - `objective` → section 1
     - `assistant_name` → section 3
     - `tone` → section 3
     - `greeting` → section 4
     - `faq` → section 8
     - `services_list` → section 6
     - `address`, `phone`, `business_hours` → section 10
     - etc.
   - Apply templates to format nicely (e.g. "Objetivo: ...", "Público-alvo: ...")

5. **Backward Compatibility**
   - If `instructions` has content but no structured data: show "Advanced" view with raw text; option to "Split into sections" (best-effort) or keep as-is
   - New assistants: start with empty sections, optionally prefill from onboarding

---

## Section Templates (for concatenation)

When building the final `instructions` string, use consistent headers so the AI understands structure:

```
Objetivo:
{section_1}

Público-alvo:
{section_2}

Estilo de Comunicação:
{section_3}

Saudação e Primeiro Contato:
{section_4}

Roteiro da Conversa:
{section_5}

Tratamentos e Serviços:
{section_6}

Tabela de Valores (apenas consulta):
{section_7}

Perguntas Frequentes:
{section_8}

Restrições e Escalação:
{section_9}

Localização e Contato:
{section_10}

Fluxo de Agendamento:
{section_11}

Instruções Adicionais:
{section_12}
```

---

## Next Steps

1. **Choose path**: A, B, or C (recommend C)
2. **Decide storage**: Single `instructions` with runtime concatenation vs new `instructions_structured` column
3. **Implement section UI** in `assistant_edit.php`
4. **Add "Fill from Onboarding"** button and mapping logic
5. **Implement concatenation** on form submit
6. **Add parse logic** for existing instructions (optional, for migration)

---

## Summary

- Break the big block into ~12 logical sections
- Use accordion/tabbed UI for easier filling
- Concatenate on save in a fixed order
- Map onboarding data to sections for future automation
- Keep "Advanced" raw edit for power users
