<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ============================================================
 * AULA DE PHP — HELPERS
 * ============================================================
 * Helper = arquivo com funções soltas (não dentro de classe).
 * São funções utilitárias usadas em qualquer lugar do módulo.
 * O Perfex carrega com: $this->load->helper('modulo/arquivo')
 */

/**
 * Formata data/hora para exibição no chat
 * "agora", "5min", "14:30", "12/03"
 */
if (!function_exists('axch_format_time')) {
    // CONCEITO: function_exists() evita erro se o helper for carregado duas vezes
    function axch_format_time($datetime)
    {
        if (!$datetime || $datetime === '0000-00-00 00:00:00') return '';

        $ts   = strtotime($datetime); // converte "2025-03-01 14:30:00" para timestamp Unix
        $diff = time() - $ts;        // diferença em segundos até agora

        if ($diff < 60)      return 'agora';
        if ($diff < 3600)    return floor($diff / 60) . 'min';
        if ($diff < 86400)   return date('H:i', $ts);
        if ($diff < 604800)  return date('d/m', $ts);
        return date('d/m/y', $ts);
    }
}

/**
 * Retorna ícone FontAwesome por tipo de mensagem
 */
if (!function_exists('axch_message_icon')) {
    function axch_message_icon($type)
    {
        $icons = [
            'image'    => '<i class="fa fa-image text-muted"></i> ',
            'audio'    => '<i class="fa fa-microphone text-muted"></i> ',
            'video'    => '<i class="fa fa-video-camera text-muted"></i> ',
            'document' => '<i class="fa fa-file text-muted"></i> ',
        ];
        return $icons[$type] ?? '';
    }
}
