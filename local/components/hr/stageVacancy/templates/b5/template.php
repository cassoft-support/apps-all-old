<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Hello, Bootstrap Table!</title>

    <link href="/local/components/brokci_report/plan_edit/css/bootstrap.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/local/components/brokci_report/plan_edit/templates/b5/css/bootstrap-table.min.css">
  </head>
  <body>
  <div class="bootstrap-table bootstrap5">
      <div class="fixed-table-toolbar"><div class="bs-bars float-left"><div id="toolbar">
  <button id="remove" class="btn btn-danger" disabled="">
    <i class="fa fa-trash"></i> Delete
  </button>
</div></div><div class="columns columns-right btn-group float-right"><button class="btn btn-secondary" type="button" name="paginationSwitch" aria-label="Скрыть/Показать постраничную навигацию" title="Скрыть/Показать постраничную навигацию"><i class="bi bi-caret-down-square"></i> </button><button class="btn btn-secondary" type="button" name="refresh" aria-label="Обновить" title="Обновить"><i class="bi bi-arrow-clockwise"></i> </button><button class="btn btn-secondary" type="button" name="toggle" aria-label="Показать записи в виде карточек" title="Показать записи в виде карточек"><i class="bi bi-toggle-off"></i> </button><button class="btn btn-secondary" type="button" name="fullscreen" aria-label="Полноэкранный режим" title="Полноэкранный режим"><i class="bi bi-arrows-move"></i> </button><div class="keep-open btn-group" title="Колонки">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-label="Columns" title="Колонки">
            <i class="bi bi-list-ul"></i>
            
            <span class="caret"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-right"><label class="dropdown-item dropdown-item-marker"><input type="checkbox" class="toggle-all" checked="checked"> <span>Выбрать все</span></label><div class="dropdown-divider"></div><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="id" value="1" checked="checked"> <span>Item ID</span></label><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="name" value="2" checked="checked"> <span>Item Name</span></label><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="price" value="3" checked="checked"> <span>Item Price</span></label><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="operate" value="4" checked="checked"> <span>Item Operate</span></label></div></div>
                <div class="export btn-group">
                <button class="btn btn-secondary dropdown-toggle" aria-label="Export" data-bs-toggle="dropdown" type="button" title="Экспортировать данные">
                <i class="bi bi-download"></i>
                
                <span class="caret"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item " href="#" data-type="json">JSON</a><a class="dropdown-item " href="#" data-type="xml">XML</a><a class="dropdown-item " href="#" data-type="csv">CSV</a><a class="dropdown-item " href="#" data-type="txt">TXT</a><a class="dropdown-item " href="#" data-type="sql">SQL</a><a class="dropdown-item " href="#" data-type="excel">MS-Excel</a></div></div></div>
        <div class="float-right search btn-group">
          <input class="form-control
        
        search-input" type="search" placeholder="Поиск" autocomplete="off">
        </div>
      </div>
      
      <div class="fixed-table-container fixed-height has-footer" style="height: 416px; padding-bottom: 185.5px;">
      <div class="fixed-table-header" style="margin-right: 15px;"><table class="table table-bordered table-hover" style="width: 1881px;"><thead class=""><tr><th class="detail" rowspan="2">
          <div class="fht-cell" style="width: 40px;"></div>
          </th><th class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; " rowspan="2" data-field="state"><div class="th-inner "><label><input name="btSelectAll" type="checkbox"><span></span></label></div><div class="fht-cell" style="width: 43px;"></div></th><th style="text-align: center; vertical-align: middle; " rowspan="2" data-field="id"><div class="th-inner sortable both">Item ID</div><div class="fht-cell" style="width: 363.047px;"></div></th><th style="text-align: center; " colspan="3"><div class="th-inner ">Item Detail</div><div class="fht-cell"></div></th></tr><tr><th style="text-align: center; " data-field="name" data-not-first-th=""><div class="th-inner sortable both">Item Name</div><div class="fht-cell" style="width: 475.734px;"></div></th><th style="text-align: center; " data-field="price"><div class="th-inner sortable both">Item Price</div><div class="fht-cell" style="width: 454.609px;"></div></th><th style="text-align: center; " data-field="operate"><div class="th-inner ">Item Operate</div><div class="fht-cell" style="width: 497.609px;"></div></th></tr></thead></table></div>
      <div class="fixed-table-body">
      <div class="fixed-table-loading table table-bordered table-hover fixed-table-border" style="top: 123px; width: 1881px;">
      <span class="loading-wrap">
      <span class="loading-text" style="font-size: 32px;">Пожалуйста, подождите, идёт загрузка</span>
      <span class="animation-wrap"><span class="animation-dot"></span></span>
      </span>
    
      </div>
      <table id="table" data-toolbar="#toolbar" data-search="true" data-show-refresh="true" data-show-toggle="true" data-show-fullscreen="true" data-show-columns="true" data-show-columns-toggle-all="true" data-detail-view="true" data-show-export="true" data-click-to-select="true" data-detail-formatter="detailFormatter" data-minimum-count-columns="2" data-show-pagination-switch="true" data-pagination="true" data-id-field="id" data-page-list="[10, 25, 50, 100, all]" data-show-footer="true" data-side-pagination="server" data-url="https://examples.wenzhixin.net.cn/examples/bootstrap_table/data" data-response-handler="responseHandler" class="table table-bordered table-hover" style="margin-top: -122.5px;">
