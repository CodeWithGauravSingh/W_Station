<?php
// redirect.php

// Specify the target page within the same directory
$targetPage = './user/index.php';

// Perform the redirect
header("Location: $targetPage");
exit;
