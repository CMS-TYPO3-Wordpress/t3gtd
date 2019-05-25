
config {
  prefixLocalAnchors = all
  tx_realurl_enable = 1
  moveJsFromHeaderToFooter = 1
}

plugin{
  tx_t3gtd_frontendgtd {
    view {
      templateRootPaths.0 = EXT:t3gtd/Resources/Private/Templates/
      templateRootPaths.1 = {$plugin.tx_t3gtd_frontendgtd.view.templateRootPath}
      partialRootPaths.0 = EXT:t3gtd/Resources/Private/Partials/
      partialRootPaths.1 = {$plugin.tx_t3gtd_frontendgtd.view.partialRootPath}
      layoutRootPaths.0 = EXT:t3gtd/Resources/Private/Layouts/
      layoutRootPaths.1 = {$plugin.tx_t3gtd_frontendgtd.view.layoutRootPath}
    }
    persistence {
      storagePid = {$plugin.tx_t3gtd_frontendgtd.persistence.storagePid}
    }
    features {
      requireCHashArgumentForActionArguments = 0
      skipDefaultArguments = 0
    }
    mvc {
      #callDefaultActionIfActionCantBeResolved = 1
    }
  }
}


plugin.tx_t3gtd._CSS_DEFAULT_STYLE (
    textarea.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    input.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    #tx-gtd .nav > li > a {
        padding: 5px 10px !important;
    }

    #tx-gtd hr {
      margin-top: 10px !important;
      margin-bottom: 10px !important;
    }

    .typo3-messages div.typo3-message {
      border: 1px solid rgba(0, 0, 0, 0);
      border-radius: 4px;
      margin-bottom: 20px;
      padding: 15px;
    }
    .typo3-messages div.message-ok {
        background-color: #DFF0D8;
        border-color: #D6E9C6;
        color: #3C763D;
    }
    .typo3-messages div.message-information {
        background-color: #D9EDF7;
        border-color: #BCE8F1;
        color: #31708F;
    }
    .typo3-messages div.message-warning {
        background-color: #FCF8E3;
        border-color: #FAEBCC;
        color: #8A6D3B;
    }
    .typo3-messages div.message-error {
        background-color: #F2DEDE;
        border-color: #EBCCD1;
        color: #A94442;
    }
    a.categoryNode {
      line-height: 1.8em !important;
      width: 100%;
    }
)

page.includeJSFooterlibs {
  fileuploadlib1 = EXT:t3gtd/Resources/Public/Js/jquery.knob.js
  fileuploadlib3 = EXT:t3gtd/Resources/Public/Js/jquery.iframe-transport.js
  fileuploadlib4 = EXT:t3gtd/Resources/Public/Js/jquery.fileupload.js
  fileuploadlib5 = EXT:t3gtd/Resources/Public/Js/bootstrap-filestyle.min.js
}

page.jsFooterInline.9999 = TEXT
page.jsFooterInline.9999.value (
  function allowDropOfTask(event) {
      event.preventDefault();
  }

  function allowDrop2Project(event){
      event.preventDefault();
  }

  function handleProjectDragStart(event) {
      var myId = event.originalEvent.target.id;
      event.originalEvent.dataTransfer.setData("project", myId);
      event.originalEvent.dataTransfer.setData("dragtype", "project");
  }

  function handleDragTaskStart(event) {
    var myId = event.originalEvent.target.id;
    event.originalEvent.dataTransfer.setData("dragtype", "task");
    event.originalEvent.dataTransfer.setData("task", myId);
    var data_target_move_to_inbox = event.originalEvent.target.getAttribute("data-target-move-to-inbox");
    var data_target_move_to_today = event.originalEvent.target.getAttribute("data-target-move-to-today");
    var data_target_move_to_next = event.originalEvent.target.getAttribute("data-target-move-to-next");
    var data_target_move_to_waiting = event.originalEvent.target.getAttribute("data-target-move-to-waiting");
    var data_target_move_to_someday = event.originalEvent.target.getAttribute("data-target-move-to-someday");
    var data_target_move_to_completed = event.originalEvent.target.getAttribute("data-target-move-to-completed");
    var data_target_move_to_trash = event.originalEvent.target.getAttribute("data-target-move-to-trash");
    var data_target_move_to_project = event.originalEvent.target.getAttribute("data-target-move-to-project");
    event.originalEvent.dataTransfer.setData("data-target-move-to-inbox", data_target_move_to_inbox);
    event.originalEvent.dataTransfer.setData("data-target-move-to-today", data_target_move_to_today);
    event.originalEvent.dataTransfer.setData("data-target-move-to-next", data_target_move_to_next);
    event.originalEvent.dataTransfer.setData("data-target-move-to-waiting", data_target_move_to_waiting);
    event.originalEvent.dataTransfer.setData("data-target-move-to-someday", data_target_move_to_someday);
    event.originalEvent.dataTransfer.setData("data-target-move-to-completed", data_target_move_to_completed);
    event.originalEvent.dataTransfer.setData("data-target-move-to-trash", data_target_move_to_trash);
    event.originalEvent.dataTransfer.setData("data-target-move-to-project", data_target_move_to_project);
  }

  $(document).ready( function() {
      /*
      $("#taskDueDate").datepicker({
          dateFormat: 'yy-mm-dd',
          constrainInput: false
      });
      */
      $(".task_draggable").on("dragstart", handleDragTaskStart).attr("draggable","true");
      $(".project_draggable").on("dragstart", handleProjectDragStart).attr("draggable","true");
  });
)

