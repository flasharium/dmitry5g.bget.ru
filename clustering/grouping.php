<?php

require_once "../libs/proj/inc.php";

if (!isset($_REQUEST['project_id'])) {
    header('Location: /clustering/projects.php');
    die();
}

insert_header();

$id = $_REQUEST['project_id'];
$project = db_get_by_id('projects', $id);

$keys = db_list('phrases', array('project_id' => $id));

?>
<input type="hidden" id="project_id" name="project_id" value="<?=$id?>"/>

<div class="row">

    <div class="col-md-6 root-item root-keywords">
        <div class="panel panel-primary">

            <div class="panel-heading panel-heading-flex">
                <div>Ключевые слова</div>
                <button type="button" class="btn btn-default btn-xs button-switch-panels">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                </button>
            </div>


            <!-- TABS -->
            <ul class="nav nav-tabs" id="myTabs" role="tablist">
                <li role="presentation" class="active">
                    <a id="start_tab" href="#free_keywords" aria-controls="free_keywords" role="tab" data-toggle="tab">
                        <i class="glyphicon glyphicon-star"></i> Новые
                    </a>
                </li>
                <li role="presentation">
                    <a href="#grouped_keywords" aria-controls="grouped_keywords" role="tab" data-toggle="tab">
                        <i class="glyphicon glyphicon-ok"></i> В группах
                    </a>
                </li>
                <li role="presentation" class="keywords-trash">
                    <a href="#blacklist_keywords" aria-controls="blacklist_keywords" role="tab" data-toggle="tab">
                        <i class="glyphicon glyphicon-trash"></i> Корзина
                    </a>
                </li>
            </ul>
            <!-- /TABS -->


            <!-- FILTERS -->
            <div class="container-fluid tab-filters" style="margin: 5px;">
                <div class="row">
                    <div class="col-md-3 filter-first">
                        <div class="input-group">
                            <input type="text" class="form-control input-sm keyword-filter" placeholder="Фильтр">
                            <span class="input-group-btn">
                                <button tabindex="-1" class="btn btn-primary glyphicon glyphicon-remove input-sm keyword-filter-clear" type="button"></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control input-sm keyword-filter" placeholder="Фильтр">
                            <span class="input-group-btn">
                                <button tabindex="-1" class="btn btn-success glyphicon glyphicon-remove input-sm keyword-filter-clear" type="button"></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3 filter-additional">
                        <div class="input-group">
                            <input type="text" class="form-control input-sm keyword-filter" placeholder="Фильтр">
                            <span class="input-group-btn">
                                <button tabindex="-1" class="btn btn-warning glyphicon glyphicon-remove input-sm keyword-filter-clear" type="button"></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3 filter-additional">
                        <div class="input-group">
                            <input type="text" class="form-control input-sm keyword-filter" placeholder="Фильтр">
                            <span class="input-group-btn">
                                <button tabindex="-1" class="btn btn-danger glyphicon glyphicon-remove input-sm keyword-filter-clear" type="button"></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /FILTERS -->


            <div class="tab-content tab-content-fullscreen">
                <div role="tabpanel" class="tab-pane active" id="free_keywords"></div>
                <div role="tabpanel" class="tab-pane" id="grouped_keywords"></div>
                <div role="tabpanel" class="tab-pane" id="blacklist_keywords"></div>
            </div>


            <div class="panel-footer panel-primary">Panel footer</div>
        </div>
    </div>


    <div class="col-md-6 root-item root-groups">
        <div class="panel panel-primary">
            <div class="panel-heading panel-heading-flex">
                <div>Группы</div>

                <button type="button" class="btn btn-default btn-xs button-create-section" data-toggle="tooltip" data-placement="кшпре" title="Создать раздел">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button>

                <button type="button" class="btn btn-default btn-xs button-switch-panels">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                </button>
            </div>

            <div class="panel-body tab-groups-fullscreen">
                <div class='keywords-groups '>
                    <br/><br/>
                </div>
            </div>

            <div class="panel-footer panel-primary">Panel footer</div>
        </div>
    </div>

</div>

<div class="modal fade" tabindex="-1" role="dialog" id="myModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Новая секция</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="titleField">Название</label>
                    <input type="text" value="" class="form-control myModal-title" name="" id="titleField">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary myModal-submit">Создать</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?

insert_footer();
