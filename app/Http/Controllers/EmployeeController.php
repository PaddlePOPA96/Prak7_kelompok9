<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Employee List';

        // RAW SQL QUERY
        $employees = DB::select('
    select *, employees.id as employee_id, positions.name as position_name
    from employees
    left join positions on employees.position_id = positions.id
');


        return view('employee.index', [
            'pageTitle' => $pageTitle,
            'employees' => $employees
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Employee';
        // RAW SQL Query
        $positions = DB::select('select * from positions');

        return view('employee.create', compact('pageTitle', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka'
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // INSERT QUERY
        DB::table('employees')->insert([
            'firstname' => $request->firstName,
            'lastname' => $request->lastName,
            'email' => $request->email,
            'age' => $request->age,
            'position_id' => $request->position,
        ]);

        return redirect()->route('employees.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pageTitle = 'Employee Detail';

        // RAW SQL QUERY
        $employee = collect(DB::select('
        select *, employees.id as employee_id, positions.name as position_name
        from employees
        left join positions on employees.position_id = positions.id
        where employees.id = ?
    ', [$id]))->first();

        return view('employee.show', compact('pageTitle', 'employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $pageTitle = 'Edit Employee';

        // Query Builder
        $employee = DB::table('employees')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
            ->select('employees.*', 'employees.id as employee_id', 'positions.name as position_name')
            ->where('employees.id', '=', $id)
            ->first();

        $positions = DB::table('positions')->get();

        return view('employee.edit', compact('pageTitle', 'employee', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka'
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        DB::table('employees')
            ->where('id', $id)->update([
                'firstname' => $request->firstName,
                'lastname' => $request->lastName,
                'email' => $request->email,
                'age' => $request->age,
                'position_id' => $request->position,
            ]);

        return redirect()->route('employees.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // QUERY BUILDER
        DB::table('employees')
            ->where('id', $id)
            ->delete();

        return redirect()->route('employees.index');
    }
}
