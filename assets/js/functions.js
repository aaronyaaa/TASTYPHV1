function timeAgo($datetime) {
  $time = strtotime($datetime);
  $diff = time() - $time;

  if ($diff < 60) return $diff . 's ago';
  elseif ($diff < 3600) return floor($diff / 60) . 'm ago';
  elseif ($diff < 86400) return floor($diff / 3600) . 'h ago';
  else return floor($diff / 86400) . 'd ago';
}
