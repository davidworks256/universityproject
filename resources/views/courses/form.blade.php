@csrf

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <div>
        <x-input-label for="code" :value="__('Code')" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
                      :value="old('code', $course?->code)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('code')" />
    </div>

    <div>
        <x-input-label for="credits" :value="__('Credits')" />
        <x-text-input id="credits" name="credits" type="number" min="1" max="10" step="1" class="mt-1 block w-full"
                      :value="old('credits', $course?->credits)" required />
        <x-input-error class="mt-2" :messages="$errors->get('credits')" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                      :value="old('title', $course?->title)" required />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="lecturer_id" :value="__('Lecturer')" />
        <select id="lecturer_id" name="lecturer_id" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-400 focus:ring-gray-400">
            <option value="">{{ __('Choose a lecturer') }}</option>

            @foreach ($lecturers as $lecturer)
                <option value="{{ $lecturer->id }}" @selected((int) old('lecturer_id', $course?->lecturer_id) === $lecturer->id)>
                    {{ $lecturer->name }} ({{ $lecturer->email }})
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('lecturer_id')" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-400 focus:ring-gray-400">{{ old('description', $course?->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>
</div>

<div class="mt-6 flex items-center gap-4">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>

    <a href="{{ route('courses.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Cancel') }}</a>
</div>
