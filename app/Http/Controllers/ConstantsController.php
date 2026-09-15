<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConstantSkillRequest;
use App\Models\ConstantSkill;

class ConstantsController extends Controller
{
    // List constants (grouped) + the group options for the form.
    public function index()
    {
        $constants = ConstantSkill::orderBy('group')->orderBy('name')->get()->groupBy('group');
        $groups = array_keys(config('resume.skill_pool')); // suggested groups

        return view('tailor.constants', compact('constants', 'groups'));
    }

    // Add a constant skill.
    public function store(ConstantSkillRequest $request)
    {
        ConstantSkill::create($request->validated());

        return back()->with('status', 'Constant skill added.');
    }

    // Remove a constant skill.
    public function destroy(ConstantSkill $constantSkill)
    {
        $constantSkill->delete();

        return back()->with('status', 'Constant skill removed.');
    }
}
