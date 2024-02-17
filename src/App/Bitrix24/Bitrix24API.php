<?php

declare(strict_types=1);

namespace app\Bitrix24;

use app\HTTP\HTTP;
use Generator;

/**
 * Class Bitrix24API
 * Performs requests to REST API of Bitrix24 system using the mechanism of incoming webhooks.
 *
 * @author    vladi-ri
 * @copyright 2024 vladi-ri
 * @see       https://github.com/vladi-ri/bitrix24-api
 * @license   OpenSource
 *
 * @version 1.0.0
 *
 * v1.0.0 (17.02.2024) Introduce Bitrix24API PHP project
 */
class Bitrix24API
{
    use Company;
    use Contact;
    use Deal;
    use Product;
    use Catalog;
    use ProductSection;
    use User;
    use Disk;
    use Activity;
    use Task;
    use Lead;
    use ProductRow;

    /**
     * Field name for an array of related entities of type contact
     * @var string
     */
    public static $WITH_CONTACTS  = 'CONTACTS';

    /**
     * Field name for an array of related entities of type company
     * @var string
     */
    public static $WITH_COMPANIES = 'COMPANIES';

    /**
     * Field name for an array of related entities of type product
     * @var string
     */
    public static $WITH_PRODUCTS  = 'PRODUCTS';

    /**
     * Object of the logging logging class
     * @var \App\DebugLogger\DebugLoggerInterface
     */
    public $logger;

    /**
     * Object of class \App\HTTP\HTTP
     * @var HTTP
     */
    public $http;

    /**
     * URL of the incoming webhook
     * @var string
     */
    protected $webhookUrl;

    /**
     * Last response from Bitrix24 API
     * @var array
     */
    protected $lastResponse;

    /**
     * Number of commands in a single request batch
     * @var integer
     */
    private $_BATCH_SIZE = 50;

    /**
     * Constructor
     *
     * @param string $webhookUrl URL of incoming webhook
     * 
     * @access public
     * @return void
     */
    public function __construct(string $webhookUrl) {
        // Normalisation for trailing slash (/) at the end of the URL
        $this->webhookUrl = rtrim($webhookUrl, '/');
        $this->http       = new HTTP();

        // Limit to 2 requests per second
        // see: https://dev.1c-bitrix.ru/rest_help/rest_sum/index.php
        $this->http->throttle   = 2;
        $this->http->useCookies = false;
    }

    /**
     * Set object of logging class
     * 
     * @param \App\DebugLogger\DebugLoggerInterface $logger
     * 
     * @access public
     * @return void
     */
    public function setLogger(DebugLoggerInterface $logger) : void {
        if (!($logger instanceof \App\DebugLogger\DebugLoggerInterface)) {
            throw new Bitrix24APIException(
                "An object of the logger class must implement the interface \App\DebugLogger\DebugLoggerInterface"
            );
        }

        $this->logger = $logger;
    }

    /**
     * Returns the last response from Bitrix24 API
     * 
     * @access public
     * @return mixed
     */
    public function getLastResponse() : mixed {
        return $this->lastResponse;
    }

    /**
     * Sends a request to Bitrix24 API
     *
     * @param  string $function Name of the request method (function)
     * @param  array  $params   Query parameter
     * 
     * @access public
     * @return array|null
     */
    public function request(string $function, array $params = []) : array|null {
        $function .= '.json';
        $url       = $this->webhookUrl . '/' . $function;

        // Request logging
        if (isset($this->logger)) {
            $jsonParams = urldecode($this->toJSON($params, true));
            $this->logger->save("Request: {$function}" . PHP_EOL . $jsonParams, $this);
        }

        // POST request
        $this->lastResponse = $this->http->request($url, 'POST', $params);

        // Response logging
        if (isset($this->logger)) {
            $jsonResponse = $this->toJSON($this->lastResponse, true);
            $this->logger->save("Response: {$function}" . PHP_EOL . $jsonResponse, $this);
        }

        // Checking HTTP status code
        if (!$this->http->isSuccess()) {
            $httpCode     = $this->http->getHTTPCode();
            $jsonParams   = $this->toJSON($params);
            $jsonResponse = $this->toJSON($this->lastResponse);

            throw new Bitrix24APIException(
                "Error: HTTP status code {$httpCode} upon request '{$function}' ({$jsonParams}): {$jsonResponse}"
            );
        }

        // Checking for errors in the response
        if (
            !empty($this->lastResponse['error'])
            || !empty($this->lastResponse['error_description'])
        ) {
            $jsonParams   = $this->toJSON($params);
            $jsonResponse = $this->toJSON($this->lastResponse);

            throw new Bitrix24APIException("Error on request '{$function}' ({$jsonParams}): {$jsonResponse}");
        }

        return $this->lastResponse['result'];
    }