<thead class=""><tr><th class="detail" rowspan="2">
          <div class="fht-cell"></div>
          </th><th class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; " rowspan="2" data-field="state"><div class="th-inner "><label><input name="btSelectAll" type="checkbox"><span></span></label></div><div class="fht-cell"></div></th><th style="text-align: center; vertical-align: middle; " rowspan="2" data-field="id"><div class="th-inner sortable both">Item ID</div><div class="fht-cell"></div></th><th style="text-align: center; " colspan="3"><div class="th-inner ">Item Detail</div><div class="fht-cell"></div></th></tr><tr><th style="text-align: center; " data-field="name" data-not-first-th=""><div class="th-inner sortable both">Item Name</div><div class="fht-cell"></div></th><th style="text-align: center; " data-field="price"><div class="th-inner sortable both">Item Price</div><div class="fht-cell"></div></th><th style="text-align: center; " data-field="operate"><div class="th-inner ">Item Operate</div><div class="fht-cell"></div></th></tr></thead><tbody><tr data-index="0" data-has-detail-view="true"><td>
          <a class="detail-icon" href="#">
          <i class="bi bi-plus"></i>
          </a>
        </td><td class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; "><label>
            <input data-index="0" name="btSelectItem" type="checkbox" value="0">
            <span></span>
            </label></td><td style="text-align: center; vertical-align: middle; ">0</td><td style="text-align: center; ">Item 0</td><td style="text-align: center; ">$0</td><td style="text-align: center; "><a class="like" href="javascript:void(0)" title="Like"><i class="fa fa-heart"></i></a>  <a class="remove" href="javascript:void(0)" title="Remove"><i class="fa fa-trash"></i></a></td></tr><tr data-index="1" data-has-detail-view="true"><td>
          <a class="detail-icon" href="#">
          <i class="bi bi-plus"></i>
          </a>
        </td><td class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; "><label>
            <input data-index="1" name="btSelectItem" type="checkbox" value="1">
            <span></span>
            </label></td><td style="text-align: center; vertical-align: middle; ">1</td><td style="text-align: center; ">Item 1</td><td style="text-align: center; ">$1</td><td style="text-align: center; "><a class="like" href="javascript:void(0)" title="Like"><i class="fa fa-heart"></i></a>  <a class="remove" href="javascript:void(0)" title="Remove"><i class="fa fa-trash"></i></a></td></tr><tr data-index="2" data-has-detail-view="true"><td>
          <a class="detail-icon" href="#">
          <i class="bi bi-plus"></i>
          </a>
        </td><td class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; "><label>
            <input data-index="2" name="btSelectItem" type="checkbox" value="2">
            <span></span>
            </label></td><td style="text-align: center; vertical-align: middle; ">2</td><td style="text-align: center; ">Item 2</td><td style="text-align: center; ">$2</td><td style="text-align: center; "><a class="like" href="javascript:void(0)" title="Like"><i class="fa fa-heart"></i></a>  <a class="remove" href="javascript:void(0)" title="Remove"><i class="fa fa-trash"></i></a></td></tr><tr data-index="3" data-has-detail-view="true"><td>
          <a class="detail-icon" href="#">
          <i class="bi bi-plus"></i>
          </a>
        </td><td class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; "><label>
            <input data-index="3" name="btSelectItem" type="checkbox" value="3">
            <span></span>
            </label></td><td style="text-align: center; vertical-align: middle; ">3</td><td style="text-align: center; ">Item 3</td><td style="text-align: center; ">$3</td><td style="text-align: center; "><a class="like" href="javascript:void(0)" title="Like"><i class="fa fa-heart"></i></a>  <a class="remove" href="javascript:void(0)" title="Remove"><i class="fa fa-trash"></i></a></td></tr><tr data-index="4" data-has-detail-view="true"><td>
          <a class="detail-icon" href="#">
          <i class="bi bi-plus"></i>
          </a>
        </td><td class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; "><label>
            <input data-index="4" name="btSelectItem" type="checkbox" value="4">
            <span></span>
            </label></td><td style="text-align: center; vertical-align: middle; ">4</td><td style="text-align: center; ">Item 4</td><td style="text-align: center; ">$4</td><td style="text-align: center; "><a class="like" href="javascript:void(0)" title="Like"><i class="fa fa-heart"></i></a>  <a class="remove" href="javascript:void(0)" title="Remove"><i class="fa fa-trash"></i></a></td></tr><tr data-index="5" data-has-detail-view="true"><td>
          <a class="detail-icon" href="#">
          <i class="bi bi-plus"></i>
          </a>
        </td><td class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; "><label>
            <input data-index="5" name="btSelectItem" type="checkbox" value="5">
            <span></span>
            </label></td><td style="text-align: center; vertical-align: middle; ">5</td><td style="text-align: center; ">Item 5</td><td style="text-align: center; ">$5</td><td style="text-align: center; "><a class="like" href="javascript:void(0)" title="Like"><i class="fa fa-heart"></i></a>  <a class="remove" href="javascript:void(0)" title="Remove"><i class="fa fa-trash"></i></a></td></tr><tr data-index="6" data-has-detail-view="true"><td>
          <a class="detail-icon" href="#">
          <i class="bi bi-plus"></i>
          </a>
        </td><td class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; "><label>
            <input data-index="6" name="btSelectItem" type="checkbox" value="6">
            <span></span>
            </label></td><td style="text-align: center; vertical-align: middle; ">6</td><td style="text-align: center; ">Item 6</td><td style="text-align: center; ">$6</td><td style="text-align: center; "><a class="like" href="javascript:void(0)" title="Like"><i class="fa fa-heart"></i></a>  <a class="remove" href="javascript:void(0)" title="Remove"><i class="fa fa-trash"></i></a></td></tr><tr data-index="7" data-has-detail-view="true"><td>
          <a class="detail-icon" href="#">
          <i class="bi bi-plus"></i>
          </a>
        </td><td class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; "><label>
            <input data-index="7" name="btSelectItem" type="checkbox" value="7">
            <span></span>
            </label></td><td style="text-align: center; vertical-align: middle; ">7</td><td style="text-align: center; ">Item 7</td><td style="text-align: center; ">$7</td><td style="text-align: center; "><a class="like" href="javascript:void(0)" title="Like"><i class="fa fa-heart"></i></a>  <a class="remove" href="javascript:void(0)" title="Remove"><i class="fa fa-trash"></i></a></td></tr><tr data-index="8" data-has-detail-view="true"><td>
          <a class="detail-icon" href="#">
          <i class="bi bi-plus"></i>
          </a>
        </td><td class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; "><label>
            <input data-index="8" name="btSelectItem" type="checkbox" value="8">
            <span></span>
            </label></td><td style="text-align: center; vertical-align: middle; ">8</td><td style="text-align: center; ">Item 8</td><td style="text-align: center; ">$8</td><td style="text-align: center; "><a class="like" href="javascript:void(0)" title="Like"><i class="fa fa-heart"></i></a>  <a class="remove" href="javascript:void(0)" title="Remove"><i class="fa fa-trash"></i></a></td></tr><tr data-index="9" data-has-detail-view="true"><td>
          <a class="detail-icon" href="#">
          <i class="bi bi-plus"></i>
          </a>
        </td><td class="bs-checkbox " style="text-align: center; vertical-align: middle; width: 36px; "><label>
            <input data-index="9" name="btSelectItem" type="checkbox" value="9">
            <span></span>
            </label></td><td style="text-align: center; vertical-align: middle; ">9</td><td style="text-align: center; ">Item 9</td><td style="text-align: center; ">$9</td><td style="text-align: center; "><a class="like" href="javascript:void(0)" title="Like"><i class="fa fa-heart"></i></a>  <a class="remove" href="javascript:void(0)" title="Remove"><i class="fa fa-trash"></i></a></td></tr></tbody></table><div class="fixed-table-border" style="width: 1881px; height: 0px;"></div></div>
      <div class="fixed-table-footer" style="margin-right: 15px;"><table class="table table-bordered table-hover" style="width: 1881px;"><thead><tr><th class="detail"><div class="th-inner"></div><div class="fht-cell" style="width: 40px;"></div></th><th style="text-align: center; vertical-align: middle; "><div class="th-inner"></div><div class="fht-cell" style="width: 43px;"></div></th><th style="text-align: center; vertical-align: middle; "><div class="th-inner">Total</div><div class="fht-cell" style="width: 363.047px;"></div></th><th style="text-align: center; "><div class="th-inner">10</div><div class="fht-cell" style="width: 475.734px;"></div></th><th style="text-align: center; "><div class="th-inner">$45</div><div class="fht-cell" style="width: 454.609px;"></div></th><th style="text-align: center; "><div class="th-inner"></div><div class="fht-cell" style="width: 497.609px;"></div></th></tr></thead></table></div>
      </div>
      <div class="fixed-table-pagination" style=""><div class="float-left pagination-detail"><span class="pagination-info">
      Записи с 1 по 10 из 800
      </span><div class="page-list"><div class="btn-group dropdown dropup">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <span class="page-size">
        10
        </span>
        <span class="caret"></span>
        </button>
        <div class="dropdown-menu"><a class="dropdown-item active" href="#">10</a><a class="dropdown-item " href="#">25</a><a class="dropdown-item " href="#">50</a><a class="dropdown-item " href="#">100</a><a class="dropdown-item " href="#">Все</a></div></div> записей на страницу</div></div><div class="float-right pagination"><ul class="pagination"><li class="page-item page-pre"><a class="page-link" aria-label="предыдущая страница" href="javascript:void(0)">‹</a></li><li class="page-item active"><a class="page-link" aria-label="перейти к странице 1" href="javascript:void(0)">1</a></li><li class="page-item"><a class="page-link" aria-label="перейти к странице 2" href="javascript:void(0)">2</a></li><li class="page-item"><a class="page-link" aria-label="перейти к странице 3" href="javascript:void(0)">3</a></li><li class="page-item"><a class="page-link" aria-label="перейти к странице 4" href="javascript:void(0)">4</a></li><li class="page-item"><a class="page-link" aria-label="перейти к странице 5" href="javascript:void(0)">5</a></li><li class="page-item page-last-separator disabled"><a class="page-link" aria-label="" href="javascript:void(0)">...</a></li><li class="page-item"><a class="page-link" aria-label="перейти к странице 80" href="javascript:void(0)">80</a></li><li class="page-item page-next"><a class="page-link" aria-label="следующая страница" href="javascript:void(0)">›</a></li></ul></div></div>
      </div>

    <script src="/local/components/brokci_report/plan_edit/js/jquery-1.11.2.min.js"></script>
    <script src="/local/components/brokci_report/plan_edit/js/bootstrap.js" ></script>
    <script src="/local/components/brokci_report/plan_edit/templates/b5/js/bootstrap-table.min.js"></script>
  </body>
</html>