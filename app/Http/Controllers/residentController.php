<?php

namespace App\Http\Controllers;

use App\Models\barangay_residents;
use Illuminate\Http\Request;

class residentController extends Controller
{

    public function residentHome(){
        return view('residents.home');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(barangay_residents $resident)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(barangay_residents $resident)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, barangay_residents $resident)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(barangay_residents $resident)
    {
        //
    }
}
