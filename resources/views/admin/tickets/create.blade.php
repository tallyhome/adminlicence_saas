@extends('admin.layouts.app')

@section('title', 'Créer un ticket de support')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Nouveau ticket de support</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tickets.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priorité</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="low">Basse</option>
                                <option value="medium">Moyenne</option>
                                <option value="high">Haute</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer le ticket
                        </button>
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary ms-2">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
