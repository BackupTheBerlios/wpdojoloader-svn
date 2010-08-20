<?php
/**
 * Inline Editor AJAX Save File
 *
 * @copyright 2009 Business Xpand
 * @license GPL v2.0
 * @author Steven Raynham
 * @version 0.7
 * @link http://www.businessxpand.com/
 * @since File available since Release 0.5
 */
require( dirname(__FILE__) . '/../../../wp-config.php' );
wp_cache_init();

//$authorised = current_user_can('read');// && current_user_can('edit_pages');
$authorised = true;
if ( $authorised ) {
    if ( isset( $_GET['id'] ) && !empty( $_GET['id'] ) ) {
        $opePost['ID'] = $_POST['id'];
        $opePost['post_content'] = rawurldecode( $_POST['content'] );
        
        
        //var_dump(get_post($_GET['id'])->post_content);
        $pst = get_post($_GET['id'])->post_content;
        
        $dl_dojoLoader = new WpDojoLoader();
        
        //$dl_dojoLoader->addcontenttags = false;
        
        $tplname  = $_GET['template'];
        $uid      = $_GET['uid'];
        $cntgroup = $_GET['contentgroup'];
        
        $dl_dojoLoader->customtemplates = array(array($tplname,$uid));
        $dl_dojoLoader->customuid       = $uid;
        $dl_dojoLoader->contentgroup    = $cntgroup;
        
        //$dl_dojoLoader->debugmode = true;
        
  		//echo $pst;
        $content = $dl_dojoLoader->addContent($pst);
  		echo $content;
        
        
        /*
        $search = array( '<!--ile-->&lt;',
                         '&gt;<!--ile-->',
                         '&lt;!--',
                         '--&gt;' );
        $replace = array( '[ilelt]',
                          '[ilegt]',
                          '<!--',
                          '-->' );
        $opePost['post_content'] = str_replace( $search, $replace, $opePost['post_content'] );
        $search = array( '[ilelt]',
                         '[ilegt]' );
	    $replace = array( '&lt;',
                          '&gt;' );                          
        $opePost['post_content'] = str_replace( $search, $replace, $opePost['post_content'] );                          
        $opePost['post_content'] = format_to_post( $opePost['post_content'] );
			
        if ( wp_update_post( $opePost ) === 0 )
            die( '{"response":"0","message":"' . __( 'Unable to save, database error generated.' ) . '"}' );
        else
            die( '{"response":"1","postid":"'.$_POST['id'].'","message":"' . __( 'Content updated.' ) . '"}' );
        */
    } else {
        die( '{"response":"1","message":"' . __( 'No id or content.' ) . '"}');
    }
} else {
    die( '{"response":"1","message":"' . __( 'You are not authorised to edit.' ) . '"}');
}

?>