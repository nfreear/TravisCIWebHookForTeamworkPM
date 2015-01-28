<?php
/**
*/

function _init_request() {
  header( 'Content-Type: text/plain; charset=utf-8' );

  //$tr_headers = apache_request_headers();
  $req = (object) array(
    'prefix' => preg_quote(_get( 'prefix', 'iet:' )),
    'authorization' => _server( 'HTTP_AUTHORIZATION' ), #$tr_headers[ 'Authorization' ];
    'repo_slug' => _server( 'HTTP_TRAVIS_REPO_SLUG' ), #$tr_headers[ 'Travis-Repo-Slug' ]; # nfreear/ou-media-player-test
    'method' => _server( 'REQUEST_METHOD' ),
    'agent'  => _server( 'HTTP_USER_AGENT' ),
    'ctype'  => _server( 'CONTENT_TYPE' ),
  );
  $req->comment_regex = '/' . $prefix . '([A-Za-z0-9_]+)/';

  $req->auth_test = hash( 'sha256', $req->repo_slug + TRAVIS_SLUG );
  $req->auth_success = ($req->auth_test === $req->authorization);

  return $req;
}


// Format the message that will post to Teamwork
function _format_teamwork_comment( $req, $build ) {
    $body = strtr( COMMENT_TEMPLATE, array(
        '{STATUS}'  => $build->status_message,
        '{BUILD_NUM}'  => $build->number,
        '{TRAVIS_URL}' => $build->build_url,
        '{COMMENT}' => $build->message,
        '{GITHUB_URL}'  => $build->repository->url .'/commit/'. $build->commit,
        '{COMMIT_NAME}' => $req->repo_slug .'@'. substr( $build->commit, 0, 7 ),
        '{AUTHOR}'  => $build->author_name,
        '{DATE}'    => date( DATE_FORMAT, strtotime( $build->committed_at ))
	));
    $params = array(
        'comment' => array(
            'body' => $body
    ));
    return $params;
}


function _post_payload_decoded() {
    $data = urldecode(filter_input( 'payload' ));
    return $data ? json_decode( $data ) : null;
}

function _get( $key, $default = NULL, $filter = FILTER_SANITIZE_STRING ) {
    $value = filter_input( INPUT_GET, $key, $filter );
    return $value ? $value : $default;
}

function _server( $key, $filter = FILTER_SANITIZE_STRING ) {
    return filter_input( INPUT_SERVER, $key, $filter );
}

function _debug($obj) {
    #if (_get( 'debug' )) {
        print_r($obj);
    #}
}


// Prevent Drupal 6 Cron emails.
function _prevent_drupal_cron_email() {
    foreach (debug_backtrace() as $a) {
        if ($a[ 'function' ] == 'drupal_cron_run') {
            return;
        }
    }
}


#https://github.com/bcit-ci/CodeIgniter/blob/develop/application/config/user_agents.php#L193
function _block_robots() {
  $re = '/(Googlebot|MSNBot|Baiduspider|Bing|Bingbot|Yahoo|MJ12bot)/i';
  if (preg_match( $re, _server( 'HTTP_USER_AGENT' ))) {
    header( 'HTTP/1.1 404 Not Found' );
    echo 'Blocking search robot: ' . $ua;
    exit;
  }
}


//End.
