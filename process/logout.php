<?php
require('../private/includes/smarty.php');

$s->user->destroySession();

header('Location: '.$s->url->server());
