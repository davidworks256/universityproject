@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">{{ __('Students') }}</h1>

                @can('manage students')
                    <a href="{{ route('students.create') }}"
                       class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                        {{ __('Add student') }}
                    </a>
                @endcan
            </div>

            <div class="mt-4 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('Student number') }}</th>
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="px-4 py-3">{{ __('Email') }}</th>
                            <th class="px-4 py-3">{{ __('Phone') }}</th>
                            <th class="px-4 py-3">{{ __('Courses') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($students as $student)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $student->student_number }}</td>
                                <td class="px-4 py-3">{{ $student->name }}</td>
                                <td class="px-4 py-3">{{ $student->email }}</td>
                                <td class="px-4 py-3">{{ $student->phone }}</td>
                                <td class="px-4 py-3 text-center">{{ $student->courses_count }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <a href="{{ route('students.show', $student) }}" class="text-indigo-600 hover:underline">
                                        {{ __('View') }}
                                    </a>

                                    @can('manage students')
                                        <a href="{{ route('students.edit', $student) }}" class="ms-3 text-indigo-600 hover:underline">
                                            {{ __('Edit') }}
                                        </a>

                                        <form method="POST" action="{{ route('students.destroy', $student) }}" class="inline"
                                              onsubmit="return confirm('Delete this student and their sign-in account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ms-3 text-red-600 hover:underline">{{ __('Delete') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $students->links() }}</div>
        </div>
    </div>
@endsection
