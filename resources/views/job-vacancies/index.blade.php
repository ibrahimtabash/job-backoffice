<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job Vacancies') }} {{ request()->input('archived') == 'true' ? '(Archived)' : '' }}
        </h2>
    </x-slot>

    <div class="overflow-x-auto p-6">
        <x-toast-notifiaction />


        <div class="flex justify-end items-center space-x-4">
            @if (request()->input('archived') == 'true')
                <!-- Active -->
                <div class="flex items-center">
                    <a href="{{ route('job-vacancies.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Active Job Vacancies
                    </a>
                </div>
            @else
                <!-- Archived -->
                <div class="flex items-center">
                    <a href="{{ route('job-vacancies.index', ['archived' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Archived Job Vacancies
                    </a>
                </div>
            @endif


            <!-- Add Company Buttons -->
            @if (request()->input('archived') == 'false' || !request()->input('archived'))
                <div class="flex items-center">
                    <a href="{{ route('job-vacancies.create') }}"
                        class="inline-flex items-center px-4 py-2 text-white rounded-md bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Add Job Vacancy
                    </a>
                </div>
            @endif

        </div>

        <!-- Company Table -->
        <table class="min-w-full divide-y divide-gray-200 rounded-lg shadow-md mt-4 bg-white">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500">Title</th>
                    @if (auth()->user()->role == 'admin')
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500">Company</th>
                    @endif
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500">Location</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500">Type</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500">Salary</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jobVacancies as $jobVacancy)
                    <tr class="border-b">
                        <td class="px-6 py-4 text-gray-800">
                            @if (request()->input('archived') == 'true')
                                <span>{{ $jobVacancy->title }}</span>
                            @else
                                <a class="text-blue-500 hover:text-blue-900 underline hover:no-underline"
                                    href="{{ route('job-vacancies.show', $jobVacancy->id) }}">{{ $jobVacancy->title }}</a>
                            @endif

                        </td>
                        @if (auth()->user()->role == 'admin')
                            <td class="px-6 py-4 text-gray-800">{{ $jobVacancy->company->name }}</td>
                        @endif
                        <td class="px-6 py-4 text-gray-800">{{ $jobVacancy->location }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $jobVacancy->type }}</td>
                        <td class="px-6 py-4 text-gray-800">$ {{ number_format($jobVacancy->salary, 2) }}</td>
                        <td>
                            <div class="flex space-x-4">
                                @if (request()->input('archived') == 'true')
                                    <form action="{{ route('job-vacancies.restore', $jobVacancy->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="flex justify-center items-center gap-2 text-green-500 hover:text-green-700 px-4 py-2 rounded-md">
                                            <i class="fa-solid fa-repeat"></i>
                                            <span>Restore</span>

                                        </button>
                                    </form>
                                @else
                                    <!-- Edit Button -->
                                    <a href="{{ route('job-vacancies.edit', $jobVacancy->id) }}"
                                        class="text-blue-500 hover:text-blue-700">Edit</a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('job-vacancies.destroy', $jobVacancy->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            Archive
                                        </button>
                                    </form>
                                @endif

                            </div>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-gray-800">No job vacancies found</td>
                    </tr>
                @endforelse

            </tbody>
        </table>

        <div class="mt-5">
            {{ $jobVacancies->links() }}
        </div>
    </div>
</x-app-layout>
