<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div class="mb-4 text-green-600 bg-green-100 p-2 rounded">
                    {{ session('message') }}
                </div>
            @endif

            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.courses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Course
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Free Limit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($courses as $course)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $course->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $course->is_active ? 'Active' : 'Inactive' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $course->free_question_limit }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.courses.edit', $course) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <button wire:click="delete({{ $course->id }})" wire:confirm="Are you sure?" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
