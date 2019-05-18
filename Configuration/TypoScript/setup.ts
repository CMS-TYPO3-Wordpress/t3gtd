
plugin.tx_t3gtd_frontendgtd {
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
    requireCHashArgumentForActionArguments = 1
    #skipDefaultArguments = 1
  }
  mvc {
    #callDefaultActionIfActionCantBeResolved = 1
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
