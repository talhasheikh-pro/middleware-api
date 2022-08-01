<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class ResourceController extends Controller
{
    public $client;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        $handler = HandlerStack::create();
        $handler->push(Middleware::mapRequest(function (RequestInterface $request) {
            $token = env('AUTH_TOKEN');
            return $request->withHeader('Authorization', "Bearer {$token}");
        }));

        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.dev.elucidate.co',
            // You can set any number of default request options.
            'timeout'  => 2.0,
            // Add global header
            'handler' => $handler,
        ]);
    }

    /**
     * Creates a post body for Ticket API
     * @param {string} $resource
     */
    public function getBodyForTicket($resource)
    {
        return [
            "project" => "projects/2a9caad1-19c7-4340-949f-30b81a8a043e",
            "title" => "missing Institution $resource",
            "description" => "add Institution $resource",
            "createdAt" => date('Y-m-d\TH:i:s.u\Z'),
            "updatedAt" => date('Y-m-d\TH:i:s.u\Z'),
        ];
    }

    /**
     * Checks if the institute exists, and returns the count, else creates a ticket
     * @param {string} $resource
     */
    public function verifyResource($resource)
    {
        try {
            $response = $this->client->get("/institutions?fullSearch={$resource}");

            $key = 'hydra:totalItems';
            $json = json_decode($response->getBody());

            $resultCount = $json->{$key};

            if ($resultCount > 0)
                return response($resultCount, 200);

            if ($resultCount <= 0) {

                $this->client->post('/tickets', [
                    'headers' =>  [ 
                        'Content-Type' => 'application/ld+json'
                    ],
                    'body' => json_encode( $this->getBodyForTicket($resource) )
                ]);

                return response('Institute not found, Relevant Ticket has been created', 200);
            }    

        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }
}
