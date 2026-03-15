-- Omni Pilot Wizard Tables
-- Run this SQL directly in phpMyAdmin or MySQL if you prefer not to reinstall the module

CREATE TABLE IF NOT EXISTS `tblcontactcenter_omni_pilot_sessions` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `staffid` int(11) DEFAULT NULL,
    `device_id` int(11) DEFAULT NULL,
    `goal_target` int(11) DEFAULT 0,
    `goal_status_id` int(11) DEFAULT NULL,
    `deadline_date` datetime DEFAULT NULL,
    `tag_id` int(11) DEFAULT NULL,
    `campaign_id` int(11) DEFAULT NULL,
    `leads_engine_id` int(11) DEFAULT NULL,
    `status` enum('pending','importing','campaign_setup','message_setup','followup_setup','active','completed','failed') DEFAULT 'pending',
    `progress_percentage` int(11) DEFAULT 0,
    `current_phase` varchar(255) DEFAULT NULL,
    `error_message` TEXT DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `completed_at` datetime DEFAULT NULL,
    INDEX `idx_staffid` (`staffid`),
    INDEX `idx_status` (`status`),
    INDEX `idx_device_id` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tblcontactcenter_omni_pilot_steps` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `session_id` int(11) DEFAULT NULL,
    `step_number` int(11) DEFAULT NULL,
    `step_data` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `completed` tinyint(1) DEFAULT 0,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    INDEX `idx_session_id` (`session_id`),
    INDEX `idx_step_number` (`session_id`, `step_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
