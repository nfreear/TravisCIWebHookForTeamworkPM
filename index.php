<?php
/**
* Travis-CI web hook for Teamwork PM.
*
* @copyright © Nick Freear, 25 January 2015.
* @link http://docs.travis-ci.com/user/notifications/#Webhook-notification
* @link https://github.com/rblalock/GithubWebHookForTeamworkPM
*/

require_once 'config.php';
require_once 'web-hook-utils.php';


_block_robots();
_prevent_drupal_cron_email();

ob_start();

$request = _init_request();

if ($request->auth_success) {
    echo 'OK. Travis-CI request verified' . PHP_EOL;
} else {
    echo 'Warning. Travis-CI request not verified' . PHP_EOL;
}
var_dump( 'Request', $request );

//var_dump( $_SERVER, $_POST );


try {
    // Convert the post data
    $postdata = $build = _post_payload_decoded();

    if ($postdata) {
        print_r( 'POST', $postdata );

        // Get any commit messages that have a # tag (points to a resource ID in Teamwork)
        preg_match_all( $request->comment_regex, $build->message, $matches );

        // Remove the first index since it's the original
        $resourceID = array_pop( $matches );

        $params = _format_teamwork_comment( $request, $build );

        var_dump( $params, $resourceID );
    }

} catch (Exception $ex) {
    _debug( $ex );
    echo $ex->getMessage();
}



if (DEBUG_MAIL) { #if (isset($_GET[ 'mail' ])) {
  $body = ob_get_contents();
  $b_ok = mail( DEBUG_MAIL_TO, 'Travis-Teamwork-Hook', $body );

  echo 'Mail OK?  '. $b_ok .PHP_EOL;
}

ob_end_flush();
exit();


//End.
