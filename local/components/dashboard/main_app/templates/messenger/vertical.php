<?php

?>
<link rel="stylesheet" href="/local/lib/bootstrap/bootstrap.css"/>
<link rel="stylesheet" href="/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css"/>
<!--     Fonts and icons     -->
<link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/style.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css"/>
<link rel="stylesheet" href="/local/lib/chosen/chosen.min.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/cs-root-blue.css">
<link rel="stylesheet" href="/local/lib/css/new/menuWhite.css"/>
<link rel="stylesheet" href="/local/components/dashboard/main_app/templates/accountant/menuMob.css"/>
<!--<link rel="stylesheet" href="/local/lib/css/cassoft/panel.css"/>-->
<link rel="stylesheet" href="/local/lib/css/cassoft/brokci-grid.css?020223"/>
<link rel="stylesheet" href="/local/components/dashboard/main_app/css/vertical_menu.css?020223"/>
<link rel="stylesheet" href="/local/components/dashboard/main_app/css/v2.css?020223"/>
<div class="layout has-sidebar fixed-sidebar fixed-header">
    <div class="input-block">
        <input type="hidden" id="member_id" name="member_id" hidden value='<?= $arResult['member_id'] ?>'>
        <input type="text" id="user_id" name="user_id" hidden value='<?= $arResult['user_id'] ?>'>
        <input type="text" id="appCode"  hidden value='messenger'>

    </div>
    <aside id="sidebar" class="sidebar break-point-sm has-bg-image">
        <a id="btn-collapse" class="sidebar-collapser"><i class="ri-arrow-left-s-line"></i></a>
        <div class="image-wrapper">
            <img src="/local/images/hr-pro/hr_logo1.png" alt="sidebar background" />
        </div>

        <div class="sidebar-layout">
            <div class="sidebar-header">
                <div class="pro-sidebar-logo">
                    <div>M</div>
                    <h5>pro</h5>
                </div>
            </div>
            <div class="sidebar-content">
                <nav class="menu open-current-submenu">
                    <ul>
                        <li class="menu-header"><span> Главное </span></li>
<!--                        <li class="menu-item ">-->
<!--                            <a href="#" class="click" value="candidates">-->
<!--                                <span class="menu-icon"> <i class="ri-user-search-fill"></i> </span>-->
<!--                                <span class="menu-title">Соискатели</span>-->
<!--                                <span class="menu-suffix"> <span class="badge primary">Hot</span> </span>-->
<!--                            </a>-->
<!--                        </li>-->
<!--                        <li class="menu-item ">-->
<!--                            <a href="#" class="click" value="vacancy">-->
<!--                                <span class="menu-icon"><i class="ri-vip-diamond-fill"></i>  </span>-->
<!--                                <span class="menu-title">Вакансии</span>-->
<!--                                <span class="menu-suffix"> <span class="badge secondary">Скоро</span>  </span>-->
<!--                            </a>-->
<!--                        </li>-->
                        <li class="menu-header" style="padding-top: 20px"><span> Администрирование </span></li>
                        <li class="menu-item sub-menu">
                            <a href="#">
                                <span class="menu-icon"> <i class="ri-settings-4-line"></i> </span>
                                <span class="menu-title">Настройки</span>
                            </a>
                            <div class="sub-menu-list">
                                <ul>
                                    <li class="menu-item">
                                        <a href="#" class="click" value="general_settings">
                                            <span class="menu-title">Общие настройки</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="#" class="click" value="stageCandidates">
                                            <span class="menu-title">Настройки стадий соискателей</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="#">
                                            <span class="menu-title">Настройки стадий вакансий</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <?php
                        if ($arResult['userFIO'] === 'Черкасов') {
                            ?>
                            <li class="menu-item">
                                <a href="#" class="click" value="update">
                                    <span class="menu-icon"> <i class="ri-service-fill"></i> </span>
                                    <span class="menu-title">Update</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="click" value="support">
                                    <span class="menu-icon"> <i class="ri-service-fill"></i> </span>
                                    <span class="menu-title">Support</span>
                                </a>
                            </li>

                            <?php } ?>

                    </ul>
                </nav>
            </div>
            <div class="sidebar-footer">
                <div class="footer-box" style="display: none;">
                    <div>
                        <img
                            class="react-logo"
                            src="https://user-images.githubusercontent.com/25878302/213938106-ca8f0485-3f30-4861-9188-2920ed7ab284.png"
                            alt="react"
                        />
                    </div>
                    <div style="padding: 0 10px">
                <span style="display: block; margin-bottom: 10px" >HR-pro инструкция по работе</span>
                        <div style="margin-bottom: 15px">
                            <img
                                alt="preview badge"
                                src="https://cassoft.ru?style=social"
                            />
                        </div>
                        <div>
                            <a href="https://cassoft.ru" target="_blank" >CAS-soft</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </aside>


    <div id="overlay" class="overlay"></div>
    <div class="layout">
        <main class="content">
            <a id="btn-toggle" href="#" class="sidebar-toggler break-point-sm">
                <i class="ri-menu-line ri-xl"></i>
            </a>
            <div id='main' class="container-block"></div>

            <footer class="footer">
                <small style="margin-bottom: 20px; display: inline-block">
                    © 2024 разработка компании
                    <a target="_blank" href="https://cassoft.ru"> CAS-soft </a>
                </small>
                <br />
                <div class="social-links" style="display: none;">
                    <a href="https://github.com/azouaoui-med" target="_blank">
                        <i class="ri-github-fill ri-xl"></i>
                    </a>
                    <a href="https://twitter.com/azouaoui_med" target="_blank">
                        <i class="ri-twitter-fill ri-xl"></i>
                    </a>
                    <a href="https://codepen.io/azouaoui-med" target="_blank">
                        <i class="ri-codepen-fill ri-xl"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/mohamed-azouaoui/" target="_blank">
                        <i class="ri-linkedin-box-fill ri-xl"></i>
                    </a>
                </div>
            </footer>
        </main>

        <div class="overlay"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script defer src="//api.bitrix24.com/api/v1/"></script>
<script defer src="/local/lib/js/jquery.maskedinput.js"></script>
<script src="/local/lib/chosen/chosen.jquery.js"></script>
<script src="/local/lib/js/cleave.min.js"></script>
<script src="/local/lib/js/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"></script>
<script type="text/javascript" src="/local/lib/bootstrap/bootstrap.js"></script>
<script type="text/javascript" src="/local/components/logistics/applications/js/bootstrap-table-1.22.1.min.js"></script>
<script defer src="/local/components/dashboard/main_app/js/vertical_menu.js?1238"></script>
<script defer src="/local/components/dashboard/main_app/templates/messenger/script.js?1238"></script>
<script>
    (function(w,d,u){
        var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
        var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
    })(window,document,'https://cdn-ru.bitrix24.ru/b9950371/crm/site_button/loader_5_9bynjt.js');
</script>