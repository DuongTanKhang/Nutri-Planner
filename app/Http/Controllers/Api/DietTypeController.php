<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\allergen;
use App\Models\reps\DietTypeRepository;

class DietTypeController extends Controller
{
    protected $dietTypeRep;

    public function __construct()
    {
        $this->dietTypeRep = new DietTypeRepository();
    }

    public function index()
    {
        return response()->json($this->dietTypeRep->getAll());
    }
}
