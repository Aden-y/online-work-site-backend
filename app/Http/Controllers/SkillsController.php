<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Skill;

class SkillsController extends Controller
{
    public function index()
    {
        $skills = Skill::all();
        $_skills = [];
        foreach($skills as $skill){
            array_push($_skills,$skill->name);
        }
        return \response()->json(['skills' => $_skills]);
    }
}
