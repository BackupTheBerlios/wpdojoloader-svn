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

$authorised = current_user_can('edit_posts') && current_user_can('edit_pages');
if ( $authorised ) {
    if ( isset( $_POST['id'] ) && isset( $_POST['content'] ) && !empty( $_POST['id'] ) && !empty( $_POST['content'] ) ) {
        $opePost['ID'] = $_POST['id'];
        $opePost['post_content'] = rawurldecode( $_POST['content'] );
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
    } else {
        die( '{"response":"1","message":"' . __( 'No id or content.' ) . '"}');
    }
} else {
    die( '{"response":"1","message":"' . __( 'You are not authorised to edit.' ) . '"}');
}

?>