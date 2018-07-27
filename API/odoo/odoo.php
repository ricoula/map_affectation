<?php
    require_once('ripcord.php');

    $url = "http://openerp.ambitiontelecom.ovh";
    $db = "ambigroup_prod8";
    $username = "admin";
    $password = "NTSi6874ERP";

    $common = ripcord::client("$url/xmlrpc/2/common");
    
    $uid = $common->authenticate($db, $username, $password, array());
    $models = ripcord::client("$url/xmlrpc/2/object");
	/*echo json_encode($models->execute_kw($db, $uid, $password,
    'ag.poi', 'search',
    array(array(array('id', '>', 1000))),
    array('offset'=>10, 'limit'=>5)));*/
?>

<?php
/*
require_once 'openerp.php';


// Instanciate with $url, and $dbname
$oe = new OpenERP("http://openerp.ambitiontelecom.ovh", "ambigroup_prod8");

// login with $url and $dbname
$oe->login("admin", "NTSi6874ERP");

echo "Logged in (session id: " . $oe->session_id . ")";

// Query with direct object method which are mapped to json-rpc calls
$partners = $oe->write(array(
  'model' => 'ag.poi',
  'ids' => array(189552),
  'values' => array(array('id', '=', 189552)),
));

echo "<ul>";
foreach($partners['records'] as $partner) {
   echo "    <li>" . $partner["id"] . "</li>\n";
}
echo "</ul>";
*/
?>