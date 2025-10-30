<?php
require_once __DIR__ . '/../config/app.php';

session_start();
session_destroy();

header('Location: ' . url_for('index.php'));
exit();