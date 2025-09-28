<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Companies') }} {{ request()->input('archived') == 'true' ? '(Archived)' : '' }}
        </h2>
    </x-slot>

    <div class="overflow-x-auto p-6">
        <x-toast-notifiaction />


        <div class="flex justify-end items-center space-x-4">
            @if (request()->input('archived') == 'true')
                <!-- Active -->
                <div class="flex items-center">
                    <a href="{{ route('companies.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Active Company
                    </a>
                </div>
            @else
                <!-- Archived -->
                <div class="flex items-center">
                    <a href="{{ route('companies.index', ['archived' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Archived Company
                    </a>
                </div>
            @endif


            <!-- Add Company Buttons -->
            @if (request()->input('archived') == 'false' || !request()->input('archived'))
                <div class="flex items-center">
                    <a href="{{ route('companies.create') }}"
                        class="inline-flex items-center px-4 py-2 text-white rounded-md bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Add Company
                    </a>
                </div>
            @endif

        </div>

        <!-- Company Table -->
        <table class="min-w-full divide-y divide-gray-200 rounded-lg shadow-md mt-4 bg-white">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500">Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500">Address</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500">Industry</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500">Website</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($companies as $company)
                    <tr class="border-b">
                        <td class="px-6 py-4 text-gray-800">
                            @if (request()->input('archived') == 'true')
                                <span>{{ $company->name }}</span>
                            @else
                                <a class="text-blue-500 hover:text-blue-900 underline hover:no-underline"
                                    href="{{ route('companies.show', $company->id) }}">{{ $company->name }}</a>
                            @endif

                        </td>
                        <td class="px-6 py-4 text-gray-800">{{ $company->address }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $company->industry }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $company->website }}</td>
                        <td>
                            <div class="flex space-x-4">
                                @if (request()->input('archived') == 'true')
                                    <form action="{{ route('companies.restore', $company->id) }}" method="POST"
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
                                    <a href="{{ route('companies.edit', $company->id) }}"
                                        class="text-blue-500 hover:text-blue-700">Edit</a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('companies.destroy', $company->id) }}" method="POST"
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
                        <td colspan="2" class="px-6 py-4 text-gray-800">No companies found</td>
                    </tr>
                @endforelse

            </tbody>
        </table>

        <div class="mt-5">
            {{ $companies->links() }}
        </div>
    </div>
</x-app-layout>
