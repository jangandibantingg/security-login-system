<?php

include 'ASEngine/AS.php';

ASSession::destroySession();

redirect('login.php');
