<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request; // Importe a classe Request
use Illuminate\Support\Collection; 

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
            $perPage = 10;
            $page = $request->input('page', 1);

            $url = $this->apiUrl;

            $response = $this->client->get($url . '?TIPO=2', [
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

            $items = array_slice($newDemandas, $offset, $perPage);

            $response = [
                'data' => $items,
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
}