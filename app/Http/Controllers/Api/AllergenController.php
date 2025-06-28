<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\allergen;
use App\Models\reps\AllergenReposity;

class AllergenController extends Controller
{
     protected $allergenRep;

    public function __construct()
    {
        $this->allergenRep = new AllergenReposity();
    }

    public function index()
    {
        return response()->json($this->allergenRep->getAll());
    }
}
