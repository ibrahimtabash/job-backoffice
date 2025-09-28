<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobVacancyCreateRequest;
use App\Http\Requests\JobVacancyUpdateRequest;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use Illuminate\Http\Request;

class JobVacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Active
        $query = JobVacancy::latest();

        if (auth()->user()->role == 'company-owner') {
            $query->where('companyId', auth()->user()->company->id);
        }

        // Archived
        if ($request->input('archived') == 'true') {
            $query->onlyTrashed();
        }
        $jobVacancies = $query->paginate(10)->onEachSide(1);

        return view('job-vacancies.index', compact('jobVacancies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jobVacancy = JobVacancy::all();
        $types = ['Full-Time', 'Contract', 'Remote', 'Hybrid'];
        $companies = Company::all();
        $jobCategories = JobCategory::all();
        return view('job-vacancies.create', compact('jobVacancy', 'types', 'companies', 'jobCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobVacancyCreateRequest $request)
    {
        $validated = $request->validated();
        JobVacancy::create($validated);
        return redirect()->route('job-vacancies.index')->with('success', 'Job vacancy created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);
        return view('job-vacancies.show', compact('jobVacancy'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);
        $types = ['Full-Time', 'Contract', 'Remote', 'Hybrid'];
        $companies = Company::all();
        $jobCategories = JobCategory::all();
        return view('job-vacancies.edit', compact('jobVacancy', 'types', 'companies', 'jobCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobVacancyUpdateRequest $request, string $id)
    {
        $validated = $request->validated();
        $jobVacancy = JobVacancy::findOrFail($id);
        $jobVacancy->update($validated);

        if ($request->query('redirectToList') == 'false') {
            return redirect()->route('job-vacancies.show', $id)->with('success', 'Job vacancy updated successfully');
        }
        return redirect()->route('job-vacancies.index')->with('success', 'Job vacancy updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $jobVacancy = JobVacancy::findOrFail($id);
        $jobVacancy->delete();
        return redirect()->route('job-vacancies.index')->with('success', 'Job vacancy archived successfully!');
    }

    public function restore(string $id)
    {
        $jobVacancy = JobVacancy::onlyTrashed()->findOrFail($id);
        $jobVacancy->restore();
        return redirect()->route('job-vacancies.index', ['archived' => 'true'])
            ->with('success', 'The Job vacancy restored successfully!');
    }
}
