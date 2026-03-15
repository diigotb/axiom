# Visual Builder Template - iNexxus Assistant

## 📋 Overview

This template demonstrates how to build complex assistant instructions using the visual builder nodes. It's based on the complete instructions from `INSTRUCOES_ASSISTENTE_INEXXUS_ATUALIZADO.md`.

## 🎯 How to Use This Template

1. **Open Visual Builder**: Navigate to an assistant and click "Visual Builder"
2. **Load Template**: Click the "Save" dropdown → "Load Template"
3. **Review Structure**: The template will load with all nodes connected showing the flow
4. **Customize**: Edit each instruction node to match your needs
5. **Save**: Click "Save" to store your visual configuration

## 🧩 Node Structure Explained

### Start Node
- **Purpose**: Entry point of the assistant flow
- **Connections**: Connects to first function or instruction node

### Function Nodes
These represent assistant functions that should be called:

- **get_lead_info**: Retrieves all lead information
- **get_lead_context**: Gets conversation history (last 50 messages by default)
- **update_leads**: Saves lead responses to database fields
- **get_horario_agenda**: Checks available scheduling times
- **create_group_chat**: Creates WhatsApp group
- **send_media**: Sends media files
- **manage_conversation**: Controls AI, transfers, notifications

### Instruction Nodes
These contain the actual instructions text. Break down complex instructions into multiple nodes:

1. **CRITICAL RULE #1 - Save Answers**: Rules about saving lead responses
2. **Objective and Application**: Business context and target audience
3. **Communication Style**: Tone, language, and communication guidelines
4. **Response Mapping Rules**: How to map questions to database fields
5. **Pitch Flow - Steps 1-10**: Complete sales pitch flow

### Media Nodes
- Represents media files to be sent
- Select from assistant-specific or library media
- Configure which media to send at which point

## 📊 Template Flow Structure

```
Start
  ↓
Get Lead Info (Function)
  ↓
Get Lead Context (Function)
  ↓
CRITICAL RULE #1 (Instruction)
  ↓
Objective and Application (Instruction)
  ↓
Communication Style (Instruction)
  ↓
Response Mapping Rules (Instruction)
  ↓
Update Leads (Function)
  ↓
Pitch Flow Steps 1-10 (Instruction)
```

## 💡 Best Practices

1. **Break Down Instructions**: Don't put everything in one instruction node. Split by topic:
   - Critical rules
   - Business context
   - Communication style
   - Flow steps
   - Examples

2. **Use Function Nodes Explicitly**: Show which functions the assistant should use and when

3. **Connect Nodes Logically**: The connections show the flow and dependencies

4. **Use Descriptive Titles**: Make instruction node titles clear and descriptive

5. **Group Related Content**: Keep related instructions together

6. **Place Critical Rules First**: Important rules should be at the beginning of the flow

7. **Use Media Nodes Strategically**: Show when media should be sent in the flow

## 🔄 Converting Visual to Form Format

When you save the visual builder, it automatically converts to the traditional form format:
- All instruction node contents are combined into one `instructions` field
- Function nodes are converted to the `functions` array
- Media nodes are preserved for reference

## 📝 Example: Building the iNexxus Instructions

Based on the markdown file, here's how the nodes are structured:

### Node 1: Critical Rule
**Title**: "CRITICAL RULE #1 - Save Answers"
**Content**: The complete critical rule about saving answers before responding

### Node 2: Context Functions
**Functions**: 
- get_lead_info
- get_lead_context

### Node 3: Response Mapping
**Title**: "Response Mapping Rules"
**Content**: Complete mapping table showing which questions map to which database fields

### Node 4: Update Function
**Function**: update_leads
**Purpose**: Shows that update_leads should be called after identifying responses

### Node 5: Pitch Flow
**Title**: "Pitch Flow - Steps 1-10"
**Content**: Complete 10-step pitch flow with all details

## 🎨 Visual Organization Tips

- **Horizontal Flow**: Place nodes left to right showing the sequence
- **Vertical Grouping**: Group related nodes vertically
- **Color Coding**: Use node titles to identify types
- **Spacing**: Leave space between node groups for clarity

## 📦 Template File Location

The template file is located at:
`modules/contactcenter/assets/templates/assistant_visual_template_inexxus.json`

You can:
- Export your own templates
- Import templates from other assistants
- Share templates with your team
- Version control templates

## 🚀 Next Steps

1. Load this template
2. Review the structure
3. Customize for your specific use case
4. Add or remove nodes as needed
5. Connect nodes to create your flow
6. Save and test your assistant
