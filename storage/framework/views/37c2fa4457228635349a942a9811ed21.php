<?php $__env->startSection('title', 'Tableau de bord utilisateur'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-2">Tableau de bord utilisateur</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-success mb-4">
                        <h4 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Connexion réussie !</h4>
                        <p>Bienvenue sur votre tableau de bord, <?php echo e(auth()->user()->name); ?>.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4 h-100">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informations personnelles</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nom :</strong> <?php echo e(auth()->user()->name); ?></p>
                                    <p><strong>Email :</strong> <?php echo e(auth()->user()->email); ?></p>
                                    <p><strong>Membre depuis :</strong> <?php echo e(auth()->user()->created_at->format('d/m/Y')); ?></p>
                                    <a href="#" class="btn btn-outline-primary">Modifier mon profil</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card mb-4 h-100">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Abonnement</h5>
                                </div>
                                <div class="card-body">
                                    <?php if(isset($subscription) && $subscription): ?>
                                        <?php
                                            $plan = \App\Models\Plan::find($subscription->plan_id);
                                        ?>
                                        <p><strong>Plan actuel :</strong> <?php echo e($plan ? $plan->name : $subscription->name); ?></p>
                                        <p><strong>Statut :</strong> 
                                            <span class="badge bg-success">Actif</span>
                                        </p>
                                        <p><strong>Prochain paiement :</strong> 
                                            <?php if($subscription->ends_at): ?>
                                                <?php echo e($subscription->ends_at->format('d/m/Y')); ?>

                                            <?php else: ?>
                                                Non défini
                                            <?php endif; ?>
                                        </p>
                                        
                                        <?php if($plan): ?>
                                            <div class="mt-3">
                                                <h6>Limites de votre abonnement :</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <td>Projets</td>
                                                            <td>
                                                                <?php
                                                                    $projectsCount = auth()->user()->projects()->count();
                                                                    $maxProjects = $plan->max_projects ?? 'Illimité';
                                                                    if ($maxProjects == 0) $maxProjects = 'Illimité';
                                                                    $projectsPercentage = $maxProjects !== 'Illimité' ? min(100, round(($projectsCount / $maxProjects) * 100)) : 0;
                                                                ?>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span><?php echo e($projectsCount); ?> / <?php echo e($maxProjects); ?></span>
                                                                </div>
                                                                <?php if($maxProjects !== 'Illimité'): ?>
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar <?php echo e($projectsPercentage > 80 ? 'bg-danger' : 'bg-success'); ?>" role="progressbar" style="width: <?php echo e($projectsPercentage); ?>%" aria-valuenow="<?php echo e($projectsPercentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Clés de licence projet</td>
                                                            <td>
                                                                <?php
                                                                    $licencesCount = auth()->user()->licences()->count();
                                                                    $maxLicences = $plan->max_licenses ?? 'Illimité';
                                                                    if ($maxLicences == 0) $maxLicences = 'Illimité';
                                                                    $licencesPercentage = $maxLicences !== 'Illimité' ? min(100, round(($licencesCount / $maxLicences) * 100)) : 0;
                                                                ?>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span><?php echo e($licencesCount); ?> / <?php echo e($maxLicences); ?></span>
                                                                </div>
                                                                <?php if($maxLicences !== 'Illimité'): ?>
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar <?php echo e($licencesPercentage > 80 ? 'bg-danger' : 'bg-success'); ?>" role="progressbar" style="width: <?php echo e($licencesPercentage); ?>%" aria-valuenow="<?php echo e($licencesPercentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Produits</td>
                                                            <td>
                                                                <?php
                                                                    $productsCount = auth()->user()->products()->count();
                                                                    $maxProducts = $plan->max_products ?? 'Illimité';
                                                                    if ($maxProducts == 0) $maxProducts = 'Illimité';
                                                                    $productsPercentage = $maxProducts !== 'Illimité' ? min(100, round(($productsCount / $maxProducts) * 100)) : 0;
                                                                ?>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span><?php echo e($productsCount); ?> / <?php echo e($maxProducts); ?></span>
                                                                </div>
                                                                <?php if($maxProducts !== 'Illimité'): ?>
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar <?php echo e($productsPercentage > 80 ? 'bg-danger' : 'bg-success'); ?>" role="progressbar" style="width: <?php echo e($productsPercentage); ?>%" aria-valuenow="<?php echo e($productsPercentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Licences produit</td>
                                                            <td>
                                                                <?php
                                                                    // Si la relation productLicenses n'existe pas encore, utilisez 0 comme valeur par défaut
                                                                    $productLicencesCount = method_exists(auth()->user(), 'productLicenses') ? auth()->user()->productLicenses()->count() : 0;
                                                                    $maxProductLicences = $plan->max_product_licenses ?? 'Illimité';
                                                                    if ($maxProductLicences == 0) $maxProductLicences = 'Illimité';
                                                                    $productLicencesPercentage = $maxProductLicences !== 'Illimité' ? min(100, round(($productLicencesCount / $maxProductLicences) * 100)) : 0;
                                                                ?>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span><?php echo e($productLicencesCount); ?> / <?php echo e($maxProductLicences); ?></span>
                                                                </div>
                                                                <?php if($maxProductLicences !== 'Illimité'): ?>
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar <?php echo e($productLicencesPercentage > 80 ? 'bg-danger' : 'bg-success'); ?>" role="progressbar" style="width: <?php echo e($productLicencesPercentage); ?>%" aria-valuenow="<?php echo e($productLicencesPercentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php if($plan->has_api_access): ?>
                                                        <tr>
                                                            <td>APIs</td>
                                                            <td>
                                                                <?php
                                                                    // Si la relation apis n'existe pas encore, utilisez 0 comme valeur par défaut
                                                                    $apisCount = method_exists(auth()->user(), 'apis') ? auth()->user()->apis()->count() : 0;
                                                                    $maxApis = $plan->max_apis ?? 'Illimité';
                                                                    if ($maxApis == 0) $maxApis = 'Illimité';
                                                                    $apisPercentage = $maxApis !== 'Illimité' ? min(100, round(($apisCount / $maxApis) * 100)) : 0;
                                                                ?>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span><?php echo e($apisCount); ?> / <?php echo e($maxApis); ?></span>
                                                                </div>
                                                                <?php if($maxApis !== 'Illimité'): ?>
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar <?php echo e($apisPercentage > 80 ? 'bg-danger' : 'bg-success'); ?>" role="progressbar" style="width: <?php echo e($apisPercentage); ?>%" aria-valuenow="<?php echo e($apisPercentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Clés d'API</td>
                                                            <td>
                                                                <?php
                                                                    // Si la relation apiKeys n'existe pas encore, utilisez 0 comme valeur par défaut
                                                                    $apiKeysCount = method_exists(auth()->user(), 'apiKeys') ? auth()->user()->apiKeys()->count() : 0;
                                                                    $maxApiKeys = $plan->max_api_keys ?? 'Illimité';
                                                                    if ($maxApiKeys == 0) $maxApiKeys = 'Illimité';
                                                                    $apiKeysPercentage = $maxApiKeys !== 'Illimité' ? min(100, round(($apiKeysCount / $maxApiKeys) * 100)) : 0;
                                                                ?>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span><?php echo e($apiKeysCount); ?> / <?php echo e($maxApiKeys); ?></span>
                                                                </div>
                                                                <?php if($maxApiKeys !== 'Illimité'): ?>
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar <?php echo e($apiKeysPercentage > 80 ? 'bg-danger' : 'bg-success'); ?>" role="progressbar" style="width: <?php echo e($apiKeysPercentage); ?>%" aria-valuenow="<?php echo e($apiKeysPercentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <a href="<?php echo e(route('subscription.plans')); ?>" class="btn btn-outline-primary mt-3">Gérer mon abonnement</a>
                                    <?php else: ?>
                                        <p>Vous n'avez pas d'abonnement actif.</p>
                                        <a href="<?php echo e(route('subscription.plans')); ?>" class="btn btn-primary">Voir les plans disponibles</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card mb-4 h-100">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Licences</h5>
                                </div>
                                <div class="card-body">
                                    <p>Gérez vos licences et accédez à vos produits.</p>
                                    <a href="#" class="btn btn-outline-primary">Voir mes licences</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notifications récentes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Bienvenue sur AdminLicence</h6>
                                                <small><?php echo e(now()->format('d/m/Y')); ?></small>
                                            </div>
                                            <p class="mb-1">Merci de vous être connecté avec succès à notre plateforme.</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Aide et support</h5>
                                </div>
                                <div class="card-body">
                                    <p>Besoin d'aide ? Contactez notre équipe de support ou consultez notre documentation.</p>
                                    <div class="d-grid gap-2">
                                        <a href="#" class="btn btn-outline-primary">Documentation</a>
                                        <a href="#" class="btn btn-outline-secondary">Contacter le support</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Mes projets récents</h5>
                                    <a href="<?php echo e(route('user.projects.index')); ?>" class="btn btn-sm btn-primary">Voir tous mes projets</a>
                                </div>
                                <div class="card-body">
                                    <?php
                                        $projects = auth()->user()->projects()->orderBy('created_at', 'desc')->take(3)->get();
                                    ?>
                                    
                                    <?php if($projects->count() > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Statut</th>
                                                        <th>Clés</th>
                                                        <th>Date de création</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><?php echo e($project->name); ?></td>
                                                            <td>
                                                                <?php if($project->status === 'active'): ?>
                                                                    <span class="badge bg-success">Actif</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactif</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?php echo e($project->totalKeysCount() ?? 0); ?></td>
                                                            <td><?php echo e($project->created_at->format('d/m/Y')); ?></td>
                                                            <td>
                                                                <a href="<?php echo e(route('user.projects.show', $project->id)); ?>" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            Vous n'avez pas encore créé de projets. 
                                            <a href="<?php echo e(route('user.projects.create')); ?>" class="alert-link">Créer votre premier projet</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Mes produits récents</h5>
                                    <a href="<?php echo e(route('user.products.index')); ?>" class="btn btn-sm btn-primary">Voir tous mes produits</a>
                                </div>
                                <div class="card-body">
                                    <?php
                                        $products = auth()->user()->products()->orderBy('created_at', 'desc')->take(3)->get();
                                    ?>
                                    
                                    <?php if($products->count() > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Version</th>
                                                        <th>Prix</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><?php echo e($product->name); ?></td>
                                                            <td><?php echo e($product->version); ?></td>
                                                            <td><?php echo e($product->price ? number_format($product->price, 2) . ' €' : '-'); ?></td>
                                                            <td>
                                                                <?php if($product->status === 'active'): ?>
                                                                    <span class="badge bg-success">Actif</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactif</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="<?php echo e(route('user.products.show', $product->id)); ?>" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            Vous n'avez pas encore créé de produits. 
                                            <a href="<?php echo e(route('user.products.create')); ?>" class="alert-link">Créer votre premier produit</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Mes licences récentes</h5>
                                    <a href="<?php echo e(route('user.licences.index')); ?>" class="btn btn-sm btn-primary">Voir toutes mes licences</a>
                                </div>
                                <div class="card-body">
                                    <?php
                                        $licences = auth()->user()->licences()->orderBy('created_at', 'desc')->take(3)->get();
                                    ?>
                                    
                                    <?php if($licences->count() > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Clé</th>
                                                        <th>Produit</th>
                                                        <th>Client</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $licences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $licence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><code><?php echo e($licence->licence_key); ?></code></td>
                                                            <td><?php echo e($licence->product->name ?? 'N/A'); ?></td>
                                                            <td><?php echo e($licence->client_name); ?></td>
                                                            <td>
                                                                <?php if($licence->status === 'active'): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php elseif($licence->status === 'expired'): ?>
                                                                    <span class="badge bg-warning">Expirée</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="<?php echo e(route('user.licences.show', $licence->id)); ?>" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            Vous n'avez pas encore créé de licences. 
                                            <a href="<?php echo e(route('user.licences.create')); ?>" class="alert-link">Créer votre première licence</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notifications récentes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Bienvenue sur AdminLicence</h6>
                                                <small><?php echo e(now()->format('d/m/Y')); ?></small>
                                            </div>
                                            <p class="mb-1">Merci de vous être connecté avec succès à notre plateforme.</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Aide et support</h5>
                                </div>
                                <div class="card-body">
                                    <p>Besoin d'aide ? Contactez notre équipe de support ou consultez notre documentation.</p>
                                    <div class="d-grid gap-2">
                                        <a href="#" class="btn btn-outline-primary">Documentation</a>
                                        <a href="#" class="btn btn-outline-secondary">Contacter le support</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <form action="<?php echo e(route('user.logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/user/dashboard.blade.php ENDPATH**/ ?>