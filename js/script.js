log = console.log;
var clusterNamespace = clusterNamespace || {};

function isTouchDevice() {
    return 'ontouchstart' in document.documentElement;
}

function asyncRequest(data, onSuccess) {
    $.ajax({
        type: 'POST',
        url: '/cluster/async.php',
        data: data,
        success: function (responce) {
            onSuccess(responce)
        },
        error: function (a, b, c) {
            // alert("error: " + a.responseText);
            console.log({a: a, b: b, c: c})
        }
    });
}

function createTreeView() {
    var loader = $('<div id="groupsLoader" class="loader-wrapper"><div class="loader"/></div>').show()
    $('.keywords-groups').hide().parent().append(loader)

    asyncRequest({project_id: projectId, action: 'list_groups'}, function (resp) {
        clusterNamespace.treeView = new TreeView(isTouchDevice());
        clusterNamespace.treeView.setView($(".keywords-groups").eq(0));
        clusterNamespace.treeView.setData(resp);
        clusterNamespace.treeView.redraw()

        $('#groupsLoader').remove()
        $('.keywords-groups').show()
    })
}

function buildDraggableUI() {
    $(".keyword-item").draggable({
        distance: 20,
        revert: "invalid",
        zIndex: 100,
        cursorAt: {top: -5, left: -5},
        containment: "window",
        helper: function () {
            var selected = $('.keyword-item input:checked').parents('li');
            if (selected.length === 0) {
                selected = $(this);
            }
            var container = $('<div></div>').attr('id', 'draggingContainer').css({'width' : selected.css('width')});
            container.append(selected.clone());
            return container;
        }
    });
}

function updateCurrentDataset() {
    var dataset = $("#myTabs").find(".active a").attr('aria-controls');
    updateTabWithDataset(dataset)
}

function updateTabWithDataset(dataset) {
    $('.tab-pane.active').hide().parent().append($('<div class="keysLoader loader-wrapper"><div class="loader"/></div>').show())

    var filters = [];
    $('.keyword-filter').each(function () {
        filters.push($(this).val())
    });

    var colors = [
        '#d0dcff', '#d2ffd2', '#ffe290', '#ffc7c7'
    ]

    asyncRequest({project_id: projectId, action: 'list_dataset', dataset: dataset, filter: filters}, function (resp) {

        var ul = $("<ul class='list-group'></ul>");

        for (var i = 0; i < resp.length; i++) {
            var key = resp[i]

            // Раскраска вхождений в ключах
            var phraseView = key['phrase']
            for (var f in filters) {
                var word = filters[f]
                if (!word) continue;
                var res = '';
                var parts = phraseView.split(word)
                for (p in parts) {
                    res += parts[p] + (p == parts.length - 1 ? '' : '<span style="background-color:' + colors[f] + '">' + word + '</span>')
                }
                phraseView = res
            }

            var li = $('<li class="list-group-item keyword-item checkbox">'
            + '<span class="badge">' + key['frequence'] + '</span>'
            + '<label style="display: block">' + '<input type="checkbox"/>' + phraseView + '</label>' + '</li>')

            li.attr('keyId', key.id)
            li.attr('phrase', key.phrase)
            li.attr('frequence', key.frequence)

            ul.append(li);
        }

        $('#' + dataset).empty().append(ul);
        $("ul, li").disableSelection();

        ul.find(".keyword-item").each(function (item) {
            var self = $(this);
            var checkbox = self.find("input[type=checkbox]").eq(0);
            checkbox.on('change', function (val) {

                // if (checkbox.prop('checked')) {
                //     self.addClass('list-item-checked').addClass('ui-draggable-dragging')
                // } else {
                //     self.removeClass('list-item-checked').removeClass('ui-draggable-dragging')
                // }

                EventMachine.send('change_selected_keywords_set')
            })
        });

        if (isTouchDevice()) {

            $('.tab-content').prepend($(".tab-filters").eq(0))
            $('body').css({'overflow-y': 'hidden'})

        } else {
            buildDraggableUI()
        }

        $('.keysLoader').remove()
        $('.tab-pane.active').show()


        $(this).tab('show');

        $("#myTabs a").each(function(){
            var tabName = $(this).attr('aria-controls')
            if (tabName != dataset) {
                $('#' + tabName).html('')
            }
        });

        EventMachine.send('change_selected_keywords_set')
        EventMachine.send('need_to_update_statistics')
    });
}

