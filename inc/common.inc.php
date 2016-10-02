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
                    <li class='<?=$mode=='cluster'?'active':''?>'><a href='/cluster'>Cluster</a></li>
                    <li class='<?=$mode=='cm'?'active':''?>'><a class='' href='/cm'>CM</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    <br/><br/><br/>

    <?
}
