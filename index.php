<?php
/**
* Travis-CI web hook for Teamwork PM.
*
* @copyright (c) Nick Freear, 25 January 2015.
* @link http://docs.travis-ci.com/user/notifications/#Webhook-notification
* @link https://github.com/rblalock/GithubWebHookForTeamworkPM
*/

// .htaccess
/*
  <IfModule mod_rewrite.c>
  # Make sure Authorization HTTP header is available to PHP
  # even when running as CGI or FastCGI.
  RewriteEngine on
  RewriteRule ^ - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
  </IfModule>
*/

// config.TEMPLATE.php - CHANGE ME.

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

define( 'DEBUG_MAIL', FALSE );
define( 'DEBUG_MAIL_TO', ' ** CHANGE ME ** ' );

define( 'TRAVIS_TOKEN', ' ** CHANGE ME ** ' );

define( 'TEAMWORK_USER_TOKEN', ' ** CHANGE ME ** ' );
define( 'TEAMWORK_URL', 'https://** CHANGE ME **.teamworkpm.net/tasks/%s/comments.json' );

define( 'HTTP_PROXY', NULL );


define( 'COMMENT_TEMPLATE', '> [Build status: #{BUILD_NUM} {STATUS} (Travis-CI)]({TRAVIS_URL})
> {COMMENT}

* [GitHub: {COMMIT_NAME}]({GITHUB_URL})

Committed by {AUTHOR} on {DATE} _(comment via Travis-CI)_' );

define( 'DATE_FORMAT', 'm/d/Y' );



// ===================================================
#require_once 'config.php';


// Prevent Drupal 6 Cron emails.
foreach (debug_backtrace() as $a) {
  if ($a[ 'function' ] == 'drupal_cron_run') {
    return;
  }
}


ob_start();

header( 'Content-Type: text/plain' );


$prefix = preg_quote(_get( 'prefix', 'iet:' ));
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
var_dump( $req_method, $repo_slug, TRAVIS_TOKEN, $ctype );

var_dump( $_SERVER, $_POST );


try {
    // Convert the post data
    $data = isset($_POST[ 'payload' ]) ? urldecode($_POST[ 'payload' ]) : null;
    $postdata = json_decode($data);

    if ($data && $postdata) {
        print_r( 'POST', $postdata );

		$build = $postdata;

		// Get any commit messages that have a # tag (points to a resource ID in Teamwork)
        preg_match_all( $comment_regex, $build->message, $matches );

        // Remove the first index since it's the original
        $resourceID = array_pop( $matches );
        // Format the message that will post to Teamwork
        $body = strtr( COMMENT_TEMPLATE, array(
                    '{STATUS}'  => $build->status_message,
					'{BUILD_NUM}'  => $build->number,
					'{TRAVIS_URL}' => $build->build_url,
					'{COMMENT}' => $build->message,
                    '{GITHUB_URL}'  => $build->repository->url .'/commit/'. $build->commit,
                    '{COMMIT_NAME}' => $repo_slug .'@'. substr( $build->commit, 0, 7 ),
                    '{AUTHOR}'  => $build->author_name,
                    '{DATE}'    => date( DATE_FORMAT, strtotime( $build->committed_at ))
		));

        $params = array(
            'comment' => array(
                'body' => $body
            )
        );

		var_dump( $params, $resourceID );
    }

} catch (Exception $ex) {
    _debug( $ex );
    echo $ex->getMessage();
}


function _get( $key, $default = NULL ) {
    return isset($_GET[ $key ]) ? $_GET[ $key ] : $default;
}

function _debug($obj) {
    #if (_get( 'debug' )) {
        print_r($obj);
    #}
}


if (DEBUG_MAIL) { #if (isset($_GET[ 'mail' ])) {
  $body = ob_get_contents();
  $b_ok = mail( DEBUG_MAIL_TO, 'Travis-Teamwork-Hook', $body );

  echo 'Mail OK?  '. $b_ok .PHP_EOL;
}

ob_end_flush();
exit();


#End.


/*
<img alt="" class="gh-mark" src="https://assets-cdn.github.com/images/modules/logos_page/GitHub-Mark.png" width="32">

![](https://assets-cdn.github.com/images/modules/logos_page/GitHub-Mark.png)

![](https://assets.github.com/images/icons/emoji/octocat.png)

![](http://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Octicons-mark-github.svg/20px-Octicons-mark-github.svg.png)
*/
?>