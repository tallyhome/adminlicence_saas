@extends('admin.layouts.app')

@section('title', 'Documentation - Gestion des Produits et Licences')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Documentation - Gestion des Produits et Licences</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Documentation - Produits et Licences</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-book me-1"></i>
            Guide d'utilisation du système de produits et licences
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="v-pills-overview-tab" data-bs-toggle="pill" data-bs-target="#v-pills-overview" type="button" role="tab" aria-controls="v-pills-overview" aria-selected="true">Vue d'ensemble</button>
                        <button class="nav-link" id="v-pills-products-tab" data-bs-toggle="pill" data-bs-target="#v-pills-products" type="button" role="tab" aria-controls="v-pills-products" aria-selected="false">Gestion des produits</button>
                        <button class="nav-link" id="v-pills-licenses-tab" data-bs-toggle="pill" data-bs-target="#v-pills-licenses" type="button" role="tab" aria-controls="v-pills-licenses" aria-selected="false">Gestion des licences</button>
                        <button class="nav-link" id="v-pills-workflow-tab" data-bs-toggle="pill" data-bs-target="#v-pills-workflow" type="button" role="tab" aria-controls="v-pills-workflow" aria-selected="false">Flux de travail</button>
                        <button class="nav-link" id="v-pills-faq-tab" data-bs-toggle="pill" data-bs-target="#v-pills-faq" type="button" role="tab" aria-controls="v-pills-faq" aria-selected="false">FAQ</button>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        <!-- Vue d'ensemble -->
                        <div class="tab-pane fade show active" id="v-pills-overview" role="tabpanel" aria-labelledby="v-pills-overview-tab">
                            <h2>Vue d'ensemble du système</h2>
                            <p>Le système de gestion des produits et licences vous permet de créer, gérer et distribuer des licences pour vos produits logiciels. Voici les principaux concepts à comprendre :</p>
                            
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h4><i class="fas fa-box-open text-primary me-2"></i> Produits</h4>
                                    <p>Un <strong>produit</strong> représente un logiciel ou service que vous proposez à vos clients. Chaque produit peut avoir plusieurs licences associées.</p>
                                    <ul>
                                        <li>Chaque produit a un nom, une description, une version et un statut (actif/inactif)</li>
                                        <li>Vous pouvez définir le nombre maximum d'activations par licence pour chaque produit</li>
                                        <li>Vous pouvez définir la durée de validité par défaut des licences pour chaque produit</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h4><i class="fas fa-key text-success me-2"></i> Licences</h4>
                                    <p>Une <strong>licence</strong> est attribuée à un utilisateur pour un produit spécifique. Elle contient une clé unique qui permet d'activer le produit.</p>
                                    <ul>
                                        <li>Chaque licence est liée à un produit et à un utilisateur</li>
                                        <li>Les licences ont une clé unique générée automatiquement</li>
                                        <li>Les licences peuvent avoir une date d'expiration</li>
                                        <li>Les licences peuvent être activées, suspendues ou révoquées</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle me-2"></i> Différence entre licences et clés de série</h5>
                                <p>Dans ce système, il existe deux concepts distincts :</p>
                                <ul>
                                    <li><strong>Licences</strong> : Elles sont liées à un produit et à un utilisateur, et gèrent les droits d'accès aux produits.</li>
                                    <li><strong>Clés de série</strong> : Elles sont liées à des projets spécifiques et sont utilisées pour l'activation de logiciels.</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Gestion des produits -->
                        <div class="tab-pane fade" id="v-pills-products" role="tabpanel" aria-labelledby="v-pills-products-tab">
                            <h2>Gestion des produits</h2>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Création d'un produit</h4>
                                </div>
                                <div class="card-body">
                                    <p>Pour créer un nouveau produit, suivez ces étapes :</p>
                                    <ol>
                                        <li>Accédez à la section <a href="{{ route('admin.products.index') }}">Produits</a> dans le menu latéral</li>
                                        <li>Cliquez sur le bouton <span class="badge bg-primary"><i class="fas fa-plus me-1"></i> Nouveau produit</span></li>
                                        <li>Remplissez le formulaire avec les informations suivantes :
                                            <ul>
                                                <li><strong>Nom du produit</strong> : Nom commercial de votre produit</li>
                                                <li><strong>Description</strong> : Description détaillée du produit</li>
                                                <li><strong>Version</strong> : Version actuelle du produit</li>
                                                <li><strong>Produit actif</strong> : Cochez cette case pour activer le produit</li>
                                                <li><strong>Nombre maximum d'activations par licence</strong> : Nombre d'appareils sur lesquels une licence peut être activée simultanément</li>
                                                <li><strong>Durée de validité des licences</strong> : Durée par défaut (en jours) des licences pour ce produit</li>
                                            </ul>
                                        </li>
                                        <li>Cliquez sur <span class="badge bg-success">Créer le produit</span> pour enregistrer</li>
                                    </ol>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i> Le <strong>slug</strong> est généré automatiquement à partir du nom du produit. Il est utilisé dans les URLs et doit être unique.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Modification d'un produit</h4>
                                </div>
                                <div class="card-body">
                                    <p>Pour modifier un produit existant :</p>
                                    <ol>
                                        <li>Accédez à la liste des produits</li>
                                        <li>Cliquez sur l'icône <i class="fas fa-edit text-primary"></i> à côté du produit que vous souhaitez modifier</li>
                                        <li>Mettez à jour les informations nécessaires</li>
                                        <li>Cliquez sur <span class="badge bg-success">Enregistrer les modifications</span></li>
                                    </ol>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> La modification des paramètres d'un produit n'affecte pas les licences déjà créées.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Suppression d'un produit</h4>
                                </div>
                                <div class="card-body">
                                    <p>Pour supprimer un produit :</p>
                                    <ol>
                                        <li>Accédez à la liste des produits</li>
                                        <li>Cliquez sur l'icône <i class="fas fa-trash text-danger"></i> à côté du produit que vous souhaitez supprimer</li>
                                        <li>Confirmez la suppression dans la boîte de dialogue qui apparaît</li>
                                    </ol>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-ban me-2"></i> <strong>Attention :</strong> Vous ne pouvez pas supprimer un produit qui a des licences associées. Vous devez d'abord supprimer toutes les licences liées à ce produit.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gestion des licences -->
                        <div class="tab-pane fade" id="v-pills-licenses" role="tabpanel" aria-labelledby="v-pills-licenses-tab">
                            <h2>Gestion des licences</h2>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Création d'une licence</h4>
                                </div>
                                <div class="card-body">
                                    <p>Pour créer une nouvelle licence, suivez ces étapes :</p>
                                    <ol>
                                        <li>Accédez à la section <a href="{{ route('admin.licences.index') }}">Licences</a> dans le menu latéral</li>
                                        <li>Cliquez sur le bouton <span class="badge bg-primary"><i class="fas fa-plus me-1"></i> Nouvelle licence</span></li>
                                        <li>Remplissez le formulaire avec les informations suivantes :
                                            <ul>
                                                <li><strong>Utilisateur</strong> : Sélectionnez l'utilisateur qui recevra la licence</li>
                                                <li><strong>Produit</strong> : Sélectionnez le produit pour lequel vous créez la licence</li>
                                                <li><strong>Date d'expiration</strong> (optionnel) : Date à laquelle la licence expirera</li>
                                            </ul>
                                        </li>
                                        <li>Cliquez sur <span class="badge bg-success">Créer la licence</span> pour enregistrer</li>
                                    </ol>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> La clé de licence est générée automatiquement lors de la création.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Gestion des licences existantes</h4>
                                </div>
                                <div class="card-body">
                                    <p>Depuis la liste des licences, vous pouvez effectuer plusieurs actions :</p>
                                    <ul>
                                        <li><i class="fas fa-eye text-info me-1"></i> <strong>Voir les détails</strong> : Affiche toutes les informations de la licence</li>
                                        <li><i class="fas fa-edit text-primary me-1"></i> <strong>Modifier</strong> : Permet de modifier le statut ou la date d'expiration</li>
                                        <li><i class="fas fa-ban text-warning me-1"></i> <strong>Révoquer</strong> : Désactive la licence sans la supprimer</li>
                                        <li><i class="fas fa-sync text-success me-1"></i> <strong>Régénérer la clé</strong> : Crée une nouvelle clé de licence (l'ancienne ne fonctionnera plus)</li>
                                        <li><i class="fas fa-trash text-danger me-1"></i> <strong>Supprimer</strong> : Supprime définitivement la licence</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">États d'une licence</h4>
                                </div>
                                <div class="card-body">
                                    <p>Une licence peut avoir différents états :</p>
                                    <ul>
                                        <li><span class="badge bg-success">Active</span> : La licence est valide et peut être utilisée</li>
                                        <li><span class="badge bg-warning">Expirée</span> : La date d'expiration est dépassée</li>
                                        <li><span class="badge bg-secondary">Suspendue</span> : La licence est temporairement désactivée</li>
                                        <li><span class="badge bg-danger">Révoquée</span> : La licence a été définitivement désactivée</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Flux de travail -->
                        <div class="tab-pane fade" id="v-pills-workflow" role="tabpanel" aria-labelledby="v-pills-workflow-tab">
                            <h2>Flux de travail recommandé</h2>
                            
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h4 class="mb-3">Étapes pour configurer et gérer votre système de licences</h4>
                                    
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary">1</div>
                                            <div class="timeline-content">
                                                <h5>Créer vos produits</h5>
                                                <p>Commencez par créer tous les produits que vous souhaitez proposer à vos clients. Définissez clairement leurs caractéristiques, versions et paramètres de licence.</p>
                                                <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-outline-primary">Créer un produit</a>
                                            </div>
                                        </div>
                                        
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary">2</div>
                                            <div class="timeline-content">
                                                <h5>Créer des licences pour vos produits</h5>
                                                <p>Une fois vos produits créés, vous pouvez générer des licences pour chaque utilisateur qui achète ou a droit à un de vos produits.</p>
                                                <a href="{{ route('admin.licences.create') }}" class="btn btn-sm btn-outline-primary">Créer une licence</a>
                                            </div>
                                        </div>
                                        
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary">3</div>
                                            <div class="timeline-content">
                                                <h5>Distribuer les licences aux utilisateurs</h5>
                                                <p>Communiquez les clés de licence aux utilisateurs concernés. Ils pourront les utiliser pour activer vos produits.</p>
                                            </div>
                                        </div>
                                        
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary">4</div>
                                            <div class="timeline-content">
                                                <h5>Gérer le cycle de vie des licences</h5>
                                                <p>Surveillez l'utilisation des licences, renouvelez celles qui expirent, et révoquez celles qui ne devraient plus être utilisées.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success">
                                <h5><i class="fas fa-lightbulb me-2"></i> Bonnes pratiques</h5>
                                <ul>
                                    <li>Créez d'abord tous vos produits avant de commencer à générer des licences</li>
                                    <li>Utilisez des descriptions claires pour vos produits et licences</li>
                                    <li>Définissez des durées de licence adaptées à votre modèle commercial</li>
                                    <li>Vérifiez régulièrement les licences qui vont expirer pour les renouveler si nécessaire</li>
                                    <li>Documentez clairement pour vos utilisateurs comment utiliser leurs licences</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- FAQ -->
                        <div class="tab-pane fade" id="v-pills-faq" role="tabpanel" aria-labelledby="v-pills-faq-tab">
                            <h2>Foire aux questions (FAQ)</h2>
                            
                            <div class="accordion" id="accordionFAQ">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Quelle est la différence entre un produit et une licence ?
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionFAQ">
                                        <div class="accordion-body">
                                            <p>Un <strong>produit</strong> représente le logiciel ou service que vous proposez. Une <strong>licence</strong> est une autorisation spécifique accordée à un utilisateur pour utiliser ce produit. Vous pouvez avoir plusieurs licences pour un même produit, chacune attribuée à un utilisateur différent.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            Comment fonctionne la limitation du nombre d'activations ?
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionFAQ">
                                        <div class="accordion-body">
                                            <p>Lorsque vous créez un produit, vous pouvez définir un nombre maximum d'activations par licence. Cela limite le nombre d'appareils différents sur lesquels un utilisateur peut activer le produit avec la même licence. Par exemple, si vous définissez 3 activations maximum, l'utilisateur pourra installer et activer le produit sur 3 appareils différents, mais pas sur un 4ème sans d'abord désactiver l'un des trois premiers.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            Puis-je modifier une licence après sa création ?
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionFAQ">
                                        <div class="accordion-body">
                                            <p>Oui, vous pouvez modifier certains aspects d'une licence après sa création :</p>
                                            <ul>
                                                <li>Changer son statut (active, suspendue, révoquée)</li>
                                                <li>Modifier sa date d'expiration</li>
                                                <li>Régénérer sa clé</li>
                                            </ul>
                                            <p>Cependant, vous ne pouvez pas changer l'utilisateur ou le produit associé à une licence existante. Si vous avez besoin de faire ce type de changement, il est recommandé de supprimer la licence existante et d'en créer une nouvelle.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFour">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                            Que se passe-t-il quand une licence expire ?
                                        </button>
                                    </h2>
                                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionFAQ">
                                        <div class="accordion-body">
                                            <p>Lorsqu'une licence expire :</p>
                                            <ul>
                                                <li>Son statut passe automatiquement à "Expirée"</li>
                                                <li>L'utilisateur ne peut plus activer le produit sur de nouveaux appareils</li>
                                                <li>Selon la configuration de votre produit, il peut continuer à fonctionner sur les appareils déjà activés ou cesser de fonctionner</li>
                                            </ul>
                                            <p>Vous pouvez renouveler une licence expirée en modifiant sa date d'expiration pour une date future.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFive">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                            Comment intégrer ce système de licences à mon application ?
                                        </button>
                                    </h2>
                                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionFAQ">
                                        <div class="accordion-body">
                                            <p>Pour intégrer ce système de licences à votre application, vous devez :</p>
                                            <ol>
                                                <li>Implémenter un mécanisme de vérification de licence dans votre application</li>
                                                <li>Utiliser l'API de vérification de licence fournie par ce système</li>
                                                <li>Stocker localement les informations de licence pour permettre l'utilisation hors ligne</li>
                                                <li>Vérifier périodiquement la validité de la licence auprès du serveur</li>
                                            </ol>
                                            <p>Pour plus de détails techniques sur l'intégration, consultez la documentation développeur.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -30px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        text-align: center;
        color: white;
        font-weight: bold;
        line-height: 24px;
    }
    
    .timeline-content {
        padding-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .timeline-item:last-child .timeline-content {
        border-bottom: none;
    }
</style>
@endsection
