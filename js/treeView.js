log = console.log;

draggableParams = {
  containment: "window",
  appendTo: "body",
  helper: "clone",
  revert: "invalid",
  zIndex: 100,
  cursorAt: { top: -5, left: -5 }
}

String.prototype.format = String.prototype.f = function () {
  var args = arguments;
  return this.replace(/\{(\d+)\}/g, function (m, n) {
    return args[n] ? args[n] : m;
  });
};


var TreeView;
TreeView = function (isTouchDevice) {
  var data, view;
  var dictSections, dictGroups, dictKeys;
  var touchscreen = isTouchDevice

  function createDict(array, field) {
    var dict = {};
    for (var i = 0; i < array.length; i++) {
      var item = array[i];
      dict[item[field]] = dict[item[field]] || [];
      dict[item[field]].push(item)
    }

    return dict
  }

  function getKeyData(view) {
    return {
      "id": view.attr("keyId"),
      "phrase": view.attr("phrase"),
      "frequence": view.attr("frequence")
    }
  }

  function createKeyView(key) {
    var view = $("<div class='treeView-key'></div>")
    view.attr('keyId', key.id)
    view.attr('phrase', key.phrase)
    view.attr('frequence', key.frequence)
    view.text("{0} ({1})".format(key.phrase, key.frequence))

    view.draggable(draggableParams)

    return view
  }

  function getKeysFromGroupView(groupView) {
    var keys = []

    groupView.find('.treeView-key').each(function(){
      keys.push(getKeyData($(this)))
    })

    return keys
  }

  function updateGroupView(groupView) {
    var keys = getKeysFromGroupView(groupView)

    if (keys.length == 0) {
      return groupView.remove()
    }

    var primaryKey = keys.reduce(function (prev, cur) {
      return parseInt(prev.frequence) > parseInt(cur.frequence) ? prev : cur
    }, keys[0]);

    var totalFreq = keys.reduce(function (prev, cur) {
      return prev + parseInt(cur.frequence)
    }, 0) || '0';

    var text = "{0} ({1})".format(primaryKey.phrase, totalFreq)
    groupView.find('.treeView-group-header-label').text(text)

    groupView.find('.treeView-key').sort(function (a, b) {
      var contentA = parseInt( $(a).attr('frequence'));
      var contentB = parseInt( $(b).attr('frequence'));
      return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
    })

    groupView.find('.treeView-key').sort(function(a, b) {
      return +$(b).attr('frequence') - +$(a).attr('frequence');
    }).appendTo(groupView);
  }

  function createGroupView(keys) {
    if (!keys) return;
    var groupRoot = $("<div class='treeView-group'></div>");

    for (var i = 0; i < keys.length; i++) {
      groupRoot.append(createKeyView(keys[i]))
    }

    var span = $('<span class="treeView-group-header-label"></span>')
    var header = $("<div class='treeView-group-header glyphicon glyphicon-triangle-bottom'></div>").append(span)
    header.on('click', function () {
      $(this)
        .toggleClass("glyphicon-triangle-bottom")
        .toggleClass("glyphicon-triangle-right")
        .siblings('.treeView-key')
        .slideToggle('fast');
    })
    groupRoot.prepend(header);

    updateGroupView(groupRoot)

    groupRoot
      .draggable(draggableParams)
      .droppable({
        accept: ".treeView-key, .treeView-group, .keyword-item",
        hoverClass: 'bg-blue',
        tolerance: 'pointer',
        drop: function (event, ui) {
          var self = $(this)
          var $item = $(ui.draggable[0])

          if ($item.hasClass('treeView-group')) {

            $item.find('.treeView-key').each(function(){
              self.append($(this))
            })
            updateGroupView(self)
            updateGroupView($item)

          } else if ($item.hasClass('treeView-key')) {

            console.log($item);

            if ($item.parent() != self) {
              var oldGroup = $item.parent()
              self.append($item)
              updateGroupView(oldGroup)
              updateGroupView(self)
            }
            
          } else if ($item.hasClass('keyword-item')) {

            var selected = $('.keyword-item input:checked').parents('li');
            if (selected.length > 0) {
              selected.each(function(){
                var that = $(this)
                if (that.parents('#draggingContainer').length == 0) {
                  var newKey = createKeyView(getKeyData(that))
                  self.append(newKey)
                  that.remove()
                }
              })
            } else {
              var newKey = createKeyView(getKeyData($item))
              $item.remove()
              self.append(newKey)
            }

            updateGroupView(self)

          }
        }
      })

    return groupRoot
  }

  function createSectionView(section) {
    var sectionRoot = $("<div class='treeView-section'></div>");
    sectionRoot.append(
      $("<div class='treeView-section-header glyphicon glyphicon-folder-open'></div>")
        .append($('<span class="treeView-section-header-label"></span>').text(section.title))
    );

    if (dictSections[section.id]) {
      for (var i = 0; i < dictSections[section.id].length; i++) {
        var childSection = dictSections[section.id][i];
        sectionRoot.append(createSectionView(childSection))
      }
    }

    if (dictGroups[section.id]) {
      for (var i = 0; i < dictGroups[section.id].length; i++) {
        var group = dictGroups[section.id][i];
        var keys = dictKeys[group.id]
        sectionRoot.append(createGroupView(keys))
      }
    }

    sectionRoot.draggable(draggableParams)

    sectionRoot.find('.treeView-section-header').eq(0)
      .droppable({
        accept: ".treeView-key, .treeView-section, .treeView-group, .keyword-item",
        hoverClass: 'bg-blue',
        tolerance: 'pointer',
        drop: function (event, ui) {
          var self = sectionRoot
          var $item = $(ui.draggable[0])

          if ($item.hasClass('treeView-section')) {

            self.append($item)

          } else if ($item.hasClass('treeView-group')) {

            self.append($item)

          } else if ($item.hasClass('treeView-key')) {

            var oldGroup = $item.parent()
            var newGroup = [getKeyData($item)]
            $item.remove()
            updateGroupView(oldGroup)
            self.append(createGroupView(newGroup))

          } else if ($item.hasClass('keyword-item')) {

            var newGroup = [getKeyData($item)]
            $item.remove()
            self.append(createGroupView(newGroup))

          }
        }
      })
      .on("click", function () {
        $(this)
          .toggleClass("glyphicon-folder-close")
          .toggleClass("glyphicon-folder-open")
          .siblings('.treeView-group, .treeView-section')
          .slideToggle('fast');
      })

    return sectionRoot
  }

  function createRootView() {
    var root = $("<div class='treeView-root'></div>");
    root.disableSelection()

    var rootSections = dictSections["0"] || []
    for (var i in rootSections) root.append(createSectionView(rootSections[i]));

    var rootGroups = dictGroups["0"] || []
    for (var j in rootGroups) {
      var keys = dictKeys[rootGroups[j].id]
      root.append(createGroupView(keys));
    }

    return root
  }

  function makeDraggable() {

    /*

    1) ключи из списка падают на:
          - Корзину
          - Группу
          - Секцию (создавая группу)
          - treeView-root (создавая группу)

    2) ключи из Групп падают на:
          - Список
          - Корзину
          - другие группы
          - Секцию (создавая группу)
          - treeView-root (создавая группу)

    3) группы падают на:
          - другие секции
          - другие группы (мерж)
          - treeView-root

    4) секции падают на:
          - другие секции
          - treeView-root

     */

    $('.treeView-droppable-zone').droppable({
      accept: ".treeView-key, .treeView-section, .treeView-group, .keyword-item",
      activeClass: 'drop-here',
      hoverClass: 'bg-blue',
      tolerance: 'pointer',
      drop: function (event, ui) {
        console.log(ui);
        var $item = $(ui.draggable[0])

        if ($item.hasClass('treeView-section')) {

          $(".treeView-root").append($item)

        } else if ($item.hasClass('treeView-group')) {

          $(".treeView-root").append($item)

        } else if ($item.hasClass('treeView-key')) {

          var oldGroup = $item.parent()
          var newGroup = [getKeyData($item)]
          $item.remove()
          updateGroupView(oldGroup)
          $(".treeView-root").append(createGroupView(newGroup))

        } else if ($item.hasClass('keyword-item')) {

          var newGroup = [getKeyData($item)]
          $item.remove()
          $(".treeView-root").append(createGroupView(newGroup))

        }
      }
    })

    $('.keywords-trash').droppable({
      accept: ".treeView-key, .treeView-group, .keyword-item",
      hoverClass: 'bg-blue',
      tolerance: 'pointer',
      drop: function (event, ui) {

        var $item = $(ui.draggable[0])

        if ($item.hasClass('treeView-group')) {

          $item.remove()

        } else if ($item.hasClass('treeView-key')) {

          var oldGroup = $item.parent()
          $item.remove()
          updateGroupView(oldGroup)

        } else if ($item.hasClass('keyword-item')) {

          var selected = $('.keyword-item input:checked').parents('li');
          $item.remove()
          selected.remove()

        }
      }
    })

  }

  function createGui() {
    var root = $('.treeView-root').eq(0)

    $('.button-create-section').on('click', function() {
      root.append(createSectionView())
    })
  }


  // public


  this.setView = function (_view) {
    view = _view;
  };

  this.setData = function (_data) {
    data = _data;

    var sections = data.sections || [];
    var groups = data.groups || [];
    var keys = data.keywords || [];

    dictSections = createDict(sections, 'parent_id');
    dictGroups = createDict(groups, 'section_id');
    dictKeys = createDict(keys, 'group_id')
  };

  this.redraw = function () {
    view.empty();
    view.append(createRootView())
    view.append($('<div class="treeView-droppable-zone"><br/><br/><br/></div>'))

    if (!touchscreen) {
      makeDraggable()
    }

    createGui()
  };

};