function initSwitchPanelsButton() {
    $('.button-switch-panels').on('click', function () {
        $('.root-item').each(function () {
            $(this).toggle();
        })
    });
}

function initKeywordFilters() {
    $('.keyword-filter').each(function () {
        $(this).keypress(function (e) {
            if (e.which == 13) {
                e.preventDefault();
                var dataset = $("#myTabs").find(".active a").attr('aria-controls');
                updateTabWithDataset(dataset)
            }
        })
    });

    $('.keyword-filter-clear').on('click', function () {
        $(this).parents('.input-group').eq(0).find('.keyword-filter').eq(0).val('');
        updateCurrentDataset()
    });
}

function initTabSwitching() {
    $('#myTabs').find('a').click(function (e) {
        e.preventDefault();
        var dataset = $(this).attr('aria-controls');
        updateTabWithDataset(dataset);
    });
}

function createCommonActions() {
    EventMachine.register('treeUpdated', function () {
        var data = JSON.stringify(clusterNamespace.treeView.getData())
        asyncRequest({project_id: projectId, action: 'change_struct', "struct": data}, function (data) {
            EventMachine.send('need_to_update_statistics')
        })
    })

    EventMachine.register('need_to_update_statistics', function(){
        var items = $('.keyword-item')
        var count = 0,
            totalFreq = 0
        items.each(function(){ totalFreq += parseInt($(this).attr('frequence')); count += 1; })
        $('.panel-footer-keywords-statistic').eq(0).text("{0} / {1}".format(count, totalFreq))
    })

    EventMachine.register('Move_selected_ungrouped_keywords_to_trash', function(){
        var selected = $('.keyword-item input:checked').parents('li');
        var ids = [];
        selected.each(function(){ ids.push($(this).attr('keyId')) })
        selected.remove()

        EventMachine.send('ServerRequest move_keywords_to_trash', ids)
    })

    EventMachine.register('Move_selected_keywords_from_trash', function(){
        var selected = $('.keyword-item input:checked').parents('li');
        var ids = [];
        selected.each(function(){ ids.push($(this).attr('keyId')) })
        selected.remove()

        asyncRequest({project_id: projectId, action: 'restore_from_trash', ids: ids}, function (resp) {
            console.log(resp);
        })
    })

    EventMachine.register('ServerRequest unset_groupId_for_keys_by_ids', function(ids){
        asyncRequest({project_id: projectId, action: 'unset_group_ids', ids: ids}, function (resp) {
            updateCurrentDataset()
        })
    })

    EventMachine.register('ServerRequest move_keywords_to_trash', function(ids){
        asyncRequest({project_id: projectId, action: 'move_to_trash', ids: ids}, function (resp) {
            console.log(resp);
        })
    })

    EventMachine.register('change_selected_keywords_set', function(){
        var selected = $('.keyword-item input:checked').parents('li')
        if (selected.length > 0) {
            var total = 0
            selected.each(function(){
                total += parseInt($(this).attr('frequence'))
            })
            var infoBlock = $('.panel-footer-keywords').eq(0).find('.panel-footer-keywords-info').eq(0)
            if (infoBlock.length == 0) {
                infoBlock = $('<span class="label label-primary panel-footer-keywords-info center-content"></span>')
                $('.panel-footer-keywords').eq(0).append(infoBlock)
            }
            infoBlock.eq(0).text(selected.length + ' / ' + total)
        } else {
            $('.panel-footer-keywords-info').remove()
        }
    })

}

