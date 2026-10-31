@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('Register for a course') }}</h1>

            @if ($ownStudent !== null && $students === null)
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Signing in as :name (:number).', [
                        'name' => $ownStudent->name,
                        'number' => $ownStudent->student_number,
                    ]) }}
                </p>
            @endif

            @if ($students === null && $ownStudent === null)
                <p class="mt-4 bg-white px-4 py-6 text-center text-sm text-gray-500 shadow-sm sm:rounded-lg">
                    {{ __('This account has no student record yet, so an administrator must create one first.') }}
                </p>
            @elseif ($courses->isEmpty())
                <p class="mt-4 bg-white px-4 py-6 text-center text-sm text-gray-500 shadow-sm sm:rounded-lg">
                    {{ __('There are no courses left to register for.') }}
                </p>
            @else
                <form method="POST" action="{{ route('registrations.store') }}" class="mt-6 bg-white p-6 shadow-sm sm:rounded-lg">
                    @csrf

                    @if ($students !== null)
                        <div>
                            <x-input-label for="student_id" :value="__('Student')" />
                            <select id="student_id" name="student_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-400 focus:ring-gray-400">
                                <option value="">{{ __('Choose a student') }}</option>

                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected((int) old('student_id') === $student->id)>
                                        {{ $student->student_number }} &mdash; {{ $student->name }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error class="mt-2" :messages="$errors->get('student_id')" />
                        </div>
                    @endif

                    <div class="mt-6">
                        <x-input-label :value="__('Course')" />

                        <div class="mt-2 space-y-2">
                            @foreach ($courses as $course)
                                <label class="flex items-start gap-3 rounded-md border border-gray-200 p-3 text-sm hover:bg-gray-50">
                                    <input type="radio" name="course_id" value="{{ $course->id }}" class="mt-1"
                                           @checked((int) old('course_id') === $course->id)>

                                    <span>
                                        <span class="font-medium text-gray-900">{{ $course->code }}</span>
                                        &mdash; {{ $course->title }}

                                        <span class="block text-xs text-gray-500">
                                            {{ $course->lecturer?->name }}
                                            &middot; {{ __(':count credits', ['count' => $course->credits]) }}
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <x-input-error class="mt-2" :messages="$errors->get('course_id')" />
                    </div>

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>{{ __('Register') }}</x-primary-button>

                        <a href="{{ route('registrations.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Cancel') }}</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
