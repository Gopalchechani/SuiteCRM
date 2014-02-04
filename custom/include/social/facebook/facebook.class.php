<?php
require_once("custom/include/social/facebook/facebook_sdk/src/facebook.php");


class facebook_helper
{

    var $facebook;

    function __construct()
    {
        require("custom/modules/Connectors/connectors/sources/ext/rest/facebook/config.php");

        $fb_config = array(
            'appId' => $config['properties']['appid'],
            'secret' => $config['properties']['secret']
        );
        $this->facebook = new Facebook($fb_config);
    }

    function get_my_user()
    {
        try {
            // Proceed knowing you have a logged in user who's authenticated.
            return $this->facebook->api('/me');
        } catch (FacebookApiException $e) {
            error_log($e);
            $user = null;
        }
    }

    function get_my_newsfeed()
    {
        return $this->facebook->api('me/home'); //get my news feed
    }

    function get_other_newsfeed($user, $limit = "100")
    {
        return $this->facebook->api('/' . $user . '/feed?limit=' . $limit);
    }

    function get_login_url($url)
    {
        $params = array(
            'scope' => 'read_stream, publish_stream'

        );


        return $this->facebook->getLoginUrl($params);
    }

    function get_logout_url()
    {
        return $this->facebook->getLogoutUrl();
    }

    function get_facebook_user($username)
    {
        return $this->facebook->api('/' . $username);
    }

    function process_feed($story)
    {
        switch ($story['type']) {
            case "status":
                return $this->status($story);
                break;
            case "photo":
                return $this->photo_status($story);
                break;
            case "link":
                return $this->link_type($story);
                break;
            case "video":
                return $this->video_type($story);
                break;
        }
    }

    function photo_status($story)
    {

        $people = $this->get_people($story);
        $post_link = $this->create_post_link($story);

        $string = '';

        $string .= "<div style=' margin: 0 auto; background-color: #F7F7F7; height:160px; width:389px; ; border:1px solid #cccccc'>";

        if (isset($people['to'])) {
            $string .= '<div style="width:50%; height: 65%; float:right;" id="to">';

            $i = 0;
            while (count($people['to']) > $i) {

                $string .= '<img style="width:50%; margin-top:20px;" src='. $story['picture'] .'>';
                $string .= '<p style="text-align:left;">' .  $story['description'] . '</p>';
                $i++;
            }


            $string .= '</div>';
        }


        if (isset($people['from'])) {
            $string .= '<div style="width:50%; height: 65%; float:left;" id="from">';

            $i = 0;
            while (count($people['from']) > $i) {

                $string .= '<img style="margin:75px; margin-bottom:10px; margin-top:20px;" src=https://graph.facebook.com/' . $people['from'][$i]['id'] . '/picture>';
                $string .= '<p style="text-align:center;">' . $people['from'][$i]['name'] . '</p>';

                $i++;
            }


            $string .= '</div>';
        }



        if(!empty($story['story'])){
            $string .= '<div style="width:100%; float:left; text-align:center;">' . $story['story'] . '</br>' .$post_link . '</div>';

        }
        if(!empty($story['message'])){
            $string .= '<div style="width:100%; float:left; text-align:center;">' . $story['message'] . '</br>' .$post_link . '</div>';

        }
        $string .= "</div>";

        return $string;
    }

    function status($story)
    {

        $people = $this->get_people($story);
        $post_link = $this->create_post_link($story);

        $string = '';

        $string .= "<div style=' margin: 0 auto; background-color: #F7F7F7; height:160px; width:389px; border:1px solid #cccccc'>";

        if (isset($people['to']) && $people['to'][0]['id'] != null && $people['from'][0]['id'] != null) {
            $string .= '<div style="width:50%; height: 65%; float:right;" id="to">';

            $i = 0;
            while (count($people['to']) > $i) {

                $string .= '<img style="margin:75px; margin-bottom:10px; margin-top:20px;" src=https://graph.facebook.com/' . $people['to'][$i]['id'] . '/picture>';
                $string .= '<p style="text-align:center;">' . $people['to'][$i]['name'] . '</p>';
                $i++;
            }


            $string .= '</div>';
        }


        if (isset($people['from'])) {
            if (isset($people['to']) && $people['to'][0]['id'] != null && $people['from'][0]['id'] != null) {
            $string .= '<div style="width:50%; height: 65%; float:left;" id="from">';

            $i = 0;


            while (count($people['from']) > $i) {

                $string .= '<img style="margin:75px; margin-bottom:10px; margin-top:20px;" src=https://graph.facebook.com/' . $people['from'][$i]['id'] . '/picture>';
                $string .= '<p style="text-align:center;">' . $people['from'][$i]['name'] . '</p>';

                $i++;
            }


            $string .= '</div>';
        }else{
                $string .= '<div style="width:100%; height: 65%; float:left;" id="from">';

                $i = 0;


                while (count($people['from']) > $i) {

                    $string .= '<img style="margin:170px; margin-bottom:10px; margin-top:20px;" src=https://graph.facebook.com/' . $people['from'][$i]['id'] . '/picture>';
                    $string .= '<p style="text-align:center;">' . $people['from'][$i]['name'] . '</p>';

                    $i++;
                }


                $string .= '</div>';
            }
        }

        if(!empty($story['story'])){
            $string .= '<div style="width:100%; float:left; text-align:center;">' . $story['story'] . '</br>' .$post_link . '</div>';

        }
        if(!empty($story['message'])){
            $string .= '<div style="width:100%; float:left; text-align:center;">' . $story['message'] . '</br>' .$post_link . '</div>';

        }
        $string .= "</div>";

        return $string;


    }

