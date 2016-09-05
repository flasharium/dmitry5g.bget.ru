log = console.log;

var myApp;
myApp = myApp || (function () {
  var pleaseWaitDiv = $('<div class="modal fade bs-example-modal-sm" tabindex="-1" aria-labelledby="mySmallModalLabel"> <div class="modal-dialog modal-sm center-block" role="document"> <div class="modal-content center-block"> <h2 class="center-block"> <span class="badge"><span class="glyphicon glyphicon-cloud"></span></span> Loading...</h2> </div> </div> </div>');
  var needToShow = false;
  return {
    showPleaseWait: function() {
      needToShow = true;
      setTimeout(function(){
        if (needToShow) {
          pleaseWaitDiv.modal();
        }
      }, 1000)

    },
    hidePleaseWait: function () {
      needToShow = false;
      pleaseWaitDiv.modal('hide');
    }
  };
})();

function isTouchDevice() {
  return 'ontouchstart' in document.documentElement;
}

function asyncRequest(data, onSuccess) {
  myApp.showPleaseWait();

  $.ajax({
    type: 'POST',
    url: '/clustering/async.php',
    data: data,
    success: function(responce){
      myApp.hidePleaseWait();
      onSuccess(responce)
    },
    error: function(a,b,c) {
      myApp.hidePleaseWait();
      alert("error: " + a.responseText);
      console.log({a:a,b:b,c:c})
    }
  });
}

function createTreeView(isTouchDevice) {
  asyncRequest({ project_id: projectId, action: 'list_groups'}, function(resp){
    var treeView = new TreeView(isTouchDevice);
    treeView.setView($(".keywords-groups").eq(0));
    treeView.setData(resp);
    treeView.redraw()
  })
}

function buildDraggableUI() {
  $( ".keyword-item" ).draggable({
    distance: 20,
    revert: "invalid",
    zIndex: 100,
    cursorAt: { top: -5, left: -5 },
    containment: "window",
    helper: function(){
      var selected = $('.keyword-item input:checked').parents('li');
      if (selected.length === 0) {
        selected = $(this);
      }
      var container = $('<div></div>').attr('id', 'draggingContainer');
      container.append(selected.clone());
      return container;
    }
  });


}

function updateTabWithDataset(dataset) {
  var filters = [];
  $('.keyword-filter').each(function(){
    filters.push($(this).val())
  });

  var colors = [
    '#d0dcff', '#d2ffd2', '#ffe290', '#ffc7c7'
  ]

  asyncRequest({ project_id: projectId, action: 'list_dataset', dataset: dataset, filter: filters}, function(resp){

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
          res += parts[p] + (p == parts.length-1 ? '' : '<span style="background-color:' + colors[f] + '">' + word + '</span>')
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

    var root = $('#' + dataset).empty().append(ul);
    $( "ul, li" ).disableSelection();

    ul.find(".keyword-item").each(function(item){
      var self = $(this);
      var checkbox = self.find("input[type=checkbox]").eq(0);
      checkbox.on('change', function(val){
        self.toggleClass('list-item-checked').toggleClass('ui-draggable-dragging ')
      })
    });

    if (isTouchDevice()) {
      $('.tab-content').prepend($(".tab-filters").eq(0))
    } else {
      buildDraggableUI()
    }

    $(this).tab('show');

  });
}

function initSwitchPanelsButton() {
  $('.button-switch-panels').on('click', function(){
    $('.root-item').each(function(){
      $(this).toggle();
    })
  });
}

function initKeywordFilters() {
  $('.keyword-filter').each(function(){
    $(this).keypress(function(e) {
      if (e.which == 13) {
        e.preventDefault();
        var dataset = $("#myTabs").find(".active a").attr('aria-controls');
        updateTabWithDataset(dataset)
      }
    })
  });

  $('.keyword-filter-clear').on('click', function(){
    $(this).parents('.input-group').eq(0).find('.keyword-filter').eq(0).val('');
    var dataset = $("#myTabs").find(".active a").attr('aria-controls');
    updateTabWithDataset(dataset)
  });
}

function initTabSwitching() {
  $('#myTabs').find('a').click(function (e) {
    e.preventDefault();
    var dataset = $(this).attr('aria-controls');
    updateTabWithDataset(dataset);
  });
}

$(document).ready(function() {

  projectId = $("#project_id").val();

  initTabSwitching();
  initKeywordFilters();
  initSwitchPanelsButton();
  createTreeView(isTouchDevice());

  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

  $("#start_tab").click()

});

