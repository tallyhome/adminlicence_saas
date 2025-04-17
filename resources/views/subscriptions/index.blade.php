@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-4">Mon abonnement</h1>
        
        @if($subscription)
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-semibold mb-2">{{ $subscription->plan->name }}</h2>
                        <p class="text-gray-600">{{ $subscription->plan->description }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold">{{ number_format($subscription->renewal_price, 2) }} €/{{ $subscription->billing_cycle }}</p>
                        <p class="text-sm text-gray-600">Prochain renouvellement : {{ $subscription->ends_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    @if($subscription->auto_renew)
                        <button type="button" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded transition duration-200" data-bs-toggle="modal" data-bs-target="#cancelRenewalModal">
                            Annuler le renouvellement automatique
                        </button>
                        
                        <!-- Modal de confirmation pour l'annulation du renouvellement -->
                        @component('components.confirmation-modal')
                            @slot('id', 'cancelRenewalModal')
                            @slot('title', 'Confirmer l\'annulation')
                            @slot('message')
                                Êtes-vous sûr de vouloir annuler le renouvellement automatique de votre abonnement ? 
                                <br><br>
                                Votre abonnement restera actif jusqu'à la fin de la période en cours, mais ne sera pas renouvelé automatiquement.
                            @endslot
                            @slot('formAction', route('subscription.cancel-renewal'))
                            @slot('confirmButtonText', 'Annuler le renouvellement')
                            @slot('confirmButtonClass', 'btn-danger')
                        @endcomponent
                    @else
                        <button type="button" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded transition duration-200" data-bs-toggle="modal" data-bs-target="#resumeRenewalModal">
                            Activer le renouvellement automatique
                        </button>
                        
                        <!-- Modal de confirmation pour l'activation du renouvellement -->
                        @component('components.confirmation-modal')
                            @slot('id', 'resumeRenewalModal')
                            @slot('title', 'Confirmer l\'activation')
                            @slot('message')
                                Êtes-vous sûr de vouloir activer le renouvellement automatique de votre abonnement ? 
                                <br><br>
                                Votre abonnement sera automatiquement renouvelé à la fin de la période en cours.
                            @endslot
                            @slot('formAction', route('subscription.resume'))
                            @slot('confirmButtonText', 'Activer le renouvellement')
                            @slot('confirmButtonClass', 'btn-success')
                        @endcomponent
                    @endif
                    
                    <a href="{{ route('subscription.plans') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition duration-200">
                        Changer de plan
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-6 mb-8 text-center">
                <p class="text-gray-600 mb-4">Vous n'avez pas d'abonnement actif.</p>
                <a href="{{ route('subscription.plans') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition duration-200">
                    Voir les plans disponibles
                </a>
            </div>
        @endif

        <h2 class="text-2xl font-bold mb-4">Historique des factures</h2>
        @if($invoices->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoices as $invoice)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $invoice->number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $invoice->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($invoice->amount, 2) }} €
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $invoice->status === 'paid' ? 'Payée' : 'En attente' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('invoice.download', $invoice->id) }}" class="text-blue-600 hover:text-blue-900">
                                        Télécharger
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $invoices->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-6 text-center text-gray-600">
                Aucune facture disponible.
            </div>
        @endif
    </div>
</div>
@endsection