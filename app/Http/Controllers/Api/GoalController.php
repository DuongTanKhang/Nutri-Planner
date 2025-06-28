<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\reps\GoalRepository;


class GoalController extends Controller
{
    protected $goalRep;

    public function __construct()
    {
        $this->goalRep = new GoalRepository();
    }

    public function index()
    {
        return response()->json($this->goalRep->getAll());
    }

}
