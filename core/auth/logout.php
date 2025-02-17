<?php
session_start();
session_destroy();
header("Location: /znahidka/?page=login");
exit;

