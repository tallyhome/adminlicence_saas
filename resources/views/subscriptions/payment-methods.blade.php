@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Moyens de paiement</h1>

    <!-- Formulaire d'ajout de carte -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-6">Ajouter une carte</h2>
        <form id="payment-form" class="space-y-4">
            <div id="card-element" class="p-4 border rounded-lg"></div>
            <div id="card-errors" class="text-red-600 text-sm"></div>
            <div class="flex items-center space-x-2 mt-4">
                <input type="checkbox" id="default-payment" name="default-payment" class="rounded text-blue-600">
                <label for="default-payment">Définir comme moyen de paiement par défaut</label>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition duration-200">
                Ajouter la carte
            </button>
        </form>
    </div>

    <!-- Liste des moyens de paiement -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-semibold mb-6">Vos moyens de paiement</h2>
        <div class="space-y-4">
            @forelse($paymentMethods as $method)
                <div class="flex items-center justify-between p-4 border rounded-lg">
                    <div class="flex items-center space-x-4">
                        @if($method->provider === 'stripe')
                            <i class="fas fa-credit-card text-gray-600"></i>
                        @else
                            <i class="fab fa-paypal text-blue-600"></i>
                        @endif
                        <div>
                            <p class="font-medium">{{ $method->card_brand }} •••• {{ $method->card_last4 }}</p>
                            <p class="text-sm text-gray-600">Expire le {{ $method->card_exp_month }}/{{ $method->card_exp_year }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if(!$method->is_default)
                            <button onclick="setDefaultPaymentMethod('{{ $method->id }}')" 
                                    class="text-blue-600 hover:text-blue-800">
                                Définir par défaut
                            </button>
                        @else
                            <span class="text-sm text-green-600">Par défaut</span>
                        @endif
                        <button onclick="deletePaymentMethod('{{ $method->id }}')" 
                                class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-gray-600">Aucun moyen de paiement enregistré.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config("services.stripe.key") }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');

    // Gestion des erreurs Stripe
    cardElement.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Soumission du formulaire
    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        const isDefault = document.getElementById('default-payment').checked;

        try {
            const { setupIntent } = await fetch('/payment/setup-intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(function(r) { return r.json(); });

            const { error, paymentMethod } = await stripe.confirmCardSetup(setupIntent.client_secret, {
                payment_method: { card: cardElement }
            });

            if (error) {
                document.getElementById('card-errors').textContent = error.message;
                return;
            }

            // Enregistrement du moyen de paiement
            await fetch('/payment/methods', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    payment_method_id: paymentMethod.id,
                    is_default: isDefault
                })
            });

            window.location.reload();
        } catch (e) {
            console.error(e);
            document.getElementById('card-errors').textContent = 'Une erreur est survenue.';
        }
    });

    // Définir un moyen de paiement par défaut
    async function setDefaultPaymentMethod(id) {
        try {
            await fetch(`/payment/methods/${id}/default`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            window.location.reload();
        } catch (e) {
            console.error(e);
            alert('Une erreur est survenue lors de la modification.');
        }
    }

    // Supprimer un moyen de paiement
    async function deletePaymentMethod(id) {
        if (!confirm('Voulez-vous vraiment supprimer ce moyen de paiement ?')) return;

        try {
            await fetch(`/payment/methods/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            window.location.reload();
        } catch (e) {
            console.error(e);
            alert('Une erreur est survenue lors de la suppression.');
        }
    }
</script>
@endpush
@endsection