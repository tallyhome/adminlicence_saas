<?php $__env->startSection('title', 'Gestion des Pages Légales'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des Pages Légales</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Paramètres</li>
        <li class="breadcrumb-item active">Pages Légales</li>
    </ol>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-file-contract me-1"></i>
            Édition des Pages Légales
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="legalTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="terms-tab" data-bs-toggle="tab" data-bs-target="#terms" type="button" role="tab" aria-controls="terms" aria-selected="true">
                        <i class="fas fa-gavel me-1"></i> Conditions Générales d'Utilisation
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="privacy-tab" data-bs-toggle="tab" data-bs-target="#privacy" type="button" role="tab" aria-controls="privacy" aria-selected="false">
                        <i class="fas fa-user-shield me-1"></i> Politique de Confidentialité
                    </button>
                </li>
            </ul>
            <div class="tab-content mt-4" id="legalTabsContent">
                <!-- Onglet Conditions Générales d'Utilisation -->
                <div class="tab-pane fade show active" id="terms" role="tabpanel" aria-labelledby="terms-tab">
                    <form action="<?php echo e(route('admin.settings.legal-pages.update-terms')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="terms-title" class="form-label">Titre</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="terms-title" name="title" value="<?php echo e(old('title', $termsPage->title)); ?>" required>
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-3">
                            <label for="terms-content" class="form-label">Contenu</label>
                            <textarea class="form-control" id="terms-content" name="content" rows="15" required><?php echo e(old('content', $termsPage->content)); ?></textarea>
                            <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">
                                    <?php if($termsPage->last_updated_by): ?>
                                        Dernière mise à jour par <?php echo e($termsPage->updatedBy->name ?? 'Administrateur'); ?> le <?php echo e($termsPage->updated_at->format('d/m/Y à H:i')); ?>

                                    <?php else: ?>
                                        Pas encore modifié
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div>
                                <a href="<?php echo e(route('terms')); ?>" target="_blank" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-eye me-1"></i> Prévisualiser
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Onglet Politique de Confidentialité -->
                <div class="tab-pane fade" id="privacy" role="tabpanel" aria-labelledby="privacy-tab">
                    <form action="<?php echo e(route('admin.settings.legal-pages.update-privacy')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="privacy-title" class="form-label">Titre</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="privacy-title" name="title" value="<?php echo e(old('title', $privacyPage->title)); ?>" required>
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-3">
                            <label for="privacy-content" class="form-label">Contenu</label>
                            <textarea class="form-control" id="privacy-content" name="content" rows="15" required><?php echo e(old('content', $privacyPage->content)); ?></textarea>
                            <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">
                                    <?php if($privacyPage->last_updated_by): ?>
                                        Dernière mise à jour par <?php echo e($privacyPage->updatedBy->name ?? 'Administrateur'); ?> le <?php echo e($privacyPage->updated_at->format('d/m/Y à H:i')); ?>

                                    <?php else: ?>
                                        Pas encore modifié
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div>
                                <a href="<?php echo e(route('privacy')); ?>" target="_blank" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-eye me-1"></i> Prévisualiser
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/super-build/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fonction pour initialiser CKEditor sur un élément
        function initCKEditor(elementId) {
            CKEDITOR.ClassicEditor.create(document.getElementById(elementId), {
                // Configuration de la barre d'outils
                toolbar: {
                    items: [
                        'exportPDF','exportWord', '|',
                        'findAndReplace', 'selectAll', '|',
                        'heading', '|',
                        'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                        'bulletedList', 'numberedList', 'todoList', '|',
                        'outdent', 'indent', '|',
                        'undo', 'redo',
                        '-',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                        'alignment', '|',
                        'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                        'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                        'textPartLanguage', '|',
                        'sourceEditing'
                    ],
                    shouldNotGroupWhenFull: true
                },
                // Personnalisation de la liste des titres disponibles
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraphe', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Titre 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Titre 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Titre 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Titre 4', class: 'ck-heading_heading4' },
                        { model: 'heading5', view: 'h5', title: 'Titre 5', class: 'ck-heading_heading5' },
                        { model: 'heading6', view: 'h6', title: 'Titre 6', class: 'ck-heading_heading6' }
                    ]
                },
                // Configuration pour l'insertion d'images
                image: {
                    toolbar: [
                        'imageTextAlternative',
                        'imageStyle:inline',
                        'imageStyle:block',
                        'imageStyle:side'
                    ]
                },
                // Configuration pour les tableaux
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells',
                        'tableCellProperties',
                        'tableProperties'
                    ]
                },
                // Options de langue
                language: 'fr',
                // Autres options
                licenseKey: '',
            })
            .then(editor => {
                // Sauvegarde automatique lors de la modification
                editor.model.document.on('change:data', () => {
                    const data = editor.getData();
                    document.getElementById(elementId).value = data;
                });
            })
            .catch(error => {
                console.error(error);
            });
        }

        // Initialiser les éditeurs
        initCKEditor('terms-content');
        initCKEditor('privacy-content');
    });
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    /* Styles pour l'éditeur CKEditor */
    .ck-editor__editable {
        min-height: 400px;
        max-height: 600px;
    }
    .ck-editor__editable_inline {
        padding: 0 1.5em !important;
    }
    .ck.ck-editor {
        width: 100%;
    }
    .ck.ck-content {
        font-size: 1rem;
        line-height: 1.6;
        color: #333;
    }
    .ck.ck-content h1 {
        font-size: 2rem;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        color: #0d6efd;
    }
    .ck.ck-content h2 {
        font-size: 1.75rem;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
        color: #0d6efd;
    }
    .ck.ck-content h3 {
        font-size: 1.5rem;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        color: #0d6efd;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/settings/legal_pages.blade.php ENDPATH**/ ?>