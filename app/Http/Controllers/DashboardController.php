<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 'admin') {
            $analytics = $this->adminDashboard();
        } else {
            $analytics = $this->companyOwnerDashboard();
        }

        return view('dashboard.index', compact('analytics'));
    }

    private function adminDashboard()
    {
        // Last 30 days active users (job-seeker role)
        $activeUsers = User::where('last_login_at', '>=', now()->subDays(30))
            ->where('role', 'job-seeker')
            ->count();

        // Total Jobs (not deleted)
        $totalJobs = JobVacancy::whereNull('deleted_at')->count();

        // Total applications (not deleted)
        $totalApplications = JobApplication::whereNull('deleted_at')->count();


        $mostAppliedJobs = JobVacancy::withCount('jobApplications as totalCount')
            ->whereNull('deleted_at')
            ->orderByDesc('totalCount')
            ->limit(5)
            ->get();



        // Conversion Rates
        $conversionRates = JobVacancy::withCount('jobApplications as totalCount')
            ->having('totalCount', '>', 0)
            ->orderByDesc('totalCount')
            ->limit(5)
            ->get()
            ->map(function ($job) {
                if ($job->viewCount > 0) {
                    $job->conversionRate = $job->totalCount / $job->viewCount * 100;
                } else {
                    $job->conversionRate = 0;
                }
                return $job;
            });

        $analytics = [
            'activeUsers' => $activeUsers,
            'totalJobs' => $totalJobs,
            'totalApplications' => $totalApplications,
            'mostAppliedJobs' => $mostAppliedJobs,
            'conversionRates' => $conversionRates,
        ];

        return $analytics;
    }

    private function companyOwnerDashboard(): array
    {
        $company = auth()->user()->company;

        // filter active users by applying to jobs of the company
        $activeUsers = User::where('last_login_at', '>=', now()->subDays(30))
            ->where('role', 'job-seeker')
            ->whereHas('jobApplications', function (Builder $query) use ($company) {
                $query->whereIn('jobVacancyId', $company->jobVacancies->pluck('id'));
            })->count();

        // Total jobs of the company
        $totalJobs = $company->jobVacancies->count();

        // Total applications of the company
        $totalApplications = JobApplication::whereIn('jobVacancyId', $company->jobVacancies->pluck('id'))->count();

        // most applied jobs of the company
        $mostAppliedJobs = JobVacancy::withCount('jobApplications as totalCount')
            ->whereIn('id', $company->jobVacancies->pluck('id'))
            ->limit(5)
            ->orderByDesc('totalCount')
            ->get();



        // conversion rates of the company

        $conversionRates = JobVacancy::withCount('jobApplications as totalCount')
            ->whereIn('id', $company->jobVacancies->pluck('id'))
            ->having('totalCount', '>', 0)
            ->orderByDesc('totalCount')
            ->limit(5)
            ->get()
            ->map(function ($job) {
                if ($job->viewCount > 0) {
                    $job->conversionRate = $job->totalCount / $job->viewCount * 100;
                } else {
                    $job->conversionRate = 0;
                }
                return $job;
            });


        $analytics = [
            'activeUsers' => $activeUsers,
            'totalJobs' => $totalJobs,
            'totalApplications' => $totalApplications,
            'mostAppliedJobs' => $mostAppliedJobs,
            'conversionRates' => $conversionRates,
        ];
        return $analytics;
    }
}
