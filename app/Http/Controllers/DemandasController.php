<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Request;

class DemandasController extends Controller
{
    public array $demandas = [];
    protected $client;
    protected $apiUrl;

    public function __construct()
    {
        $this->client = new Client([
            'verify' => false
        ]);
        $this->apiUrl = env('PROTHEUS_API_URL');
    }

    public function getDemandas(Request $request)
    {
        try {
            $perPage = 20;
            $page = $request->input('page', 1);
            $info = $request->input('info');

            $url = $this->apiUrl. '?TIPO=2';

            if ($info) {
                $url.= '&INFO='. $info;
            }

            $response = $this->client->get($url, [
                'auth' => [
                    env('PROTHEUS_API_USER'), 
                    env('PROTHEUS_API_PASSWORD')
                ]
            ]);

            $demandas = json_decode($response->getBody(), true);
            $offset = ($page - 1) * $perPage; 

            $newDemandas = [];
            foreach ($demandas as $demanda) {
                $newDemandas[] = [
                    'codigo' => $demanda['codigo'],
                    'descricao' => $demanda['descricao'],
                    'descricaoweb' => $demanda['descricaoweb'],
                ];
            }

            $items = array_slice($demandas, $offset, $perPage);

            $response = [
                $items,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => count($demandas), 
                    'last_page' => ceil(count($demandas) / $perPage), 
                ],
            ];            

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDemanda($cod)
    {
        $demanda = null;
        $url = $this->apiUrl;

        try {
            $response = $this->client->get($url. '?TIPO=1&INFO='. $cod, [
                'auth' => [
                    env('PROTHEUS_API_USER'),
                    env('PROTHEUS_API_PASSWORD')
                ]
            ]);

            $demanda = json_decode($response->getBody(), true);

            if ($demanda) {
                return response()->json($demanda);
            } else {
                return response()->json(['message' => 'Demanda não encontrada'], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'protheus_response' => [
                    'status_code' => $response->getStatusCode(),
                    'body' => $response->getBody()->getContents(),
                ]
            ], 500);
        }
    }

    public function createDemanda(Request $request)
    {
        $url = $this->apiUrl; 

        $objetoSimples = $request->all();
        
        try {
            $promise = $this->client->post($url, [
                'auth' => [
                    env('PROTHEUS_API_USER'),
                    env('PROTHEUS_API_PASSWORD')
                ],
                'json' => $objetoSimples,
                'headers' => [ 
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json', 
                ]
            ]);
            
            $response = json_decode($promise->getBody(), true);
            return $response;

        } catch (ServerException $e) {
            $response = $e->getResponse();
            $errorBody = $response->getBody();
            $statusCode = $response->getStatusCode();

            $errorData = null;
            try {
                $errorData = json_decode($errorBody, true);
            } catch (\Exception $ex) {
            }


            return response()->json(['error' => 'Erro ao comunicar com a API Protheus', 'details' => $errorData, 'status' => $statusCode], $statusCode); 

        } catch (\Exception $e) { 
            return response()->json(['error' => 'Erro geral: '. $e->getMessage()], 500);
        }
    }

    public function updateDemanda(Request $request, $cod)
    {
        $url = $this->apiUrl; 

        $objetoSimples = $request->all();
        
        try {
            $promise = $this->client->putAsync($url, [
                'auth' => [
                    env('PROTHEUS_API_USER'),
                    env('PROTHEUS_API_PASSWORD')
                ],
                'json' => $objetoSimples,
                'headers' => [ 
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json', 
                ]
            ]);
            
            $promise->then(
                function ($response) {
                },
                function ($exception) {
                }
            );
            
            $response = $promise->wait();

            $demanda = json_decode($response->getBody(), true);
            return $demanda;

        } catch (ServerException $e) {
            $response = $e->getResponse();
            $errorBody = $response->getBody();
            $statusCode = $response->getStatusCode();

            $errorData = null;
            try {
                $errorData = json_decode($errorBody, true);
            } catch (\Exception $ex) {
            }


            return response()->json(['error' => 'Erro ao comunicar com a API Protheus', 'details' => $errorData, 'status' => $statusCode], $statusCode); 

        } catch (\Exception $e) { 
            return response()->json(['error' => 'Erro geral: '. $e->getMessage()], 500);
        }
    }

    public function deleteDemanda($cod)
    {
        $url = $this->apiUrl;

        try {

            $response = $this->client->delete($url, [
                'auth' => [
                    env('PROTHEUS_API_USER'),
                    env('PROTHEUS_API_PASSWORD')
                ],
                'json' => $cod,
                'headers' => [ 
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json', 
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'protheus_response' => [
                    'status_code' => $response->getStatusCode(),
                    'body' => $response->getBody()->getContents(),
                ]
            ], 500);
        }
    }
}