<?php
/**
 * SQL configuration
 */
session_start();

/**
 * Keeps a logged out user from using browser navigation to look at data preceding the logout action, enforcing that they login again.
 */
if(session_destroy()) {
  header("Location: Login.html");
}
?>
