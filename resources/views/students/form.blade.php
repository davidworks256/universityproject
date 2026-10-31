@csrf

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <div>
        <x-input-label for="student_number" :value="__('Student number')" />
        <x-text-input id="student_number" name="student_number" type="text" class="mt-1 block w-full"
                      :value="old('student_number', $student?->student_number)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('student_number')" />
    </div>

    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                      :value="old('name', $student?->name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                      :value="old('email', $student?->email)" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
        <p class="mt-1 text-xs text-gray-500">{{ __('Also used as the sign-in address.') }}</p>
    </div>

    <div>
        <x-input-label for="phone" :value="__('Phone')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                      :value="old('phone', $student?->phone)" />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>
</div>

<div class="mt-6 flex items-center gap-4">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>

    <a href="{{ route('students.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Cancel') }}</a>
</div>
