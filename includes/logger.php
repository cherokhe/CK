<?php
if (!defined('ABSPATH')) exit;

class TW_Logger {
    public function error($msg) {
        $this->log('error', $msg);
    }
    public function info($msg) {
        $this->log('info', $msg);
    }
    private function log($level, $msg) {
        $logs = get_option('tw_logs', []);
        $logs[] = [
            'time'  => current_time('mysql'),
            'level' => strtoupper($level),
            'msg'   => $msg
        ];
        update_option('tw_logs', $logs, false);
    }
    public function get_logs() {
        return get_option('tw_logs', []);
    }
    public function clear() {
        update_option('tw_logs', []);
    }
}
