<?php
use yii\helpers\Url;
$this->title = 'Pricing';
?>

<section id="pricing" class="pricing section">
  <div class="container">
    <div class="row gy-4 justify-content-center">
      <div class="col-12 text-center mb-4">
        <h3>Select a Subscription</h3>
      </div>

      <?php foreach ($plans as $index => $plan): ?>
        <div class="col-xl-3 col-md-6 d-flex">
          <div class="pricing-item shadow p-4 w-100 d-flex flex-column <?= $index === 1 ? 'featured' : '' ?>"
            style="<?= $index === 1 ? 'scale: 1.05; z-index: 9;' : '' ?>">
            <div class="mb-3 text-center">
              <h3><?= htmlspecialchars($plan['name']) ?></h3>
              <h4><sup>$</sup><?= $plan['price'] ?><span> / <?= $plan['duration_days'] ?>
                  days</span></h4>
            </div>

            <ul class="list-unstyled flex-grow-1">
              <?php
              $decoded = json_decode($plan['features_json'], true);

              if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
              }

              $features = is_array($decoded) ? $decoded : [];
              foreach ($features as $f):
                $isAvailable = strpos($f, '[x]') === false;
                $text = str_replace('[x]', '', $f);
                ?>
                <li class="mb-2 d-flex align-items-center <?= $isAvailable ? '' : 'text-secondary' ?>">
                  <i
                    class="bi <?= $isAvailable ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i>
                  <?= htmlspecialchars($text) ?>
                </li>
              <?php endforeach ?>
            </ul>

            <div class="btn-wrap mt-3 text-center">
              <a href="<?= Url::to(['/register']) ?>">
                <label class="btn btn-outline-primary px-4" for="plan-<?= $plan['id'] ?>">Register Now</label>
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>