<?php
include("../database/session.php");

$usertype = $_SESSION['user']['usertype'] ?? null;

if (!$usertype) {
  include '../includes/nav/user_navbar.php'; // default for visitors
  return;
}

switch ($usertype) {
  case 'seller':
     include '../includes/nav/seller_navbar.php';
    break;
  case 'supplier':
    include '../includes/nav/supplier_navbar.php';
    break;

  case 'admin':
    include '../includes/nav/admin_navbar.php';
    break;

  default:
    include '../includes/nav/user_navbar.php';
    break;
}
