@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Plans tarifaires</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($plans as $plan)
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col">
                <h2 class="text-2xl font-bold mb-4">{{ $plan['name'] }}</h2>
                <p class="text-gray-600 mb-4">{{ $plan['description'] }}</p>
                
                <div class="text-3xl font-bold mb-6">
                    {{ number_format($plan['price'], 2) }} €
                    <span class="text-sm font-normal text-gray-600">/mois</span>
                </div>

                <ul class="mb-8 flex-grow">
                    @foreach ($plan['features'] as $feature)
                        <li class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>

                @if (isset($currentSubscription) && $currentSubscription->plan_id === $plan['id'])
                    <button disabled class="w-full bg-gray-300 text-gray-700 py-2 px-4 rounded-lg">
                        Plan actuel
                    </button>
                @else
                    <form action="{{ route('subscription.create') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan['id'] }}">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition duration-200">
                            Souscrire
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection