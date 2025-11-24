<?php
use yii\helpers\Html;
?>

<h1>Hierarchy List</h1>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Organ System</th>
            <th>Subject</th>
            <th>Chapter</th>
            <th>Topic</th>

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

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
