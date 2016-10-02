
<h3>Adsense</h3>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Total: <?= $total[1] ?>, Avg: <?= $avg[1] ?></h3>
  </div>
  <table class="table">

    <tbody>
    <thead>
    <tr>
      <th>date</th>
      <th>revenue</th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($resource->getRows() as $value) { ?>
      <tr>
        <td><?= $value[0] ?></td>
        <td><?= $value[1] ?></td>
      </tr>
    <? } ?>
    </tbody>
  </table>
</div>


