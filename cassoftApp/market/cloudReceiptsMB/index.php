<div id="app" style="width: 100%; ">
    <!--<script type="text/javascript" src="js/application.js"></script>-->
    <script src="//api.bitrix24.com/api/v1/"></script>
    <?php
        define("NO_KEEP_STATISTIC", true);
        define("NOT_CHECK_PERMISSIONS", true);
        //style="height: 3500px;"
        require_once("application.php");

    ?>
</div>

<style>

    body {
        background-image: url(/pub/images/app/cover.jpg);
    }
</style>

<script>

    function resizeFrame() {
        var currentSize = BX24.getScrollSize();
        minHeight = currentSize.scrollHeight;
        var FrameWidth = document.getElementById("app").offsetWidth;
        if (minHeight < 300) minHeight = 300;
        BX24.resizeWindow(FrameWidth, minHeight);
    }


    $(document).ready(function () {
        BX24.init(function () {
            resizeFrame();

        });
    });
</script>
