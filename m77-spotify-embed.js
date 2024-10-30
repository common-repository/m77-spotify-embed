function addSpotifyShortcode(){
	var TRACK_ID_LENGTH = 22;
	var uri_includes 	= "spotify:";
	var spotify_uri 	= jQuery('#m77_spotify_meta_uri').val();
	var spotify_size 	= jQuery('#m77_spotify_meta_size').val();
	var spotify_width 	= jQuery('#m77-spotify-last-used-custom-width').val();
	var spotify_height 	= jQuery('#m77-spotify-last-used-custom-height').val();

	if(spotify_uri.length < TRACK_ID_LENGTH){
		addSpotifyShortcodeError('No valid URI');
	}else if(spotify_uri.indexOf(uri_includes) == -1){
		addSpotifyShortcodeError('The URI should start with <strong>'+ uri_includes +'</strong>');
	}else{
		var spotify_shortcode = '[spotify uri="'+spotify_uri+'"';
		if(spotify_size == "custom" || spotify_size == "large"){
			spotify_shortcode += ' size="'+ spotify_size +'"';
		}
		spotify_shortcode += ']';
		if(jQuery("#content").is(':visible')){
			jQuery("#content").append('\n\r'+spotify_shortcode);
		}else{
			//tinyMCE.activeEditor.setContent(tinyMCE.activeEditor.getContent() + spotify_shortcode);
			tinyMCE.execInstanceCommand('content',"mceInsertContent",false,'<br/>'+spotify_shortcode);
		}
		jQuery('#m77_spotify_meta_uri').val(''); // Empty
	}
}
function addSpotifyShortcodeError(msg){
	jQuery('#m77_spotify_alert_msg').html(msg);
	jQuery('#m77_spotify_alert_msg').show();
	setTimeout("jQuery('#m77_spotify_alert_msg').fadeOut()", 2500);
}
jQuery(function(){
	jQuery('#m77_spotify_meta_size').change(function(e){
		if(jQuery(this).val() == 'custom'){
			jQuery('#m77-spotify-last-used-custom').show();
		}else{
			jQuery('#m77-spotify-last-used-custom').hide();
		}
	});

	jQuery('#m77-spotify-preview-text, #m77-spotify-options-theme-coverart, #m77-spotify-options-view-list, #m77-spotify-custom-width, #m77-spotify-custom-height, #m77-spotify-options-theme-black, #m77-spotify-options-theme-white').change(function(){
		// Update preview
		console.log(jQuery('#m77-spotify-preview-iframe'));
		jQuery('#m77-spotify-preview-iframe').html('<iframe src="https://embed.spotify.com/?uri='+ jQuery('#m77-spotify-preview-text').val() +'&theme='+ jQuery('m77-spotify-options[default-theme]').val() +'&view='+ jQuery('m77-spotify-options[default-view]').val() +'" width="'+ jQuery('#m77-spotify-custom-width').val() +'" height="'+ jQuery('#m77-spotify-custom-height').val() +'" frameborder="0" allowtransparency="true"></iframe>');
	});

	jQuery('#m77_spotify_help_icon').mouseover(function(){
		jQuery('#m77_spotify_help').show();
	});
	jQuery('#m77_spotify_help_icon').mouseout(function(){
		jQuery('#m77_spotify_help').hide();
	});

});