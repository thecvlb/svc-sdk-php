<?php

namespace CVLB\Svc\Api\Services\DataSegmentation;

use CVLB\Svc\Api\HttpClient\Message\ResponseMediator;
use CVLB\Svc\Api\Sdk;

class DataSegmentation
{
    /**
     * @var Sdk
     */
    private Sdk $sdk;

    /**
     * @var string
     */
    public static string $base_uri;

    /**
     * @var string
     */
    private string $api_uri;

    /**
     * @var array
     */
    private static array $logging_endpoints = [
        'local' =>          'https://svc.dse.dev.prm-lfmd.com',
        'development' =>    'https://svc.dse.dev.prm-lfmd.com',
        'stage' =>          'https://svc.dse.stage.prm-lfmd.com',
        'production' =>     'https://svc.dse.prm-lfmd.com',
    ];

    /**
     * @param Sdk $sdk
     */
    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
        self::$base_uri = self::$logging_endpoints[$_ENV['APP_ENV']] ?? self::$logging_endpoints['development'];
        $this->api_uri = self::$base_uri;
    }

    /**
     * Get all lists
     * @return array
     */
    public function get_lists(): array
    {
        try {
            $headers = [];
            return ResponseMediator::getContent(
                $this->sdk->getHttpClient()->get(
                    $this->api_uri . '/lists',
                    $headers
                )
            );
        }
        catch (\Exception|\Http\Client\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to get lists. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Search lists
     * @param string $attr
     * @param string $val
     * @return array
     */
    public function search_lists(string $attr, string $val): array
    {
        try {
            $headers = [];
            $body = json_encode(['attr' => $attr, 'val' => $val]);
            return ResponseMediator::getContent(
                $this->sdk->getHttpClient()->post(
                $this->api_uri . '/lists/search',
                    $headers,
                    $body
                )
            );
        }
        catch (\Exception|\Http\Client\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to log to service. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Refresh lists
     * @return array
     */
    public function refresh_lists(): array
    {
        try {
            $headers = [];
            return ResponseMediator::getContent(
                $this->sdk->getHttpClient()->get(
                    $this->api_uri . '/lists/refresh',
                    $headers
                )
            );
        }
        catch (\Exception|\Http\Client\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to refresh lists. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Execute query on data lake
     * @param string $sql
     * @return array
     */
    public function query(string $sql): array
    {
        try {
            $headers = [];
            return ResponseMediator::getContent(
                $this->sdk->getHttpClient()->post(
                    $this->api_uri . '/query',
                    $headers,
                    json_encode(['sql' => $sql])
                )
            );
        }
        catch (\Exception|\Http\Client\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to execute query. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Create new list
     * @param string $query_execution_id
     * @param string $name
     * @param string $description
     * @param bool $refresh
     * @return array
     */
    public function create_list(string $query_execution_id, string $name, string $description, bool $refresh): array
    {
        try {
            $headers = [];
            $body = json_encode(['query_execution_id' => $query_execution_id, 'name' => $name, 'description' => $description, 'refresh_rate' => $refresh]);
            return ResponseMediator::getContent(
                $this->sdk->getHttpClient()->post(
                    $this->api_uri . '/list/create',
                    $headers,
                    $body
                )
            );
        }
        catch (\Exception|\Http\Client\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to create list. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get a list
     * @param string $id
     * @return array
     */
    public function view_list(string $id): array
    {
        try {
            $headers = [];
            return ResponseMediator::getContent(
                $this->sdk->getHttpClient()->get(
                    $this->api_uri . "/list/$id",
                    $headers
                )
            );
        }
        catch (\Exception|\Http\Client\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to create list. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update existing list
     * @param string $id
     * @param string $query_execution_id
     * @param string $name
     * @param string $description
     * @param bool $refresh
     * @return array
     */
    public function update_list(string $id, string $query_execution_id, string $name, string $description, bool $refresh): array
    {
        try {
            $headers = [];
            $body = json_encode(['query_execution_id' => $query_execution_id, 'name' => $name, 'description' => $description, 'refresh_rate' => $refresh]);
            return ResponseMediator::getContent(
                $this->sdk->getHttpClient()->put(
                    $this->api_uri . "/list/$id/update",
                    $headers,
                    $body
                )
            );
        }
        catch (\Exception|\Http\Client\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to update list. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Refresh a list
     * @param string $id
     * @return array
     */
    public function refresh_list(string $id): array
    {
        try {
            $headers = [];
            return ResponseMediator::getContent(
                $this->sdk->getHttpClient()->post(
                    $this->api_uri . "/list/$id/refresh",
                    $headers
                )
            );
        }
        catch (\Exception|\Http\Client\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to refresh list. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete a list
     * @param string $id
     * @return array
     */
    public function delete_list(string $id): array
    {
        try {
            $headers = [];
            return ResponseMediator::getContent(
                $this->sdk->getHttpClient()->delete(
                    $this->api_uri . "/list/$id/delete",
                    $headers
                )
            );
        }
        catch (\Exception|\Http\Client\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to delete list. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get list of databases
     * @return array
     */
    public function get_databases(): array
    {
        try {
            $headers = [];
            return ResponseMediator::getContent(
                $this->sdk->getHttpClient()->get(
                    $this->api_uri . "/databases",
                    $headers
                )
            );
        }
        catch (\Exception|\Http\Client\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to get databases. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get list of tables for a database
     * @param string $database
     * @return array
     */
    public function get_tables(string $database): array
    {
        try {
            $headers = [];
            return ResponseMediator::getContent(
                $this->sdk->getHttpClient()->get(
                    $this->api_uri . "/tables/$database",
                    $headers
                )
            );
        }
        catch (\Exception|\Http\Client\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to get tables. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }
}