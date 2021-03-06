<?php

function insert_header($title = '')
{
  function nocache(){if (true) {return "?nocache=".time();}}
    ?>
  <!doctype html>
  <html lang="en-US">
  <head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"
            integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <link rel="stylesheet" href="https://bootswatch.com/yeti/bootstrap.min.css" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"
            integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" crossorigin="anonymous"></script>

    <script src="/js/jackmoore-autosize-961af07/jquery.autosize.js"></script>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/cupertino/jquery-ui.css"/>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="/css/styles.css<?=nocache()?>"/>
    <link rel="stylesheet" href="/css/google-logo.css<?=nocache()?>"/>

    <script type="text/javascript" src="../../js/keymaster.js<?=nocache()?>"></script>
    <script type="text/javascript" src="../../js/stuff.js<?=nocache()?>"></script>
    <script type="text/javascript" src="../../js/EventMachine.js<?=nocache()?>"></script>
    <script type="text/javascript" src="../../js/treeView.js<?=nocache()?>"></script>
    <script type="text/javascript" src="../../js/script.js<?=nocache()?>"></script>


    <meta name="mobile-web-app-capable" content="yes">
    <meta name="viewport" content="user-scalable=no, width=device-width"/>

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white">
    <link rel="apple-touch-icon" href="/img/appicon/ios/Icon-App-iPad@2x.png">
    <meta name="format-detection" content="telephone=no">

  </head>
  <body>
  <div class="container bordered">
    <?
}

function insert_footer()
{
    ?>
  </div>  <!-- .container -->
  <div class="modal hide" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
      <h1>Processing...</h1>
    </div>
    <div class="modal-body">
      <div class="progress progress-striped active">
        <div class="bar" style="width: 100%;"></div>
      </div>
    </div>
  </div>
  </body>

  <script>
    if (("standalone" in window.navigator) && window.navigator.standalone) {
      $(function () {
        $("a").click(function (event) {
          event.preventDefault();
          window.location = $(this).attr("href");
          return false;
        });

      })
    }
  </script>

  </html>
    <?
}

function print_table($list, $item_url = '', $delete_url = '')
{
    $headers = array_keys($list[0]);
    ?>
  <table border="1">
    <tr>
        <?php foreach ($headers as $header) { ?>
          <td><strong><?= $header ?></strong></td>
        <?php } ?>
        <?php if ($delete_url) { ?>
          <td><strong>Actions</strong></td>
        <?php } ?>
    </tr>
      <?php foreach ($list as $index => $key_values) { ?>
        <tr>
            <?php foreach ($key_values as $key => $value) { ?>
                <?php if ($key == 'id' && $item_url) { ?>
                <td>
                  <a href="<?= str_replace('{ID}', $value, $item_url) ?>"><?= $value ?></a>
                </td>
                <?php } else { ?>
                <td><?= $value ?></td>
                <? } ?>
            <?php } ?>
            <?php if ($delete_url) { ?>
              <td>
                <a href="<?= str_replace('{ID}', $key_values['id'], $delete_url) ?>">delete</a>
              </td>
            <?php } ?>
        </tr>
      <?php } ?>
  </table>
    <?
}

function print_create_form($table, $fields)
{
    ?>
  <div>
    <h4>Create <?= $table ?></h4>
    <form action="" method="post" name="create_item">
        <? foreach ($fields as $field => $type) {
            $type = $type ? $type : 'text';
            switch ($type) {
                case 'text':
                    ?>
                    <?= $field ?>: <input type="text" name="<?= $table ?>[<?= $field ?>]"/> <br/>
                    <?
                    break;
                case 'textarea':
                    ?>
                  <textarea name="<?= $table ?>[<?= $field ?>]" id="" cols="30" rows="10"></textarea><br/>
                    <?
                    break;
            }
            ?>
        <? } ?>
      <button type="submit">create</button>
    </form>
  </div>
    <?
}

function print_header($text, $num = '4')
{
    ?>
  <h<?= $num ?>><?= $text ?></h<?= $num ?>><?
}

function print_link($href, $anchor)
{
    echo "<a href='$href'>$anchor</a>";
}


function print_status($status)
{
    global $statuses;
    ?>
  <span class="label <?= $statuses[$status]['class'] ?>"><?= $statuses[$status]['title'] ?></span>
    <?
}

function print_report_status($status)
{
    global $report_statuses;
    ?>
  <span class="label <?= $report_statuses[$status]['class'] ?>"><?= $report_statuses[$status]['title'] ?></span>
    <?
}

function tz_url($id)
{
    return 'http://tz.binet.pro/keys/view/index?id=' . intval($id);
}

function task_url($id)
{
    return '/cm?view=task&task_id=' . intval($id);
}


function report_url($id)
{
    return '/cm?view=report&report_id=' . intval($id);
}

function print_result(){
    global $error, $success;
    ?>

    <? if ($error) { ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <?=nl2br($error)?>
    </div>
    <? } ?>

    <? if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
        <?=nl2br($success)?>
    </div>
    <? } ?>
    <?
}
