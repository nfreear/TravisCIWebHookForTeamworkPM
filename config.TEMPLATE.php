<?php
/**
* Configuration options.
*/

// Debug only!
//error_reporting( E_ALL );
//ini_set( 'display_errors', 1 );


define( 'DEBUG_MAIL', FALSE );
define( 'DEBUG_MAIL_TO', ' ** CHANGE ME ** ' );


// CHANGE ME
define( 'TRAVIS_TOKEN', ' ** CHANGE ME ** ' );

// CHANGE ME - Root URL for the Teamwork API call,
// e.g. https://yoursite.teamworkpm.net/tasks/... (LEAVE %s!)
define( 'TEAMWORK_URL',
    'https://** CHANGE ME **.teamworkpm.net/tasks/%s/comments.json' );

// CHANGE ME - TeamworkPM user token used to post a comment on behalf of the Github commit
define( 'TEAMWORK_USER_TOKEN', ' ** CHANGE ME ** ' );

// If you're behind a proxy server then edit this line, e.g. 'proxy.example.org:80'
define( 'HTTP_PROXY', NULL );

// Skip resource IDs (task IDs) less than this number.
define( 'MIN_RESOURCE_ID', 9999 );

// Optionally, edit the comment template, using placeholders, e.g. {URL}. Markdown allowed.
define( 'COMMENT_TEMPLATE', '> [Build status: #{BUILD_NUM} {STATUS} (Travis-CI)]({TRAVIS_URL})
> {COMMENT}

* [GitHub: {COMMIT_NAME}]({GITHUB_URL})

Committed by {AUTHOR} on {DATE} _(comment via Travis-CI)_' );

// Optionally, edit the date-time format for your locale, based on http://php.net/manual/en/function.date.php
define( 'DATE_FORMAT', 'm/d/Y' );


//End.
