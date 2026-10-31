{{-- The mobile menu stacks its links, so it uses the responsive variant of the same link component. --}}
@php
    $navLinkComponent = ($variant ?? 'desktop') === 'mobile' ? 'responsive-nav-link' : 'nav-link';
@endphp

@can('manage students')
    <x-dynamic-component :component="$navLinkComponent" :href="route('students.index')" :active="request()->routeIs('students.*')">
        {{ __('Students') }}
    </x-dynamic-component>
@endcan

@can('manage courses')
    <x-dynamic-component :component="$navLinkComponent" :href="route('courses.index')" :active="request()->routeIs('courses.*')">
        {{ __('Courses') }}
    </x-dynamic-component>
@endcan

@can('manage registrations')
    <x-dynamic-component :component="$navLinkComponent" :href="route('registrations.index')" :active="request()->routeIs('registrations.*')">
        {{ __('Registrations') }}
    </x-dynamic-component>
@endcan

@role('lecturer')
    <x-dynamic-component :component="$navLinkComponent" :href="route('courses.index')" :active="request()->routeIs('courses.*')">
        {{ __('My Courses') }}
    </x-dynamic-component>
@endrole

@role('student')
    <x-dynamic-component :component="$navLinkComponent" :href="route('courses.index')" :active="request()->routeIs('courses.*')">
        {{ __('Available Courses') }}
    </x-dynamic-component>

    <x-dynamic-component :component="$navLinkComponent" :href="route('registrations.index')" :active="request()->routeIs('registrations.*')">
        {{ __('My Registrations') }}
    </x-dynamic-component>

    {{-- A student may open their own record, but only once an administrator has created it. --}}
    @php($ownRecord = auth()->user()->student)
    @if ($ownRecord)
        <x-dynamic-component :component="$navLinkComponent" :href="route('students.show', $ownRecord)" :active="request()->routeIs('students.show')">
            {{ __('My Record') }}
        </x-dynamic-component>
    @endif
@endrole
