@php
    $role = auth()->user()->role ?? null;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Job Vacancy') }}
        </h2>
    </x-slot>

    <div class="overflow-x-auto p-6">

        <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md">

            <form
                action="{{ route('job-vacancies.update', ['job_vacancy' => $jobVacancy->id, 'redirectToList' => request()->query('redirectToList')]) }}"
                method="POST">
                @csrf
                @method('PUT')

                <!-- JobVacancy Details -->
                <div class="mb-4 p-6 bg-gray-50 border border-gray-100 rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold">Job Vacancy Details</h3>
                    <p class="text-sm mb-4">Enter the job vacancy details</p>

                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" id="title"
                            value="{{ old('title', $jobVacancy->title) }}"
                            class="@error('title') outline-red-500 outline outline-1  @enderror mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" name="location" id="location"
                            value="{{ old('location', $jobVacancy->location) }}"
                            class="@error('location') outline-red-500 outline outline-1  @enderror mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('location')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    <div class="mb-4">
                        <label for="salary" class="block text-sm font-medium text-gray-700">Expected Salary
                            (USD)</label>
                        <input type="number" name="salary" id="salary"
                            value="{{ old('salary', $jobVacancy->salary) }}"
                            class="@error('salary') outline-red-500 outline outline-1  @enderror mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('salary')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type"
                            class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach ($types as $type)
                                <option value="{{ $type }}"
                                    {{ old('type', $jobVacancy->type) == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach

                        </select>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Company Select Dropdown --}}
                    {{-- <div class="mb-4">
                        <label for="companyId" class="block text-sm font-medium text-gray-700">Company</label>
                        <select name="companyId" id="companyId"
                            class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}"
                                    {{ old('companyId', $jobVacancy->companyId) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('companyId')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div> --}}
                    {{-- Company (Admin only) --}}
                    @if ($role === 'admin')
                        <div class="mb-4">
                            <label for="companyId" class="block text-sm font-medium text-gray-700">Company</label>
                            <select name="companyId" id="companyId"
                                class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ (int) old('companyId', $jobVacancy->companyId) === (int) $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('companyId')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @elseif ($role === 'company-owner')
                        {{-- لا نرسل companyId أبداً (prohibited في الفاليديشن) --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Company</label>
                            <input type="text" class="block w-full rounded-md bg-gray-100 border-gray-200 sm:text-sm"
                                value="{{ $company->name ?? '' }}" disabled>
                            <p class="mt-1 text-xs text-gray-500">لا يمكن تغيير الشركة من حساب المالك.</p>
                        </div>
                    @endif

                    {{-- Job Category Select Dropdown --}}
                    <div class="mb-4">
                        <label for="jobCategoryId" class="block text-sm font-medium text-gray-700">job Category</label>
                        <select name="jobCategoryId" id="jobCategoryId"
                            class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach ($jobCategories as $jobCategory)
                                <option value="{{ $jobCategory->id }}"
                                    {{ old('jobCategoryId', $jobVacancy->jobCategoryId) == $jobCategory->id ? 'selected' : '' }}>
                                    {{ $jobCategory->name }}
                                </option>
                            @endforeach

                        </select>
                        @error('jobCategoryId')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Job Description --}}
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">job Description</label>
                        <textarea name="description" id="description" rows="4"
                            class="@error('salary') outline-red-500 outline outline-1 @enderror mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $jobVacancy->description) }}
                        </textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>



                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('job-vacancies.index') }}"
                            class="px-4 py-2 rounded-md text-gray-500 hover:text-gray-700">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2">
                            Update Job Vacancy
                        </button>
                    </div>


                </div>
            </form>
        </div>
    </div>
</x-app-layout>
