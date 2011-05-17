<?php
require('../private/includes/smarty.php');

$s->users->logOut();

header('Location: '.$s->url->server());
