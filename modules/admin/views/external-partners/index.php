<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var \app\models\ExternalPartners $model */

$this->title = 'External Partners';
$this->params['breadcrumbs'][] = $this->title;

// Get the models from the data provider
$partners = $dataProvider->getModels();
?>

<div class="external-partners-index">

    <!-- Page Header with Title and Create Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            '<i class="bi bi-plus-lg me-1"></i> Add New Partner',
            ['create'],
            ['class' => 'btn btn-primary']
        ) ?>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Partner Name</th>
                            <th scope="col">Contact Person</th>
                            <th scope="col">Email</th>
                            <th scope="col">Status</th>
                            <th scope="col">Dashboard Link</th>
                            <th scope="col">Created On</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($partners) > 0): ?>
                            <?php foreach ($partners as $index => $model): ?>
                                <tr>
                                    <td><?= Html::encode($model->name) ?></td>
                                    <td><?= Html::encode($model->contact_person) ?></td>
                                    <td><?= Html::mailto(Html::encode($model->email), $model->email) ?></td>
                                    <td>
                                        <?php
                                        $status = strtolower($model->status);
                                        $badgeClass = 'bg-secondary';
                                        if ($status === 'active') {
                                            $badgeClass = 'bg-success';
                                        } elseif ($status === 'inactive') {
                                            $badgeClass = 'bg-danger';
                                        } elseif ($status === 'pending') {
                                            $badgeClass = 'bg-warning text-dark';
                                        }
                                        ?>
                                        <span
                                            class="badge <?= $badgeClass ?>"><?= Html::encode(ucfirst($model->status)) ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <code><?= Url::to(['/partners/exam/index', 'access' => $model->access_token], true) ?></code>
                                            <?= Html::button('<i class="bi bi-clipboard"></i>', [
                                                'class' => 'btn btn-sm btn-outline-secondary js-copy-token',
                                                'data-token' => Url::to(['/partners/exam/index', 'access' => $model->access_token], true),
                                                'title' => 'Copy to Clipboard',
                                            ]) ?>
                                        </div>
                                    </td>
                                    <td><?= Yii::$app->formatter->asDate($model->created_at, 'medium') ?></td>
                                    <td class="text-end">

                                        <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->id], [
                                            'class' => 'btn btn-sm btn-outline-primary ms-1',
                                            'title' => 'Update Partner',
                                        ]) ?>
                                        <?= Html::button('<i class="bi bi-trash"></i>', [
                                            'class' => 'btn btn-sm btn-outline-danger btn-delete',
                                            'title' => 'Delete Partner',
                                            'data-id' => $model->id,
                                            'data-url' => Url::to(['external-partners/delete']),
                                            'data-item' => 'the partner ' . Html::encode($model->name),
                                        ]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No external partners found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript for the "Copy to Clipboard" functionality
$js = <<<JS
document.querySelectorAll('.js-copy-token').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const token = this.dataset.token;
        navigator.clipboard.writeText(token).then(() => {
            const originalIcon = this.innerHTML;
            this.innerHTML = '<i class="bi bi-check-lg"></i>';
            this.classList.add('btn-success');
            this.disabled = true;

            setTimeout(() => {
                this.innerHTML = originalIcon;
                this.classList.remove('btn-success');
                this.disabled = false;
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy token: ', err);
            alert('Failed to copy token.');
        });
    });
});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>