function createKeymap() {
    function addHotckeyButton(forKeys, hotkey, title, desc, func) {
        var button = $('<button role="button" class="btn btn-xs btn-default">' + title + '</button>')
        button.on('click', func)
        button.prop('title', desc)
        $('.panel-footer-' + (forKeys?'keywords':'groups')).eq(0).append(button)
        key(hotkey, func);
    }
    function keyButton(hotkey, title, desc, func) {addHotckeyButton(true, hotkey, title, desc, func)}
    function groupButton(hotkey, title, desc, func) {addHotckeyButton(false, hotkey, title, desc, func)}

    ///////////////////////////////////////////

    key('⌘+1', function(){
        $('a[href="#free_keywords"]').click()
        return false;
    })

    key('⌘+2', function(){
        $('a[href="#grouped_keywords"]').click()
        return false;
    })

    key('⌘+3', function(){
        $('a[href="#blacklist_keywords"]').click()
        return false;
    })

    ///////////////////////////////////////////

    keyButton('⌘+A, ctrl+A', '⌘+A', 'Выделить всё', function(){
        $('.keyword-item input[type=checkbox]').prop('checked', true)
        EventMachine.send('change_selected_keywords_set')
        return false;
    })

    keyButton('⌘+C, ctrl+C', '⌘+C', 'Создать группу', function(){
        EventMachine.send('TreeViewEvent Move_selected_ungrouped_keywords_to_droppable_zone')
        EventMachine.send('treeUpdated')
        return false;
    })

    keyButton('⌘+⌥+C, ctrl+alt+C', '⌘+⌥+C', 'Дополнить последнюю круппу', function(){
        EventMachine.send('TreeViewEvent Move_selected_ungrouped_keywords_to_last_modified_group')
        EventMachine.send('treeUpdated')
        return false;
    })

    keyButton('⌘+D, ctrl+D', '⌘+D', 'Снять выделение', function(){
        $('.keyword-item input[type=checkbox]').prop('checked', false)
        EventMachine.send('change_selected_keywords_set')
        return false;
    })

    keyButton('⌘+G, ctrl+G', '⌘+G', 'В корзину', function(){
        EventMachine.send('Move_selected_ungrouped_keywords_to_trash')
        return false;
    })

    keyButton('⌘+⌥+G, ctrl+alt+G', '⌘+⌥+G', 'Восстановить', function(){
        EventMachine.send('Move_selected_keywords_from_trash')
        return false;
    })

    //////////////////////////////////////////

    groupButton('⌘+S, ctrl+S', 'Создать группу', function(){
        EventMachine.send('Show_modal_for_section_creating')
        return false;
    })

}

function createSpecialActions() {
    $('.addNewKeywordsToProject-submit').on('click', function(){
        var text = $('#addNewKeywordsToProjectTextarea').val()
        $('#addNewKeywordsToProjectTextarea').val('')
        $('#addNewKeywordsToProject').modal('hide')
        asyncRequest({project_id: projectId, action: 'create_phrases', phrases_raw_text: text}, function (resp) {
            updateCurrentDataset();
        })
    })

    $('.js-export-for-tz').on('click', function(){


        var sectionNames = clusterNamespace.treeView.getRootSectionNames()
        var root = $('<div/>')
        for (var i in sectionNames) {
            var name = sectionNames[i]
            var $input = $('<input class="select-section-names" type="checkbox" checked />').val(name)
            var item = $('<label></label>').append($input).append(name)
            root.append(item).append($('<br/>'))
        }
        var button = $('<button type="button" class="btn btn-primary">Показать</button>')
        button.on('click', function(){
            var checkedNames = []
            $('.select-section-names:checked').each(function(){
                checkedNames.push($(this).val())
            })

            var data = clusterNamespace.treeView.exportForTZ(checkedNames)
            var textarea = $('<textarea style="margin: 20px 0" class="form-control" rows="5"></textarea>')
            textarea.val(data)
            $('.js-export-for-tz').eq(0).parent().append(textarea)
            root.empty()
        })
        root.append(button)

        $(this).parent().append(root)
    })
}

$(document).ready(function () {

    projectId = $("#project_id").val();

    if ($('.JS-Clustering').length != 0) {
        initTabSwitching();
        initKeywordFilters();
        initSwitchPanelsButton();
        createTreeView();
        createCommonActions();
        createSpecialActions();
        createKeymap()
        $("#start_tab").click()
    }

    $('.autoresizeTextarea').autosize()

});

