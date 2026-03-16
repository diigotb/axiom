<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Axiomchannel_model
 * Todas as operações de banco do AxiomChannel
 */
class Axiomchannel_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ============================================================
    // DEVICES
    // ============================================================

    public function get_devices($staff_id = null)
    {
        if ($staff_id) {
            $this->db->where('assigned_staff', $staff_id);
        }
        return $this->db->get(db_prefix() . 'axch_devices')->result();
    }

    public function get_device($id)
    {
        return $this->db->get_where(db_prefix() . 'axch_devices', ['id' => $id])->row();
    }

    public function get_device_by_instance($instance_name)
    {
        return $this->db->get_where(db_prefix() . 'axch_devices', ['instance_name' => $instance_name])->row();
    }

    public function add_device($data)
    {
        $this->db->insert(db_prefix() . 'axch_devices', [
            'name'          => $data['name'],
            'phone_number'  => $data['phone_number'] ?? null,
            'instance_name' => $data['instance_name'],
            'server_url'    => $data['server_url'] ?? 'http://localhost:8080',
            'api_key'       => $data['api_key'] ?? null,
            'assigned_staff' => $data['assigned_staff'] ?? get_staff_user_id(),
        ]);
        return $this->db->insert_id();
    }

    public function update_device($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update(db_prefix() . 'axch_devices', $data, ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }

    public function update_device_status($id, $status)
    {
        return $this->update_device($id, ['status' => $status]);
    }

    public function delete_device($id)
    {
        $this->db->delete(db_prefix() . 'axch_devices', ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }

    // ============================================================
    // CONTACTS / CONVERSATIONS
    // ============================================================

    public function get_contacts($filters = [])
    {
        $this->db->select(db_prefix() . 'axch_contacts.*, 
            tblstaff.firstname, tblstaff.lastname,
            tblleads.name as lead_name');
        $this->db->from(db_prefix() . 'axch_contacts');
        $this->db->join('tblstaff', 'tblstaff.staffid = ' . db_prefix() . 'axch_contacts.assigned_staff', 'left');
        $this->db->join('tblleads', 'tblleads.id = ' . db_prefix() . 'axch_contacts.lead_id', 'left');

        if (!empty($filters['device_id'])) {
            $this->db->where(db_prefix() . 'axch_contacts.device_id', $filters['device_id']);
        }
        if (!empty($filters['status'])) {
            $this->db->where(db_prefix() . 'axch_contacts.status', $filters['status']);
        }
        if (!empty($filters['assigned_staff'])) {
            $this->db->where(db_prefix() . 'axch_contacts.assigned_staff', $filters['assigned_staff']);
        }
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like(db_prefix() . 'axch_contacts.name', $filters['search']);
            $this->db->or_like(db_prefix() . 'axch_contacts.phone_number', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by(db_prefix() . 'axch_contacts.last_message_at', 'DESC');

        if (!empty($filters['limit'])) {
            $this->db->limit($filters['limit'], $filters['offset'] ?? 0);
        }

        return $this->db->get()->result();
    }

    public function get_contact($id)
    {
        $this->db->select(db_prefix() . 'axch_contacts.*, tblleads.name as lead_name, tblleads.email as lead_email');
        $this->db->from(db_prefix() . 'axch_contacts');
        $this->db->join('tblleads', 'tblleads.id = ' . db_prefix() . 'axch_contacts.lead_id', 'left');
        $this->db->where(db_prefix() . 'axch_contacts.id', $id);
        return $this->db->get()->row();
    }

    public function get_or_create_contact($device_id, $phone_number, $name = null)
    {
        $contact = $this->db->get_where(db_prefix() . 'axch_contacts', [
            'device_id'    => $device_id,
            'phone_number' => $phone_number,
        ])->row();

        if (!$contact) {
            $this->db->insert(db_prefix() . 'axch_contacts', [
                'device_id'    => $device_id,
                'phone_number' => $phone_number,
                'name'         => $name ?? $phone_number,
                'status'       => 'open',
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
            $contact_id = $this->db->insert_id();
            $contact = $this->get_contact($contact_id);
        } elseif ($name && empty($contact->name)) {
            $this->db->update(db_prefix() . 'axch_contacts', ['name' => $name], ['id' => $contact->id]);
        }

        return $contact;
    }

    public function update_contact($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update(db_prefix() . 'axch_contacts', $data, ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }

    public function mark_contact_read($contact_id)
    {
        $this->db->update(db_prefix() . 'axch_contacts', ['is_read' => 1], ['id' => $contact_id]);
        $this->db->update(db_prefix() . 'axch_messages', ['is_read' => 1], [
            'contact_id' => $contact_id,
            'direction'  => 'inbound',
            'is_read'    => 0,
        ]);
    }

    public function count_unread_contacts()
    {
        return $this->db->where('is_read', 0)->where('status', 'open')->count_all_results(db_prefix() . 'axch_contacts');
    }

    // ============================================================
    // MESSAGES
    // ============================================================

    public function get_messages($contact_id, $limit = 50, $offset = 0)
    {
        $this->db->select(db_prefix() . 'axch_messages.*, tblstaff.firstname, tblstaff.lastname');
        $this->db->from(db_prefix() . 'axch_messages');
        $this->db->join('tblstaff', 'tblstaff.staffid = ' . db_prefix() . 'axch_messages.sent_by_staff', 'left');
        $this->db->where('contact_id', $contact_id);
        $this->db->order_by('created_at', 'ASC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function get_messages_since($contact_id, $since_id = 0)
    {
        $this->db->select(db_prefix() . 'axch_messages.*, tblstaff.firstname, tblstaff.lastname');
        $this->db->from(db_prefix() . 'axch_messages');
        $this->db->join('tblstaff', 'tblstaff.staffid = ' . db_prefix() . 'axch_messages.sent_by_staff', 'left');
        $this->db->where('contact_id', $contact_id);
        if ($since_id > 0) {
            $this->db->where(db_prefix() . 'axch_messages.id >', $since_id);
        }
        $this->db->order_by(db_prefix() . 'axch_messages.created_at', 'ASC');
        $this->db->limit(50);
        return $this->db->get()->result();
    }

    public function save_message($data)
    {
        // Evitar duplicatas por external_id
        if (!empty($data['external_id'])) {
            $exists = $this->db->get_where(db_prefix() . 'axch_messages', [
                'external_id' => $data['external_id'],
                'device_id'   => $data['device_id'],
            ])->row();
            if ($exists) return $exists->id;
        }

        $this->db->insert(db_prefix() . 'axch_messages', [
            'contact_id'     => $data['contact_id'],
            'device_id'      => $data['device_id'],
            'external_id'    => $data['external_id'] ?? null,
            'direction'      => $data['direction'],
            'type'           => $data['type'] ?? 'text',
            'content'        => $data['content'] ?? null,
            'media_url'      => $data['media_url'] ?? null,
            'media_filename' => $data['media_filename'] ?? null,
            'sent_by_staff'  => $data['sent_by_staff'] ?? null,
            'sent_by_ai'     => $data['sent_by_ai'] ?? 0,
            'status'         => $data['status'] ?? 'sent',
            'created_at'     => $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);

        $message_id = $this->db->insert_id();

        // Atualizar última mensagem no contato
        $this->db->update(db_prefix() . 'axch_contacts', [
            'last_message'    => substr($data['content'] ?? '[mídia]', 0, 200),
            'last_message_at' => $data['created_at'] ?? date('Y-m-d H:i:s'),
            'is_read'         => ($data['direction'] === 'outbound') ? 1 : 0,
            'updated_at'      => date('Y-m-d H:i:s'),
        ], ['id' => $data['contact_id']]);

        return $message_id;
    }

    // ============================================================
    // TRANSFERÊNCIAS
    // ============================================================

    public function transfer_contact($contact_id, $to_staff, $note = null)
    {
        $current = $this->get_contact($contact_id);

        $this->db->insert(db_prefix() . 'axch_transfers', [
            'contact_id' => $contact_id,
            'from_staff' => $current->assigned_staff ?? null,
            'to_staff'   => $to_staff,
            'note'       => $note,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->update_contact($contact_id, ['assigned_staff' => $to_staff]);
        return true;
    }

    // ============================================================
    // FILA DE ENVIO
    // ============================================================

    public function queue_message($device_id, $phone, $message, $type = 'text', $scheduled_at = null)
    {
        $this->db->insert(db_prefix() . 'axch_queue', [
            'device_id'    => $device_id,
            'phone_number' => $phone,
            'message'      => $message,
            'type'         => $type,
            'status'       => 'pending',
            'scheduled_at' => $scheduled_at ?? date('Y-m-d H:i:s'),
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
        return $this->db->insert_id();
    }

    public function get_pending_queue($limit = 10)
    {
        return $this->db
            ->where('status', 'pending')
            ->where('scheduled_at <=', date('Y-m-d H:i:s'))
            ->order_by('scheduled_at', 'ASC')
            ->limit($limit)
            ->get(db_prefix() . 'axch_queue')
            ->result();
    }

    // ============================================================
    // PIPELINE
    // ============================================================

    public function get_pipelines($device_id = null)
    {
        if ($device_id) {
            $this->db->where('device_id', $device_id);
        }
        $this->db->order_by('is_default', 'DESC');
        return $this->db->get(db_prefix() . 'axch_pipelines')->result();
    }

    public function get_pipeline($id)
    {
        return $this->db->get_where(db_prefix() . 'axch_pipelines', ['id' => $id])->row();
    }

    public function create_pipeline($data)
    {
        $this->db->insert(db_prefix() . 'axch_pipelines', [
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'device_id'   => $data['device_id'] ?? null,
            'is_default'  => $data['is_default'] ?? 0,
            'created_by'  => get_staff_user_id(),
        ]);
        return $this->db->insert_id();
    }

    public function delete_pipeline($id)
    {
        $this->db->delete(db_prefix() . 'axch_pipeline_stages', ['pipeline_id' => $id]);
        $this->db->delete(db_prefix() . 'axch_pipelines', ['id' => $id]);
        return true;
    }

    // ============================================================
    // ESTÁGIOS DO PIPELINE
    // ============================================================

    public function get_stages($pipeline_id)
    {
        return $this->db
            ->where('pipeline_id', $pipeline_id)
            ->order_by('position', 'ASC')
            ->get(db_prefix() . 'axch_pipeline_stages')
            ->result();
    }

    public function create_stage($data)
    {
        $this->db->insert(db_prefix() . 'axch_pipeline_stages', [
            'pipeline_id' => $data['pipeline_id'],
            'name'        => $data['name'],
            'color'       => $data['color'] ?? '#8A9BAE',
            'position'    => $data['position'] ?? 0,
            'ai_action'   => $data['ai_action'] ?? null,
            'ai_keywords' => $data['ai_keywords'] ?? null,
            'auto_move'   => $data['auto_move'] ?? 0,
        ]);
        return $this->db->insert_id();
    }

    public function update_stage($id, $data)
    {
        $this->db->update(db_prefix() . 'axch_pipeline_stages', $data, ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }

    public function delete_stage($id)
    {
        $this->db->delete(db_prefix() . 'axch_pipeline_stages', ['id' => $id]);
        return true;
    }

    public function reorder_stages($stages)
    {
        // $stages = array de ['id' => X, 'position' => Y]
        foreach ($stages as $s) {
            $this->db->update(
                db_prefix() . 'axch_pipeline_stages',
                ['position' => $s['position']],
                ['id' => $s['id']]
            );
        }
        return true;
    }

    // ============================================================
    // CRM LEADS
    // ============================================================

    public function get_crm_leads($pipeline_id, $stage_id = null)
    {
        $this->db->select(db_prefix() . 'axch_crm_leads.*, tblstaff.firstname, tblstaff.lastname');
        $this->db->from(db_prefix() . 'axch_crm_leads');
        $this->db->join('tblstaff', 'tblstaff.staffid = ' . db_prefix() . 'axch_crm_leads.assigned_staff', 'left');
        $this->db->where('pipeline_id', $pipeline_id);
        if ($stage_id) {
            $this->db->where('stage_id', $stage_id);
        }
        $this->db->order_by('position', 'ASC');
        return $this->db->get()->result();
    }

    public function get_crm_lead($id)
    {
        return $this->db->get_where(db_prefix() . 'axch_crm_leads', ['id' => $id])->row();
    }

    public function get_lead_by_contact($contact_id, $pipeline_id)
    {
        return $this->db->get_where(db_prefix() . 'axch_crm_leads', [
            'contact_id'  => $contact_id,
            'pipeline_id' => $pipeline_id,
        ])->row();
    }

    public function get_lead_by_contact_any($contact_id)
    {
        return $this->db
            ->where('contact_id', $contact_id)
            ->order_by('updated_at', 'DESC')
            ->limit(1)
            ->get(db_prefix() . 'axch_crm_leads')
            ->row();
    }

    public function create_crm_lead($data)
    {
        $this->db->insert(db_prefix() . 'axch_crm_leads', [
            'contact_id'     => $data['contact_id'],
            'pipeline_id'    => $data['pipeline_id'],
            'stage_id'       => $data['stage_id'],
            'name'           => $data['name'] ?? null,
            'phone'          => $data['phone'] ?? null,
            'email'          => $data['email'] ?? null,
            'notes'          => $data['notes'] ?? null,
            'value'          => $data['value'] ?? 0,
            'assigned_staff' => $data['assigned_staff'] ?? null,
            'position'       => $data['position'] ?? 0,
        ]);
        return $this->db->insert_id();
    }

    public function move_lead_stage($lead_id, $stage_id, $moved_by = 'human', $staff_id = null, $note = null)
    {
        $lead = $this->get_crm_lead($lead_id);
        if (!$lead) return false;

        // Salva histórico
        $this->db->insert(db_prefix() . 'axch_pipeline_history', [
            'lead_id'    => $lead_id,
            'from_stage' => $lead->stage_id,
            'to_stage'   => $stage_id,
            'moved_by'   => $moved_by,
            'staff_id'   => $staff_id,
            'note'       => $note,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Move o lead
        $this->db->update(db_prefix() . 'axch_crm_leads', [
            'stage_id'   => $stage_id,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $lead_id]);

        return true;
    }

    public function update_crm_lead($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update(db_prefix() . 'axch_crm_leads', $data, ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }

    public function reorder_leads($leads)
    {
        foreach ($leads as $l) {
            $this->db->update(
                db_prefix() . 'axch_crm_leads',
                ['stage_id' => $l['stage_id'], 'position' => $l['position']],
                ['id' => $l['id']]
            );
        }
        return true;
    }

    public function get_pipeline_history($lead_id)
    {
        $this->db->select(db_prefix() . 'axch_pipeline_history.*, 
            fs.name as from_stage_name, ts.name as to_stage_name,
            tblstaff.firstname, tblstaff.lastname');
        $this->db->from(db_prefix() . 'axch_pipeline_history');
        $this->db->join(db_prefix() . 'axch_pipeline_stages fs',
            'fs.id = ' . db_prefix() . 'axch_pipeline_history.from_stage', 'left');
        $this->db->join(db_prefix() . 'axch_pipeline_stages ts',
            'ts.id = ' . db_prefix() . 'axch_pipeline_history.to_stage', 'left');
        $this->db->join('tblstaff',
            'tblstaff.staffid = ' . db_prefix() . 'axch_pipeline_history.staff_id', 'left');
        $this->db->where('lead_id', $lead_id);
        $this->db->order_by(db_prefix() . 'axch_pipeline_history.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // ============================================================
    // WIZARD — criar pipeline via IA
    // ============================================================

    public function save_wizard_pipeline($pipeline_id, $stages)
    {
        // Salva todos os estágios gerados pela IA de uma vez
        foreach ($stages as $i => $stage) {
            $this->create_stage([
                'pipeline_id' => $pipeline_id,
                'name'        => $stage['name'],
                'color'       => $stage['color'],
                'position'    => $i,
                'ai_action'   => $stage['ai_action'] ?? null,
                'ai_keywords' => isset($stage['ai_keywords'])
                    ? json_encode($stage['ai_keywords'])
                    : null,
                'auto_move'   => 1,
            ]);
        }
        return true;
    }

    // ============================================================
    // ASSISTENTE DE IA
    // ============================================================

    public function get_assistant($device_id)
    {
        return $this->db
            ->where('device_id', $device_id)
            ->limit(1)
            ->get(db_prefix() . 'axch_assistants')
            ->row();
    }

    public function get_assistant_by_id($id)
    {
        return $this->db->get_where(db_prefix() . 'axch_assistants', ['id' => $id])->row();
    }

    public function save_assistant($data)
    {
        $existing = null;
        if (!empty($data['device_id'])) {
            $existing = $this->get_assistant($data['device_id']);
        } elseif (!empty($data['id'])) {
            $existing = $this->get_assistant_by_id($data['id']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($existing) {
            $this->db->update(db_prefix() . 'axch_assistants', $data, ['id' => $existing->id]);
            return $existing->id;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'axch_assistants', $data);
        return $this->db->insert_id();
    }

    public function toggle_assistant($id)
    {
        $assistant = $this->get_assistant_by_id($id);
        if (!$assistant) return false;
        $new_state = $assistant->is_active ? 0 : 1;
        $this->db->update(db_prefix() . 'axch_assistants', ['is_active' => $new_state], ['id' => $id]);
        return $new_state;
    }

    // ============================================================
    // BASE DE CONHECIMENTO
    // ============================================================

    public function get_knowledge_base($assistant_id, $category = null)
    {
        $this->db->where('assistant_id', $assistant_id);
        if ($category) {
            $this->db->where('category', $category);
        }
        $this->db->order_by('position', 'ASC');
        return $this->db->get(db_prefix() . 'axch_knowledge_base')->result();
    }

    public function save_knowledge_item($data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (!empty($data['id'])) {
            $id = (int) $data['id'];
            unset($data['id']);
            $this->db->update(db_prefix() . 'axch_knowledge_base', $data, ['id' => $id]);
            return $id;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'axch_knowledge_base', $data);
        return $this->db->insert_id();
    }

    public function delete_knowledge_item($id)
    {
        $this->db->delete(db_prefix() . 'axch_knowledge_base', ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }

    public function reorder_knowledge($items)
    {
        foreach ($items as $item) {
            $this->db->update(
                db_prefix() . 'axch_knowledge_base',
                ['position' => (int) $item['position']],
                ['id' => (int) $item['id']]
            );
        }
        return true;
    }

    // ============================================================
    // ETAPAS DO FLUXO DE QUALIFICAÇÃO
    // ============================================================

    public function get_assistant_stages($assistant_id)
    {
        return $this->db
            ->where('assistant_id', $assistant_id)
            ->order_by('position', 'ASC')
            ->get(db_prefix() . 'axch_assistant_stages')
            ->result();
    }

    public function save_assistant_stage($data)
    {
        // Garante que media_id NULL quando vazio
        if (isset($data['media_id']) && $data['media_id'] === '') {
            $data['media_id'] = null;
        }

        if (!empty($data['id'])) {
            $id = (int) $data['id'];
            unset($data['id']);
            $this->db->update(db_prefix() . 'axch_assistant_stages', $data, ['id' => $id]);
            return $id;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'axch_assistant_stages', $data);
        return $this->db->insert_id();
    }

    public function get_stage_media($stage_id)
    {
        $stage = $this->db->get_where(db_prefix() . 'axch_assistant_stages', ['id' => (int) $stage_id])->row();
        if (!$stage || !$stage->media_id) return null;

        $media = $this->db->get_where(db_prefix() . 'axch_knowledge_media', ['id' => (int) $stage->media_id])->row();
        if (!$media) return null;

        $media->send_position = $stage->media_send_position ?? 'with_message';
        $media->url           = base_url($media->file_path);
        return $media;
    }

    public function delete_assistant_stage($id)
    {
        $this->db->delete(db_prefix() . 'axch_assistant_stages', ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }

    public function reorder_assistant_stages($items)
    {
        foreach ($items as $item) {
            $this->db->update(
                db_prefix() . 'axch_assistant_stages',
                ['position' => (int) $item['position']],
                ['id' => (int) $item['id']]
            );
        }
        return true;
    }

    // ============================================================
    // KNOWLEDGE MEDIA
    // ============================================================

    public function get_knowledge_media($knowledge_id)
    {
        return $this->db
            ->where('knowledge_id', (int) $knowledge_id)
            ->order_by('id', 'ASC')
            ->get(db_prefix() . 'axch_knowledge_media')
            ->result();
    }

    public function save_knowledge_media($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'axch_knowledge_media', $data);
        return $this->db->insert_id();
    }

    public function delete_knowledge_media($id)
    {
        $row = $this->db->get_where(db_prefix() . 'axch_knowledge_media', ['id' => $id])->row();
        if ($row) {
            $this->db->delete(db_prefix() . 'axch_knowledge_media', ['id' => $id]);
            return $row;
        }
        return false;
    }

    public function get_media_by_assistant($assistant_id)
    {
        return $this->db
            ->where('assistant_id', (int) $assistant_id)
            ->order_by('id', 'DESC')
            ->get(db_prefix() . 'axch_knowledge_media')
            ->result();
    }

    // ============================================================
    // APPOINTMENTS
    // ============================================================

    public function create_appointment($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'axch_appointments', $data);
        return $this->db->insert_id();
    }

    public function get_appointments($contact_id = null, $device_id = null, $date = null)
    {
        if ($contact_id) $this->db->where('contact_id', (int) $contact_id);
        if ($device_id)  $this->db->where('device_id',  (int) $device_id);
        if ($date) {
            $this->db->where('DATE(start_datetime)', $date);
        }
        return $this->db
            ->order_by('start_datetime', 'ASC')
            ->get(db_prefix() . 'axch_appointments')
            ->result();
    }

    public function get_appointment($id)
    {
        return $this->db->get_where(db_prefix() . 'axch_appointments', ['id' => (int) $id])->row();
    }

    public function update_appointment($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update(db_prefix() . 'axch_appointments', $data, ['id' => (int) $id]);
        return $this->db->affected_rows() > 0;
    }

    public function delete_appointment($id)
    {
        $this->db->delete(db_prefix() . 'axch_appointments', ['id' => (int) $id]);
        return $this->db->affected_rows() > 0;
    }

    public function get_available_slots($device_id, $date)
    {
        // Busca assistant config para horários
        $assistant = $this->db
            ->where('device_id', (int) $device_id)
            ->where('is_active', 1)
            ->get(db_prefix() . 'axch_assistants')
            ->row();

        $start_time = $assistant->appointment_start ?? '08:00:00';
        $end_time   = $assistant->appointment_end   ?? '18:00:00';
        $duration   = (int) ($assistant->appointment_duration ?? 60);
        $interval   = (int) ($assistant->appointment_interval ?? 30);

        // Agendamentos já existentes nessa data
        $booked = $this->db
            ->where('device_id', (int) $device_id)
            ->where('DATE(start_datetime)', $date)
            ->where_in('status', ['pending', 'confirmed'])
            ->get(db_prefix() . 'axch_appointments')
            ->result();

        $booked_ranges = [];
        foreach ($booked as $b) {
            $booked_ranges[] = [
                'start' => strtotime($b->start_datetime),
                'end'   => strtotime($b->end_datetime),
            ];
        }

        $slots     = [];
        $slot_start = strtotime($date . ' ' . $start_time);
        $day_end    = strtotime($date . ' ' . $end_time);

        while ($slot_start + ($duration * 60) <= $day_end) {
            $slot_end  = $slot_start + ($duration * 60);
            $available = true;
            foreach ($booked_ranges as $r) {
                if ($slot_start < $r['end'] && $slot_end > $r['start']) {
                    $available = false;
                    break;
                }
            }
            if ($available) {
                $slots[] = [
                    'start' => date('H:i', $slot_start),
                    'end'   => date('H:i', $slot_end),
                ];
            }
            $slot_start += $interval * 60;
        }

        return $slots;
    }

    public function save_google_calendar($data)
    {
        $device_id = (int) $data['device_id'];
        $existing  = $this->db->get_where(db_prefix() . 'axch_google_calendar', ['device_id' => $device_id])->row();

        if ($existing) {
            $this->db->update(db_prefix() . 'axch_google_calendar', $data, ['device_id' => $device_id]);
            return $existing->id;
        }

        $this->db->insert(db_prefix() . 'axch_google_calendar', $data);
        return $this->db->insert_id();
    }

    public function get_google_calendar($device_id)
    {
        return $this->db->get_where(db_prefix() . 'axch_google_calendar', ['device_id' => (int) $device_id])->row();
    }

    // ============================================================
    // CONTRACTS
    // ============================================================

    public function create_contract($data)
    {
        $data['sign_token'] = $data['sign_token'] ?? bin2hex(random_bytes(32));
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'axch_contracts', $data);
        return $this->db->insert_id();
    }

    public function get_contracts($contact_id = null, $device_id = null)
    {
        if ($contact_id) $this->db->where('contact_id', (int) $contact_id);
        if ($device_id)  $this->db->where('device_id',  (int) $device_id);
        return $this->db
            ->order_by('created_at', 'DESC')
            ->get(db_prefix() . 'axch_contracts')
            ->result();
    }

    public function get_contract($id)
    {
        return $this->db->get_where(db_prefix() . 'axch_contracts', ['id' => (int) $id])->row();
    }

    public function get_contract_by_token($token)
    {
        return $this->db->get_where(db_prefix() . 'axch_contracts', ['sign_token' => $token])->row();
    }

    public function update_contract($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update(db_prefix() . 'axch_contracts', $data, ['id' => (int) $id]);
        return $this->db->affected_rows() > 0;
    }

    public function sign_contract($token, $signer_data)
    {
        $contract = $this->get_contract_by_token($token);
        if (!$contract || $contract->status === 'signed') return false;

        $this->db->update(db_prefix() . 'axch_contracts', array_merge($signer_data, [
            'status'      => 'signed',
            'signed_at'   => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]), ['sign_token' => $token]);

        return $this->db->affected_rows() > 0;
    }

    public function get_contract_templates($device_id = null)
    {
        if ($device_id) {
            $this->db->group_start()
                ->where('device_id', (int) $device_id)
                ->or_where('device_id IS NULL', null, false)
                ->group_end();
        }
        return $this->db
            ->order_by('name', 'ASC')
            ->get(db_prefix() . 'axch_contract_templates')
            ->result();
    }

    public function save_contract_template($data)
    {
        if (!empty($data['id'])) {
            $id = (int) $data['id'];
            unset($data['id']);
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->update(db_prefix() . 'axch_contract_templates', $data, ['id' => $id]);
            return $id;
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'axch_contract_templates', $data);
        return $this->db->insert_id();
    }

    public function delete_contract_template($id)
    {
        $this->db->delete(db_prefix() . 'axch_contract_templates', ['id' => (int) $id]);
        return $this->db->affected_rows() > 0;
    }

    // ============================================================
    // META — Facebook + Instagram
    // ============================================================

    public function get_meta_connection($device_id)
    {
        return $this->db->get_where(
            db_prefix() . 'axch_meta_connections',
            ['device_id' => $device_id, 'is_active' => 1]
        )->row();
    }

    public function save_meta_connection($data)
    {
        $existing = $this->get_meta_connection($data['device_id']);
        if ($existing) {
            $this->db->update(
                db_prefix() . 'axch_meta_connections',
                $data,
                ['device_id' => $data['device_id']]
            );
            return $existing->id;
        }
        $this->db->insert(db_prefix() . 'axch_meta_connections', $data);
        return $this->db->insert_id();
    }

    public function get_contact_by_external($external_id, $channel, $device_id)
    {
        return $this->db->get_where(
            db_prefix() . 'axch_contacts',
            [
                'external_id' => $external_id,
                'channel'     => $channel,
                'device_id'   => $device_id,
            ]
        )->row();
    }
}