    /**
     * Returns list of all entities
     *
     * @see    https://dev.1c-bitrix.ru/rest_help/general/lists.php
     * 
     * @param  string $function Name of the request method (function)
     * @param  array  $params   Query parameter of request
     * 
     * @access public
     * @return Generator
     */
    public function getList(string $function, array $params = []) : Generator {
        do {
            // Up to 50 pieces per request
            $result = $this->request(
                $function,
                $params
            );

            $start  = $params['start'] ?? 0;

            if ($this->logger) {
                $this->logger->save(
                    "On request (getList) {$function} (start: {$start}) obtained entities: " . count($result) .
                    ", in total there are: " . $this->lastResponse['total'],
                    $this
                );
            }

            yield $result;

            if (empty($this->lastResponse['next'])) {
                break;
            }

            $params['start'] = $this->lastResponse['next'];
        } while (true);
    }

    /**
     * Returns a list of all entities using the quick method
     *
     * @see    https://dev.1c-bitrix.ru/rest_help/rest_sum/start.php
     * @see    https://dev.1c-bitrix.ru/rest_help/general/lists.php
     * 
     * @param  string $function Name of the request method (function)
     * @param  array  $params   Query parameter of request
     * 
     * @access public
     * @return Generator
     */
    public function fetchList(string $function, array $params = []) : Generator {
        $params['order']['ID']   = 'ASC';
        $params['filter']['>ID'] = 0;
        $params['start']         = -1;

        $totalCounter            = 0;

        do {
            // Up to 50 pieces per request
            $result = $this->request(
                $function,
                $params
            );

            $resultCounter = count($result);
            $totalCounter += $resultCounter;

            if ($this->logger) {
                $this->logger->save(
                    "On request (fetchList) {$function} obtained entities: {$resultCounter}, " .
                    "Total recieved: {$totalCounter}",
                    $this
                );
            }

            yield $result;

            if ($resultCounter < $this->_BATCH_SIZE) {
                break;
            }

            $params['filter']['>ID'] = $result[ $resultCounter - 1 ]['ID'];
        } while (true);
    }

    /**
     * Sends a request package to Bitrix24 API
     *
     * @see    https://dev.1c-bitrix.ru/rest_help/general/batch.php
     * 
     * @param  array $commands Command package
     * @param  bool  $halt     Determines whether to interrupt the query sequence in case of an error
     *                         (0|1, true|false)
     * 
     * @access public
     * @return array|null
     */
    public function batchRequest(array $commands, $halt = true) : array|null {
        // Up to 50 pieces per request
        $result = $this->request(
            'batch',
            [
                'halt' => (int) $halt,
                'cmd'  => $commands
            ]
        );

        // Check if there are any errors in the response from the batch request
        if (!empty($result['result_error'])) {
            $jsonCommands = $this->toJSON($commands);
            $jsonResponse = $this->toJSON($this->lastResponse);

            throw new Bitrix24APIException("Error during batch request ({$jsonCommands}): {$jsonResponse}");
        }

        return $result['result'];
    }

    /**
     * Forms an array of identical commands for the batchRequest() batch request method
     *
     * @param string $function Name of the request method (function)
     * @param array  $items    Array of query fields
     * 
     * @access public
     * @return array
     */
    public function buildCommands(string $function, array $items) : array {
        $commands = [];

        foreach ($items as $fields) {
            $commands[] = $this->buildCommand($function, $fields);
        }

        return $commands;
    }

    /**
     * Forms a single command string for a request package
     * 
     * Action        'batch'
     * @see          https://dev.1c-bitrix.ru/rest_help/general/batch.php
     * 
     * @param string $function Name of the request method (function)
     * @param array  $params   Array of command parameters
     * 
     * @access public
     * @return string
     */
    public function buildCommand(string $function, array $params) : string {
        return $function . '?' . http_build_query($params);
    }

    /**
     * Creates and returns a result with related entities
     *
     * @param array  $result Result
     * @param string $base   Name of the base entity
     * @param array  $with   Related entity names
     * 
     * @access protected
     * @return array
     */
    protected function createResultWith(array $result, string $base, array $with) : array {
        $resultWith = $result[$base];

        foreach ($with as $name) {
            $resultWith[$name] = $result[$name];
        }

        return $resultWith;
    }

    /**
     * Converts data to a JSON string for error or log messages
     *
     * @param mixed $data        Data for conversion
     * @param bool  $prettyPrint Enables pretty print for JSON
     * 
     * @access protected
     * @return string
     */
    protected function toJSON(mixed $data, bool $prettyPrint = false) : string {
        $encodeOptions = JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR;

        if ($prettyPrint) {
            $encodeOptions |= JSON_PRETTY_PRINT;
        }

        $jsonParams = json_encode($data, $encodeOptions);

        if ($jsonParams === false) {
            $jsonParams = print_r($data, true);
        }

        return $jsonParams;
    }
}
