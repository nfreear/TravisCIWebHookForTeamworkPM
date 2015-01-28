<?php
/**
* Travis-CI web hook for Teamwork PM.
*
* @copyright (c) Nick Freear, 25 January 2015.
* @link http://docs.travis-ci.com/user/notifications/#Webhook-notification
* @link https://github.com/rblalock/GithubWebHookForTeamworkPM
*/
ob_start();

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
header( 'Content-Type: text/plain' );

#require_once 'config.php';
define( 'TRAVIS_TOKEN', '*** CHANGE ME ***' );


$prefix = 'iet:'; #preg_quote(_get( 'prefix', '#' ));
$comment_regex = '/' . $prefix . '([A-Za-z0-9_]+)/';

//$tr_headers = apache_request_headers();
$travis_auth = $_SERVER[ 'HTTP_AUTHORIZATION' ]; #$tr_headers[ 'Authorization' ];
$repo_slug   = $_SERVER[ 'HTTP_TRAVIS_REPO_SLUG' ]; #$tr_headers[ 'Travis-Repo-Slug' ]; # nfreear/ou-media-player-test
$req_method = $_SERVER[ 'REQUEST_METHOD' ];
$user_agent = $_SERVER[ 'HTTP_USER_AGENT' ];
$ctype = $_SERVER[ 'CONTENT_TYPE' ];


$test_auth = hash( 'sha256', $repo_slug + TRAVIS_SLUG );

if ($test_auth == $travis_auth) {
    echo 'OK. Travis-CI request verified' . PHP_EOL;
} else {
    echo 'Warning. Travis-CI request not verified' . PHP_EOL;
}
var_dump( $req_method, $travis_auth, $repo_slug, TRAVIS_TOKEN, $ctype );

var_dump( $_SERVER, $_POST );


try {
    // Convert the post data
    $data = isset($_POST[ 'payload' ]) ? urldecode($_POST[ 'payload' ]) : null;
    $postdata = json_decode($data);

    if ($data && $postdata) {
        print_r( 'POST', $postdata );
        
    }

} catch (Exception $ex) {
    _debug( $ex );
    echo $ex->getMessage();
}



function _debug($obj) {
    #if (_get( 'debug' )) {
        print_r($obj);
    #}
}


#if (isset($_GET[ 'mail' ])) {
$body = ob_get_contents();
$b_ok = mail( 'nfreear@yahoo.co.uk', 'Travis-Teamwork-Hook', $body );

echo 'Mail OK?  '. $b_ok .PHP_EOL;

ob_end_flush();
exit();

#End.
?>