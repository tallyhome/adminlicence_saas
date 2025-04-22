@extends('layouts.user')

@section('title', 'Mes Produits')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes Produits</h5>
                    <div>
                        <a href="{{ route('user.products.export.csv') }}" class="btn btn-success me-2">
                            <i class="fas fa-file-export"></i> Exporter CSV
                        </a>
                        <a href="{{ route('user.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Produit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if($products->isEmpty())
                        <div class="alert alert-info">
                            Vous n'avez pas encore créé de produits. 
                            <a href="{{ route('user.products.create') }}">Créer votre premier produit</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 80px">Image</th>
                                        <th>Nom</th>
                                        <th>Version</th>
                                        <th>Prix</th>
                                        <th>Statut</th>
                                        <th>Licences</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                        <i class="fas fa-box fa-2x text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('user.products.show', $product->id) }}">
                                                    {{ $product->name }}
                                                </a>
                                            </td>
                                            <td>{{ $product->version }}</td>
                                            <td>
                                                @if($product->price)
                                                    {{ number_format($product->price, 2) }} €
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->is_active)
                                                    <span class="badge bg-success">Actif</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $product->licences()->count() }} licences
                                                <span class="text-muted">({{ $product->licences()->where('is_active', 1)->count() }} actives)</span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('user.products.show', $product->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('user.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) { 
                                                                document.getElementById('delete-product-{{ $product->id }}').submit(); 
                                                            }">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delete-product-{{ $product->id }}" 
                                                          action="{{ route('user.products.destroy', $product->id) }}" 
                                                          method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
