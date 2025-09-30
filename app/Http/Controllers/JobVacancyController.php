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
        $types = ['Full-Time', 'Contract', 'Remote', 'Hybrid'];
        $jobCategories = JobCategory::all();

        $jobVacancy = new JobVacancy();

        $user = auth()->user();

        if ($user->role === 'admin') {
            $companies = Company::orderBy('name')->get();
            return view('job-vacancies.create', compact('jobVacancy', 'types', 'companies', 'jobCategories'));
        }
        if ($user->role === 'company-owner') {
            $company = Company::where('ownerId', $user->id)->firstOrFail();
            return view('job-vacancies.create', compact('jobVacancy', 'types', 'company', 'jobCategories'));
        }

        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobVacancyCreateRequest $request)
    {
        $validated = $request->validated();

        $user = auth()->user();
        if ($user->role === 'company-owner') {
            $company = Company::where('ownerId', $user->id)->firstOrFail();
            $validated['companyId'] = $company->id;
        }
        JobVacancy::create($validated);
        return redirect()
            ->route('job-vacancies.index')
            ->with('success', 'Job vacancy created successfully');
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


        $user = auth()->user();

        if ($user->role === 'admin') {
            $jobVacancy = JobVacancy::findOrFail($id);
            $companies = Company::orderBy('name')->get();

            return view('job-vacancies.edit', compact('jobVacancy', 'types', 'companies', 'jobCategories'));
        }

        if ($user->role === 'company-owner') {
            $company = Company::where('ownerId', $user->id)->firstOrFail();
            $jobVacancy = JobVacancy::where('companyId', $company->id)->findOrFail($id);

            // ملاحظة: ما منرسل قائمة شركات للمالك
            return view('job-vacancies.edit', [
                'jobVacancy'   => $jobVacancy,
                'types'        => $types,
                'company'      => $company,
                'jobCategories' => $jobCategories,
            ]);
        }
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobVacancyUpdateRequest $request, string $id)
    {
        $validated = $request->validated();
        $jobVacancy = JobVacancy::findOrFail($id);
        $jobVacancy->update($validated);

        $user = auth()->user();

        if ($user->role === 'admin') {
            // الأدمن يقدر يغيّر companyId (حسب قواعد الفاليديشن)
            $jobVacancy = JobVacancy::findOrFail($id);
        } elseif ($user->role === 'company-owner') {
            // المالك: لا يسمح بتغيير الشركة + تحرّي الملكية
            $company = Company::where('ownerId', $user->id)->firstOrFail();
            $jobVacancy = JobVacancy::where('companyId', $company->id)->findOrFail($id);

            // تجاهل أي companyId قادم من الفورم وثبّت شركة المالك
            unset($validated['companyId']);
            $validated['companyId'] = $company->id;
        } else {
            abort(403);
        }

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
