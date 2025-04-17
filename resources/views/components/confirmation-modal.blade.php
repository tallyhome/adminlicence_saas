{{-- 
    Composant de modal de confirmation
    Paramètres:
    - id: Identifiant unique du modal
    - title: Titre du modal
    - message: Message de confirmation
    - confirmButtonText: Texte du bouton de confirmation (par défaut: "Confirmer")
    - confirmButtonClass: Classes CSS du bouton de confirmation (par défaut: "btn-danger")
    - cancelButtonText: Texte du bouton d'annulation (par défaut: "Annuler")
    - formAction: Action du formulaire (optionnel)
    - formMethod: Méthode du formulaire (par défaut: "POST")
--}}

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                {{ $message }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $cancelButtonText ?? 'Annuler' }}</button>
                
                @if(isset($formAction))
                    <form action="{{ $formAction }}" method="{{ $formMethod ?? 'POST' }}" class="d-inline">
                        @csrf
                        @if(strtolower($formMethod ?? 'POST') === 'delete')
                            @method('DELETE')
                        @endif
                        {{ $slot ?? '' }}
                        <button type="submit" class="btn {{ $confirmButtonClass ?? 'btn-danger' }}">{{ $confirmButtonText ?? 'Confirmer' }}</button>
                    </form>
                @else
                    <button type="button" class="btn {{ $confirmButtonClass ?? 'btn-danger' }}" id="{{ $id }}-confirm">{{ $confirmButtonText ?? 'Confirmer' }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
