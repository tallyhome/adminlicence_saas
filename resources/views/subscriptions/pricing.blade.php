@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center mb-12">Nos Plans & Tarifs</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($plans as $plan)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                <!-- En-tête du plan -->
                <div class="p-6 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
                    <h2 class="text-2xl font-bold mb-2">{{ $plan->name }}</h2>
                    <div class="text-3xl font-bold mb-2">
                        {{ number_format($plan->price, 2) }}€
                        <span class="text-sm font-normal">/mois</span>
                    </div>
                    <p class="text-blue-100">{{ $plan->description }}</p>
                </div>

                <!-- Caractéristiques du plan -->
                <div class="p-6">
                    <ul class="space-y-4">
                        @foreach(json_decode($plan->features) as $feature)
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Bouton de souscription -->
                <div class="p-6 bg-gray-50">
                    @if(isset($currentSubscription) && $currentSubscription->plan_id === $plan->id)
                        <button disabled class="w-full bg-gray-400 text-white py-3 px-4 rounded-lg font-semibold">
                            Plan actuel
                        </button>
                    @else
                        <form action="{{ route('subscription.create') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold transition duration-200">
                                Souscrire
                            </button>
                        </form>
                    @endif
                </div>

                @if($plan->trial_days > 0)
                    <div class="px-6 pb-6 text-center text-sm text-gray-600">
                        Essai gratuit de {{ $plan->trial_days }} jours
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Section FAQ -->
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-center mb-8">Questions fréquentes</h2>
        <div class="max-w-3xl mx-auto space-y-4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold mb-2">Comment fonctionne la période d'essai ?</h3>
                <p class="text-gray-600">La période d'essai vous permet de tester toutes les fonctionnalités du plan choisi gratuitement. À la fin de la période d'essai, votre carte sera débitée automatiquement si vous ne résiliez pas votre abonnement.</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold mb-2">Puis-je changer de plan à tout moment ?</h3>
                <p class="text-gray-600">Oui, vous pouvez passer à un plan supérieur ou inférieur à tout moment. La différence de prix sera calculée au prorata de la période restante.</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold mb-2">Comment puis-je annuler mon abonnement ?</h3>
                <p class="text-gray-600">Vous pouvez annuler votre abonnement à tout moment depuis votre espace client. L'accès aux services reste actif jusqu'à la fin de la période en cours.</p>
            </div>
        </div>
    </div>
</div>
@endsection