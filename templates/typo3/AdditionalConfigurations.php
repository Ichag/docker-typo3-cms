<?php

$TYPO3_CONF_VARS['BE']['sessionTimeout'] = 7200;
$TYPO3_CONF_VARS['SYS']['trustedHostsPattern'] = '.*';


$includeFile = dirname(__FILE__) . '/Secrets.php';
if (file_exists($includeFile)) {
  @include($includeFile);
} else {
  die( $includeFile .' does not exists! This file stores Database Params etc.');
}
$includeFile = dirname(__FILE__) . '/LocalConfiguration.' . php_uname('n') . '.php';
if (file_exists($includeFile)) {
  @include($includeFile);
}
