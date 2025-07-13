<?php
if (!defined('ABSPATH')) exit;

class TW_ApiClient {
    private $api_key, $api_secret, $supplier_id, $api_url, $log;

    public function __construct() {
        $this->api_key     = get_option('tw_api_key');
        $this->api_secret  = get_option('tw_api_secret');
        $this->supplier_id = get_option('tw_supplier_id');
        $this->api_url     = 'https://apigw.trendyol.com/integration/';
        $this->log         = new TW_Logger();
    }

    private function request($endpoint, $method = 'GET', $body = null) {
        $url = $this->api_url . ltrim($endpoint, '/');
        $args = [
            'method'  => $method,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->api_key . ':' . $this->api_secret),
                'Content-Type'  => 'application/json'
            ],
            'timeout' => 30,
        ];
        if ($body) $args['body'] = json_encode($body);

        $response = wp_remote_request($url, $args);
        if (is_wp_error($response)) {
            $this->log->error('API Error: ' . $response->get_error_message());
            return false;
        }
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($data)) {
            $this->log->error('API’dan beklenmeyen cevap: ' . substr(wp_remote_retrieve_body($response), 0, 800));
            return [];
        }
        if (isset($data['errors'])) {
            $this->log->error('API Error: ' . print_r($data['errors'], true));
        }
        return $data;
    }

    public function get_categories() {
        return $this->request("product/product-categories");
    }

    public function get_brands($name = '') {
        $endpoint = "product/brands";
        if ($name) {
            $endpoint .= "?name=" . urlencode($name);
        }
        return $this->request($endpoint);
    }

    // Diğer metodlar...
}
