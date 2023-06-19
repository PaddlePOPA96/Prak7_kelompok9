<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
// use App\Http\Controllers\DB;



class EmployeeController extends Controller
{
    public function edit(string $id)
    {
        $pageTitle = 'Edit Employee';

        // ELOQUENT
        $positions = Position::all();
        $employee = Employee::find($id);

        return view('employee.edit', compact('pageTitle', 'positions', 'employee'));
    }

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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // ELOQUENT
        $employee = Employee::find($id);
        $employee->firstname = $request->firstName;
        $employee->lastname = $request->lastName;
        $employee->email = $request->email;
        $employee->age = $request->age;
        $employee->position_id = $request->position;
        $employee->save();

        return redirect()->route('employees.index');
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        // ELOQUENT
        Employee::find($id)->delete();

        return redirect()->route('employees.index');
    }


}
