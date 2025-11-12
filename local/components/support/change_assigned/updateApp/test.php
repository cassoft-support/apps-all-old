<?php

    $resUser = $auth->core->call("user.current",)->getResponseData()->getResult()->getResultData();
    $debug->printR($resUser, 'user');