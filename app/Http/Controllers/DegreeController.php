<?php

namespace App\Http\Controllers;

use App\Models\Degree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Log;

class DegreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $degrees = Degree::orderBy('degree_name')->paginate(10);

        return view('portal_degree', [
            'degrees' => $degrees,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('portal_adddegree');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'degree_name' => 'required|string|max:255|unique:degrees,degree_name',
        ]);

        if ($validator->fails()) {
            return redirect()->route('degree.create')
                ->withErrors($validator)
                ->withInput();
        }

        Degree::create($validator->validated());

        $msg = 'Degree created successfully!';
        Log::info($msg);
        Log::Notice($msg);
        Log::alert($msg);
        Log::critical($msg);
        Log::emergency($msg);
        Log::warning($msg);
        Log::error($msg);

        return redirect()->route('degree.index')->with('message', $msg);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $degree = Degree::findOrFail($id);

        return view('portal_degree_details', [
            'degree' => $degree,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $degree = Degree::findOrFail($id);

        return view('portal_editdegree', [
            'degree' => $degree,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $degree = Degree::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'degree_name' => 'required|string|max:255|unique:degrees,degree_name,' . $degree->id,
        ]);

        if ($validator->fails()) {
            return redirect()->route('degree.edit', $degree->id)
                ->withErrors($validator)
                ->withInput();
        }

        $degree->fill($validator->validated());
        $degree->save();

        $msg = 'Degree updated successfully!';
        Log::info($msg);
        Log::Notice($msg);
        Log::alert($msg);
        Log::critical($msg);
        Log::emergency($msg);
        Log::warning($msg);
        Log::error($msg);

        return redirect()->route('degree.index')->with('message', $msg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Degree::destroy($id);

        return redirect()->route('degree.index');
    }
}
