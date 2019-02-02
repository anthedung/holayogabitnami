<?php
/*
Plugin Name: Video Gallery by Huzzaz
Plugin URI: https://pro.huzzaz.com/videogallery
Description: Video gallery that is awesome, easy to use, and works great with YouTube, Vimeo, Facebook, and Twitch videos. Activate and use the shortcode: [huzzaz id="?" vpp="?" bg="?" color="?" button="?" highlight="?"]. Register at huzzaz.com/join?src=wp to create a video collection. Visit the plugin site for more details.
Version: 10.1
Author: Huzzaz
Author URI: https://huzzaz.com
License: GPL2
*/

/*  Copyright 2013  James Yang  (email : james@huzzaz.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// [huzzaz id="collection ID" vpp="16"]
function huzzaz_func( $atts ) {
    extract( shortcode_atts( array(
        'id' => 'top-videos-by-huzzaz',
        'vpp' => '16',
        'bg' => '',
        'color' => '',
        'button' => '',
        'highlight' => '',
        'pro' => 0,
        'layout' => '',
        'search' => '',
        'gicon' => '',
        'titleoverlay' => '',
        'showvideos' => '',
        'arrows' => '',
        'nospaces' => '',
        'autoplay' => '',
        'hzv' => '',
        'nocoverart' => '',
        'noannotations' => '',
        'scrolloffset' => '',
        'float' => '',
        'popoutlink' => 0,
        'linktext' => 'Click Me',
        'class' => 'huzzazWrapper'
    ), $atts ) );

    $bw_preload = ( $gicon ) ? '_bw' : '';
    if( !$pro ) {
        $src = 'https://huzzaz.com/embed/' . $id . '?vpp=' . $vpp . ( empty($bg) ? "" : '&bg=' . $bg ) . ( empty($color) ? "" : '&color=' . $color ) . ( empty($button) ? "" : '&button=' . $button ) . ( empty($highlight) ? "" : '&highlight=' . $highlight );
        $gallery = '<div class="' . esc_attr($class) . '"><div class="hzload" style="width: 200px; padding: 10px; border-radius: 5px; margin: auto; text-align: center; background-color: #fff;"><img src="//huzzaz.com/images/hzload'.$bw_preload.'.gif" style="width:75px;" alt="loading videos"/><div>Loading Videos...</div></div><iframe class="hzframe" src="' . esc_url($src) . '" height="0" width="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen allowTransparency="true"></iframe><script src="https://huzzaz.com/js/hzframe.js"></script></div>';
    } else {
        if ( !$popoutlink ) {
            if ( !empty($float) ) {
                if (strpos($float, 'top') !== false) {
                    $float_y = "top: 55px" . ($layout != "card" ? "" : " !important" ) . ";";
                } elseif ( strpos($float, 'bottom') !== false ){
                    $float_y = "bottom: 25px" . ($layout != "card" ? "" : " !important" ) . ";top: auto !important;";
                }
                if( strpos($float, 'left') !== false ) {
                    $float_x = "left: 265px" . ($layout != "card" ? "" : " !important" ) . ";transform: translateX(-240px);";
                } elseif ( strpos($float, 'right') !== false) {
                    $float_x = "right: 265px" . ($layout != "card" ? "" : " !important" ) . ";left: auto !important;transform: translateX(240px);";
                }
                if( $layout != "card" ) {
                    $cssFloat = '<style>.hzframe{transition: width .2s ease-in-out, height .2s ease-in-out, transform .38s ease-in-out;}.hzframe.hzfloat{position: fixed;'.$float_x.$float_y.'max-width: 280px;max-height: 158px;width: 280px;height: 158px;z-index: 999;}.'.$class.'.hzfloat {background-color: rgba(192,192,192,.5);}</style>';

                } else {
                    $cssFloat = '<style>.hzframe{transition: width .2s ease-in-out, height .2s ease-in-out, transform .38s ease-in-out;}.hzframe.hzfloat {position: fixed !important;'.$float_x.$float_y.'max-width: 280px;max-height: 158px;width: 280px !important;height: 158px !important;z-index: 999;}.'.$class.'.hzfloat {background-color: rgba(192,192,192,.5);}</style>';
                }
            } else {
                $cssFloat = '';
            }
            if ( $layout != "card" ){
                $src = 'https://huzzaz.com/proembed/' . $id . '?layout=' . $layout . '&vpp=' . $vpp . ( empty($bg) ? "" : '&bg=' . $bg ) . ( empty($color) ? "" : '&color=' . $color ) . ( empty($button) ? "" : '&button=' . $button ) . ( empty($highlight) ? "" : '&highlight=' . $highlight ) . ( empty($search) ? "" : '&search=' . $search ) . ( empty($gicon) ? "" : '&gicon=' . $gicon ) . ( empty($titleoverlay) ? "" : '&titleoverlay=' . $titleoverlay ) . ( empty($showvideos) ? "" : '&showvideos=' . $showvideos ) . ( empty($arrows) ? "" : '&arrows=' . $arrows ) . ( empty($autoplay) ? "" : '&hzauto=' . $autoplay ) . ( empty($nospaces) ? "" : '&nospaces=' . $nospaces ) . ( empty($nocoverart) ? "" : '&nocoverart=' . $nocoverart ) . ( empty($noannotations) ? "" : '&noannotations=' . $noannotations ) . ( empty($scrolloffset) ? "" : '&scrolloffset=' . $scrolloffset ) . ( empty($hzv) ? "" : '&hzv=' . $hzv );
                $gallery = $cssFloat . '<div class="' . esc_attr($class) . '"><div class="hzload" style="width: 200px; padding: 10px; border-radius: 5px; margin: auto; text-align: center; background-color: #fff;"><img src="//huzzaz.com/images/hzload'.$bw_preload.'.gif" style="width:75px;" alt="loading videos"/><div>Loading Videos...</div></div><iframe class="hzframe" style="height: 0; width: 100%;" src="' . esc_url($src) . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen allowTransparency="true"></iframe><script src="https://huzzaz.com/js/hzframe' . (!empty($cssFloat) ? '_float' : '') . '.min.js"></script></div>';
            } else {
                $src = 'https://huzzaz.com/proembed/' . $id . '?layout=' . $layout . '&vpp=' . $vpp . ( empty($bg) ? "" : '&bg=' . $bg ) . ( empty($color) ? "" : '&color=' . $color ) . ( empty($button) ? "" : '&button=' . $button ) . ( empty($highlight) ? "" : '&highlight=' . $highlight ) . ( empty($search) ? "" : '&search=' . $search ) . ( empty($gicon) ? "" : '&gicon=' . $gicon ) . ( empty($titleoverlay) ? "" : '&titleoverlay=' . $titleoverlay ) . ( empty($showvideos) ? "" : '&showvideos=' . $showvideos ) . ( empty($autoplay) ? "" : '&hzauto=' . $autoplay ) . ( empty($nospaces) ? "" : '&nospaces=' . $nospaces ) . ( empty($nocoverart) ? "" : '&nocoverart=' . $nocoverart ) . ( empty($noannotations) ? "" : '&noannotations=' . $noannotations ) . ( empty($scrolloffset) ? "" : '&scrolloffset=' . $scrolloffset ) . ( empty($hzv) ? "" : '&hzv=' . $hzv );
                $gallery = $cssFloat . '<div class="' . esc_attr($class) . '"><div style="padding-bottom: 54.8%; position: relative; padding-top: 25px; height: 0; margin-bottom: 16px; overflow: hidden;"><iframe class="hzframe" style="position: absolute; top: 0; left: 0; height: 100%; width: 100%;" src="' . esc_url($src) . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen allowTransparency="true"></iframe>'.(!empty($cssFloat) ? '<script src="https://huzzaz.com/js/hzframe.min.js"></script>' : '').'</div></div>';
            }
        }
        else {
            $href = 'https://huzzaz.com/proembed/' . $id . '?layout=popout' . ( empty($bg) ? "" : '&bg=' . $bg ) . ( empty($color) ? "" : '&color=' . $color ) . ( empty($button) ? "" : '&button=' . $button ) . ( empty($highlight) ? "" : '&highlight=' . $highlight ) . ( empty($search) ? "" : '&search=' . $search ) . ( empty($gicon) ? "" : '&gicon=' . $gicon ) . ( empty($titleoverlay) ? "" : '&titleoverlay=' . $titleoverlay ) . ( empty($autoplay) ? "" : '&hzauto=' . $autoplay ) . ( empty($nocoverart) ? "" : '&nocoverart=' . $nocoverart ) . ( empty($noannotations) ? "" : '&noannotations=' . $noannotations ) . ( empty($hzv) ? "" : '&hzv=' . $hzv );
            $gallery = '<a class="huzzazPopoutLink" onclick="window.open(\'' . esc_url($href) . '\', \'newwindow\', \'scrollbars=1,width=1025, height=650\'); return false;" href="' . esc_url($href) . '">' . esc_html($linktext) . '</a>';
        }
    }
    return $gallery;
}
add_shortcode( 'huzzaz', 'huzzaz_func' );