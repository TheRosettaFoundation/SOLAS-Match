<?php
require($_SERVER['DOCUMENT_ROOT'].'/../includes/smarty.php');

$s->user->destroySession();

header('Location: '.$s->url->server());
