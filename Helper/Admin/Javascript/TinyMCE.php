<?php

/**
 * Unus
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://nwhiting.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@nwhiting.com so we can send you a copy immediately.
 *
 * DO NOT MODIFY this files contents if you wish to upgrade Unus in the future,
 * If there is a bug with this file address them at http://www.nwhiting.com/
 * so we can include this fix for future releases.
 *
 * For improvements please address them at http://www.nwhiting.com/
 * they will be greatly appreciated, while it is not required it would be good
 * to contribute. HAVE FUN and HAPPY CODING
 *
 */



/**
 * @category   Unus
 * @package    Unus
 * @version    $Rev: 1$
 * @author     Nickolas Whiting <admin@nwhiting.com>
 * @copyright  Copyright 2009 Nickolas Whiting
 */

class Unus_Helper_Admin_Javascript_TinyMCE
{
    
    /**
     * Loads the tinyMCE editor
     *
     */
    
    public static function loadTinyMCE($admin = false)
    {
        $tinyMCE = '<!--TinyMCE Editor Load Start-->
                    <script type="text/javascript" src="/portfolio/Unus/plugins/Admin_TinyMCE/js/tiny_mce/tiny_mce.js"></script>
                    <script type="text/javascript">
                    tinyMCE.init({
                        // General options
                        mode : "textareas",
                        theme : "advanced",
                        skin : "o2k7",
                        skin_variant : "silver",
                        height: "450",
                        margin: "4px",
                        plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
                    
                        // Theme options
                        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
                        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
                        theme_advanced_toolbar_location : "top",
                        theme_advanced_toolbar_align : "left",
                        theme_advanced_statusbar_location : "bottom",
                    
                        // Example content CSS (should be your site CSS)';
                        if ($admin)
                        {
                            $tinyMCE .= 'content_css : "'.WEBPATH.'/admin/stylesheet.css",';
                        }
                        else
                        {
                            $tinyMCE .= 'content_css : "'.WEBPATH.'/stylesheet.css",';
                        }
                        
                        $tinyMCE .= '
                       
                    });
                    </script>
                    <!--TinyMCE Editor End-->';
        echo $tinyMCE;
    }
}

?>