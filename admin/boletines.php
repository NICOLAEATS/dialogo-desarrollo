<?php
require_once 'config.php';
require_once 'includes/definiciones.php';
require_login();
$nombre_tabla = 'boletines';
include 'includes/render_tabla.php';