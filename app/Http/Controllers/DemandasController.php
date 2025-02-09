<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class DemandasController extends Controller
{

    public function getDemandas()
        {
            return response()->json([
                ['id' => 1, 'nome' => 'Demanda 1'],
                ['id' => 2, 'nome' => 'Demanda 2'],
            ]);
        }
}
