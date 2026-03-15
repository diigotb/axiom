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
}
