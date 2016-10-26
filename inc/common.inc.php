<?php

function insert_dash_navbar($mode = '')
{
    ?>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">Dash</a>
            </div>

            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class='<?=$mode=='cluster'?'active':''?>'><a href="#" onclick="window.location.href='/cluster'" >Cluster</a></li>
                    <li class='<?=$mode=='cm'?'active':''?>'><a href="#" onclick="window.location.href='/cm'">CM</a></li>
                    <li class='<?=$mode=='tools'?'active':''?>'><a href="#" onclick="window.location.href='/tools'">Tools</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    <br/><br/><br/>

    <?
}

function insert_dash_breadcrumbs($root, $views, $view) {
    $breadcrumbs = array("/$root" => strtoupper($root));
    if ($parent = $views[$view]['parent_view']) {
        $breadcrumbs["/$root?view=" . $parent] = $views[$parent]['title'];
    }
    $breadcrumbs[$_SERVER['REQUEST_URI']] = $views[$view]['title'];
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <ol class="breadcrumb">
            <?
            $end_url = end(array_keys($breadcrumbs));
            foreach ($breadcrumbs as $url => $title) {
                if ($url == $end_url) {
                    ?><li class="active"><?=$title?></li><?
                } else {
                    ?><li><a href="<?=$url?>"><?=$title?></a></li><?
                }
            }
            ?>
        </ol>
      </div>
    </div>
  </div>
    <?
}
