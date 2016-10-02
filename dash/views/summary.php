<?php

$cpi = $total[1] / $unique_users;

$seo = $unique_users*4 / $searchable_pages_count;

?>

<div class="jumbotron">
  <h1>Твой результат за неделю:</h1>
  <p>
    Доход:
    <span style="white-space: nowrap">
      <span class="glyphicon glyphicon-rub"></span>
      <strong><?= $total[1] ?></strong>
    </span>
  </p>
  <p>
    Трафик:
    <span style="white-space: nowrap">
      <span class="glyphicon glyphicon-user"></span>
      <strong><?= $unique_users ?></strong>
    </span>
  </p>
  <p>
    SEO:
    <span style="white-space: nowrap">
      <span class="glyphicon glyphicon-scale"></span>
      <strong><? printf("%d", $seo) ?></strong>
      <span class="badge">200 - 500</span>
    </span>
  </p>
  <p>
    <span title="Доход на посетителя">RPU:</span>
    <span style="white-space: nowrap">
      <span class="glyphicon glyphicon-rub"></span>
      <strong><? printf("%.3f", $cpi) ?></strong>
      <span class="badge">0.1 - 0.3</span>
    </span>
  </p>
</div>
