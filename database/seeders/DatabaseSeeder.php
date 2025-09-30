<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use App\Models\Resume;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Seed the root admin
        User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'admin',
            'password' => Hash::make('123456789'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // seed Data to test with
        $jobData = json_decode(file_get_contents(database_path('data/job-data.json')), true);
        $jobApplications = json_decode(file_get_contents(database_path('data/job-applications.json')), true);

        // Create Job Categories
        foreach ($jobData['jobCategories'] as $category) {
            JobCategory::firstOrCreate([
                'name' => $category,
            ]);
        }

        // Create Companies
        foreach ($jobData['companies'] as $company) {
            // create company owner
            $companyOwner = User::firstOrCreate([
                'email' => $faker->unique()->safeEmail(),
            ], [
                'name' => $faker->name(),
                'password' => Hash::make('12345678'),
                'role' => 'company-owner',
                'email_verified_at' => now(),
            ]);

            Company::firstOrCreate([
                'name' => $company['name'],
            ], [
                'address' => $company['address'],
                'industry' => $company['industry'],
                'website' => $company['website'],
                'ownerId' => $companyOwner->id
            ]);
        }

        // create Job Vacancies
        foreach ($jobData['jobVacancies'] as $job) {
            // Get The created company
            $company = Company::where('name', $job['company'])->firstOrFail();

            // Get the created job category
            $jobCategory = JobCategory::where('name', $job['category'])->firstOrFail();

            JobVacancy::firstOrCreate([
                'title' => $job['title'],
                'companyId' => $company->id
            ], [
                'description' => $job['description'],
                'location' => $job['location'],
                'type' => $job['type'],
                'salary' => $job['salary'],
                'jobCategoryId' => $jobCategory->id,
            ]);
        }

        // Create Job Applications
        foreach ($jobApplications['jobApplications'] as $application) {
            // Get random job vacancy
            $jobVacancy = JobVacancy::inRandomOrder()->first();

            // create applicant (job-seeker)
            $applicant = User::firstOrCreate([
                'email' => $faker->unique()->safeEmail(),
            ], [
                'name' => $faker->name(),
                'password' => Hash::make('12345678'),
                'role' => 'job-seeker',
                'email_verified_at' => now(),
            ]);

            // Create resume
            $resume = Resume::create([
                'userId' => $applicant->id,
                'filename' => $application['resume']['filename'],
                'fileUri' => $application['resume']['fileUri'],
                'contactDetails' => $application['resume']['contactDetails'],
                'summary' => $application['resume']['summary'],
                'skills' => $application['resume']['skills'],
                'experience' => $application['resume']['experience'],
                'education' => $application['resume']['education'],
            ]);

            // Create job application
            JobApplication::create([
                'jobVacancyId' => $jobVacancy->id,
                'userId' => $applicant->id,
                'resumeId' => $resume->id,
                'status' => $application['status'],
                'aiGeneratedScore' => $application['aiGeneratedScore'],
                'aiGeneratedFeedback' => $application['aiGeneratedFeedback'],
            ]);
        }
    }
}
