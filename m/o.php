<?php
// Onzichtbaar beeldje in de mail aan zaakvoerders: meldt "geopend" voor die mail.
// Let op: sommige mailprogramma's (Apple Mail) laden beeldjes vooraf, dan lijkt de mail geopend terwijl dat niet zo is.
$t = isset($_GET['t']) ? $_GET['t'] : '';
if (preg_match('/^[a-f0-9]{32}$/', $t)) {
  $body = json_encode(array('p_token' => $t, 'p_kind' => 'geopend'));
  $url = 'https://vbxbtkkbeutmaqcfcotn.supabase.co/rest/v1/rpc/lead_track';
  $head = array('apikey: sb_publishable_UiRbAIliOFBxLkNiWf7qVw_a2ciV8d3', 'Content-Type: application/json');
  if (function_exists('curl_init')) {
    $c = curl_init($url);
    curl_setopt_array($c, array(CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body, CURLOPT_HTTPHEADER => $head,
      CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 3));
    curl_exec($c);
    curl_close($c);
  } else {
    $ctx = stream_context_create(array('http' => array('method' => 'POST', 'timeout' => 3, 'ignore_errors' => true,
      'header' => implode("\r\n", $head) . "\r\n", 'content' => $body)));
    @file_get_contents($url, false, $ctx);
  }
}
header('Content-Type: image/gif');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
