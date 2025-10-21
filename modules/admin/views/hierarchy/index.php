<?php
use yii\helpers\Html;
?>

<h1>Hierarchy Management (Display Only - Editing Disabled)</h1>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Organ System</th>
            <th>Subject</th>
            <th>Chapter</th>
            <th>Topic</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($hierarchies as $h): ?>
            <tr>
                <td><?= $h['id'] ?></td>
                <td><?= Html::encode($h['organsys']['name'] ?? 'N/A') ?></td>
                <td><?= Html::encode($h['subject']['name'] ?? 'N/A') ?></td>
                <td><?= Html::encode($h['chapter']['name'] ?? 'N/A') ?></td>
                <td><?= Html::encode($h['topic']['name'] ?? 'N/A') ?></td>
                <td>
                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
