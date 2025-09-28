<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobApplicationUpdateRequest;
use App\Models\JobApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Active
        $query = JobApplication::latest();
        if (auth()->user()->role == 'company-owner') {
            $query->whereHas('jobVacancy', function (Builder $query) {
                $query->where('companyId', auth()->user()->company->id);
            });
        }

        // Archived
        if ($request->input('archived') == 'true') {
            $query->onlyTrashed();
        }
        $jobApplications = $query->paginate(10)->onEachSide(1);

        return view('job-applications.index', compact('jobApplications'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jobApplication = JobApplication::findOrFail($id);
        return view('job-applications.show', compact('jobApplication'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jobApplication = JobApplication::findOrFail($id);

        $statuses = ['pending', 'accepted', 'rejected'];
        return view('job-applications.edit', compact('jobApplication', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobApplicationUpdateRequest $request, string $id)
    {
        $jobApplication = JobApplication::findOrFail($id);
        $jobApplication->update([
            'status' => $request->input('status'),
        ]);

        if ($request->query('redirectToList' == 'false')) {
            return redirect()->route('job-applications.show', $id)->with('success', 'Applicant status updated successfully');;
        } else {
            return redirect()->route('job-applications.index')->with('success', 'Applicant status updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jobApplication = JobApplication::findOrFail($id);
        $jobApplication->delete();
        return redirect()->route('job-applications.index')
            ->with('success', 'Job Application deleted successfully');
    }

    public function restore(string $id)
    {
        $jobApplication = JobApplication::withTrashed()->findOrFail($id);
        $jobApplication->restore();
        return redirect()->route('job-applications.index', ['archived' => 'true'])->with('success', 'Job Application restored successfully');
    }
}
