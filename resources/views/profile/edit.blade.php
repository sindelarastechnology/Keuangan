<x-app-layout>
    <x-slot name="title">Profil</x-slot>

    <x-page-header title="Profil" subtitle="Kelola informasi dan keamanan akun Anda" />

    <div class="space-y-6">
        <x-card>
            @include('profile.partials.update-profile-information-form')
        </x-card>

        <x-card>
            @include('profile.partials.update-password-form')
        </x-card>

        <x-card>
            @include('profile.partials.delete-user-form')
        </x-card>
    </div>
</x-app-layout>