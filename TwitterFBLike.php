<?php
/*
 * Parser that inserts Twitter and Facebook "Like" buttons on a page
 *
 * For more info see http://mediawiki.org/wiki/Extension:TwitterFBLike
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Barry Coughlan
 * @copyright © 2012 Barry Coughlan
 * @licence GNU General Public Licence 2.0 or later
 */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'TwitterFBLike', 
	'author' => 'Barry Coughlan (modifications: Lenz Weber)', 
	'url' => 'http://mediawiki.org/wiki/Extension:TwitterFBLike',
	'description' => 'Template that inserts Twitter and Facebook "Share" buttons on a page',
);

$wgHooks['ParserFirstCallInit'][] = 'twitterFBLikeParserFunction_Setup';
$wgHooks['LanguageGetMagic'][]       = 'twitterFBLikeParserFunction_Magic';
$wgHooks['BeforePageDisplay'][] = 'twitterFBLikeParserFeedHead'; # Setup function

function twitterFBLikeParserFunction_Setup( &$parser ) {
	# Set a function hook associating the "twitterFBLike_parser" magic word with our function
	$parser->setFunctionHook( 'twitterFBLike', 'twitterFBLikeParserFunction_Render' );
	return true;
}
 
function twitterFBLikeParserFunction_Magic( &$magicWords, $langCode ) {
        //Set first parameter to 1 to make it case sensitive
		$magicWords['twitterFBLike'] = array( 0, 'TwitterFBLike' );
        return true;
}

function twitterFBLikeParserFeedHead(&$out, &$sk) {
	global $wgScriptPath;
	$out->addHeadItem('twitterFBLike.css','<link rel="stylesheet" type="text/css" href="'.$wgScriptPath.'/extensions/TwitterFBLike/TwitterFBLike.css"/>');
        $out->prependHTML('<div id="fb-root"></div>');

	return $out;
}

 
function twitterFBLikeParserFunction_Render( &$parser, $param1 = '') {
		global $wgSitename;
	
		if ($param1 === "left" || $param1 === "right") {
			$float = $param1;
		} else {
			$float = "none";
		}
		
		$size="small";
		
		//Get page title and URL
		$title = $parser->getTitle();
		if (!$title) return "";
		$urltitle = $title->getPartialURL(); //e.g. "Main_Page"
		$url = $title->getFullURL();
		if (!$url ) return "";
		
		$text = str_replace("\"", "\\\"", $wgSitename . ": " . $title->getFullText());

		
		$output = "
			<div class='twitterFBLike_$size' twitterFBLike_$urltitle' style='float: ${float}'>
				<a style='display: none' href='http://twitter.com/share' class='twitter-share-button' data-text='$text' data-url='$url' $twitterextra>Tweet</a>
				<div style='line-height: 11px;' class='fb-share-button' data-href='${url}' data-layout='button'></div>
				<script src='//platform.twitter.com/widgets.js' type='text/javascript'></script>
                                <script src='//connect.facebook.net/en_US/sdk.js#xfbml=1&amp;version=v2.5&amp;appId=198618710208347' id='facebook-jssdk'></script>
			</div>
			";

		return $parser->insertStripItem(preg_replace("/\n/",'',$output), $parser->mStripState);
}
