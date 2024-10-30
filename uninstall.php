<?php

// If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

include('m77-spotify-embed.php');

// Delete option from options table
delete_option( M77Spotify::DB_OPTION_NAME );
//
?>