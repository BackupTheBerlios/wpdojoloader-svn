<?php

require_once(dirname(__FILE__).'/config.php');
require( dirname(__FILE__) . '/../../../wp-config.php' );

wp_cache_init();
session_start();
global $gl_session_authentication;

$authorised = false;
if ($gl_session_authentication)
{
	$authorised = false;
	
	if (isset($_SESSION["wpd_session_auth"]) && ($_SESSION["wpd_session_auth"] != ""))
	{
		$pos = strpos($_SESSION["wpd_session_auth"], "wpd_session_auth");
		if ($pos == 0)
		{
			$authorised = true;	
		}	
		if ($pos === false)
		{
			$authorised = false;		
		}
	}
	/* */
} else
{
	$authorised = true;	
}
/**/
if ( $authorised ) {
    if ( isset( $_GET['id'] ) && !empty( $_GET['id'] ) ) {        
        //var_dump(get_post($_GET['id']));
        $pst = get_post($_GET['id']); 
        //echo "post_status:".$pst->post_status;
        if ($pst->post_status == "publish")
        {
	        $dl_dojoLoader = new WpDojoLoader();
	       	 
	        //$dl_dojoLoader->addcontenttags = false;
	        
	        $tplname  = $_GET['template'];
	        $uid      = $_GET['uid'];
	        $cntgroup = $_GET['contentgroup'];
	        
	        $dl_dojoLoader->customtemplates = array(array($tplname,$uid));
	        $dl_dojoLoader->customuid       = $uid;
	        $dl_dojoLoader->contentgroup    = $cntgroup;
	        $dl_dojoLoader->ajaxload = true;
	        $dl_dojoLoader->debugmode = false;
	        
	        $content = $dl_dojoLoader->addContent($pst->post_content);
	  		echo $content;
        }
    } else {
        die( '{"response":"1","message":"' . __( 'No id or content.' ) . '"}');
    }
} else {
    die( '{"response":"1","message":"' . __( 'You are not authorised to load content.' ) . '"}');
}

?>