    function link_type($story)
    {
        $people = $this->get_people($story);
        $post_link = $this->create_post_link($story);

        $string = '';

        $string .= "<div style=' margin: 0 auto; background-color: #F7F7F7; height:160px; width:389px; ; border:1px solid #cccccc'>";

        if (isset($people['to']) && $people['to'][0]['id'] != null) {
            $string .= '<div style="width:50%; height: 65%; float:right;" id="to">';

            $i = 0;
            while (count($people['to']) > $i) {

                $string .= '<img style="margin:75px; margin-bottom:10px; margin-top:20px;" src=https://graph.facebook.com/' . $people['to'][$i]['id'] . '/picture>';
                $string .= '<p style="text-align:center;">' . $people['to'][$i]['name'] . '</p>';
                $i++;
            }


            $string .= '</div>';
        }else{
            $string .= '<div style="width:50%; height: 65%; float:right;" id="to">';
            $string .= '<img style="margin:75px; margin-bottom:10px; margin-top:20px; width: 50px; height:50px;" src=https://graph.facebook.com/' . $story['application']['id'] . '/picture>';
            $string .= '<p style="text-align:center;">' . $story['application']['name'] . '</p>';
            $story['story'] = $story['from']['name'] . ' likes ' . $story['application']['name'];
            $string .= '</div>';

        }


        if (isset($people['from'])) {
            $string .= '<div style="width:50%; height: 65%; float:left;" id="from">';

            $i = 0;
            while (count($people['from']) > $i) {

                $string .= '<img style="margin:75px; margin-bottom:10px; margin-top:20px;" src=https://graph.facebook.com/' . $people['from'][$i]['id'] . '/picture>';
                $string .= '<p style="text-align:center;">' . $people['from'][$i]['name'] . '</p>';

                $i++;
            }


            $string .= '</div>';
        }

        if(!empty($story['story'])){
            $string .= '<div style="width:100%; float:left; text-align:center;">' . $story['story'] . '</br>' .$post_link . '</div>';

        }
        if(!empty($story['message'])){
            $string .= '<div style="width:100%; float:left; text-align:center;">' . $story['message'] . '</br>' .$post_link . '</div>';

        }
        $string .= "</div>";

        return $string;



    }

    function video_type($story)
    {

        $string = '';
        $string .= "<div style=' margin: 0 auto; background-color: #F7F7F7; height:160px; width:389px; ; border:1px solid #cccccc'>";
        $string .= '<div style="padding: 3px; width: 100%;">' . $story['from']['name'] . ' Shared a video with ' . $story['message'] . '</div>';

        $string .= '<a style="padding: 5px; float:left;" href="' . $story['link'] . '"><img style=float:left; src="' . $story['picture'] . '"/></a>';
        $string .= '<a  href="' . $story['link'] . '">' . $story['description'] . '</a>';
        $string .= '<p>' . $story['caption'] . '</p>';
        $string .= "</div>";
        return $string;
    }

    function get_people($story)
    {

        $i = 0;
        $j = 0;

        foreach ($story['story_tags'] as $tag => $person) {

            if ($tag == 0) {
                $value['from'][$i]['name'] = $person[0]['name'];
                $value['from'][$i]['id'] = $person[0]['id'];
                $i++;
            } else {

                $value['to'][$j]['id'] = $story['story_tags'][$tag][0]['id'];
                $value['to'][$j]['name'] = $story['story_tags'][$tag][0]['name'];
                $j++;
            }

        }

        if (!isset($value['from']) && !isset($value['to'])) {
            $i = 0;

            $value['from'][$i]['name'] = $story['from']['name'];
            $value['from'][$i]['id'] = $story['from']['id'];
            $value['to'][$i]['name'] = $story['to']['data'][0]['name'];
            $value['to'][$i]['id'] = $story['to']['data'][0]['id'];

        }

        if (!isset($value['from'])) {
            $value['from'][$i]['name'] = $story['from']['name'];
            $value['from'][$i]['id'] = $story['from']['id'];
        }

        return $value;

    }

    function create_post_link($story){

        $post_link = '';
        $post_link = explode('_',$story['id']);
        $post_link = '<a href=http://facebook.com/' . $post_link[0] . '/posts/' . $post_link[1] . '>View on Facebook</a>';

        return $post_link;
    }
}

?>