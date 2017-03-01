<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 6/2/2016
 * Time: 2:27 AM
 */

namespace lillockey\Utilities\App\Wordpress;


trait WordpressOptionableTrait
{
    /**
     * @param $key
     * @param $default - [optional] The value to return if no existing value is found
     * @return array|mixed
     */
    public abstract function option_fetch__lines_as_array($key, $default = null);

    /**
     * @param $key
     * @param $default - [optional] The value to return if no existing value is found
     * @return string|mixed
     */
    public abstract function option_fetch__string($key, $default = null);

    /**
     * @param $key
     * @param $default - [optional] The value to return if no existing value is found
     * @return int|mixed
     */
    public abstract function option_fetch__int($key, $default = null);

    /**
     * @param $key
     * @param $default - [optional] The value to return if no existing value is found
     * @return boolean|mixed
     */
    public abstract function option_fetch__boolean($key, $default = null);

    /**
     * @param $key
     * @param $default - [optional] The value to return if no existing value is found
     * @return mixed
     */
    public abstract function option_fetch__raw($key, $default = null);



    //////////////////////////////////////////////////////////
    //Discussion
    //////////////////////////////////////////////////////////
    public function option__blacklist_keys($as_array = false)
    {

    }


    public function option__comment_max_links()
    {

    }


    public function option__comment_moderation()
    {

    }


    public function option__comments_notify()
    {

    }


    public function option__default_comment_status()
    {

    }


    public function option__default_ping_status()
    {

    }


    public function option__default_pingback_flag()
    {

    }


    public function option__moderation_keys($as_array = false)
    {

    }


    public function option__moderation_notify()
    {

    }


    public function option__require_name_email()
    {

    }


    public function option__thread_comments()
    {

    }


    public function option__thread_comments_depth()
    {

    }


    public function option__show_avatars()
    {

    }


    public function option__avatar_rating()
    {

    }


    public function option__avatar_default()
    {

    }


    public function option__close_comments_for_old_posts()
    {

    }


    public function option__close_comments_days_old()
    {

    }


    public function option__page_comments()
    {

    }


    public function option__comments_per_page()
    {

    }


    public function option__default_comments_page()
    {

    }


    public function option__comment_order()
    {

    }


    public function option__comment_whitelist()
    {

    }



    //////////////////////////////////////////////////////////
    //General
    //////////////////////////////////////////////////////////

    public function option__admin_email ()
    {

    }


    public function option__blogdescription ()
    {

    }


    public function option__blogname()
    {

    }


    public function option__comment_registration ()
    {

    }


    public function option__date_format ()
    {

    }


    public function option__default_role ()
    {

    }


    public function option__gmt_offset ()
    {

    }


    public function option__home ()
    {

    }


    public function option__siteurl ()
    {

    }


    public function option__start_of_week ()
    {

    }


    public function option__time_format ()
    {

    }


    public function option__timezone_string()
    {

    }


    public function option__users_can_register ()
    {

    }



    //////////////////////////////////////////////////////////
    //Link
    //////////////////////////////////////////////////////////

    public function option__links_updated_date_format()
    {

    }


    public function option__links_recently_updated_prepend()
    {

    }


    public function option__links_recently_updated_append()
    {

    }


    public function option__links_recently_updated_time()
    {

    }



    //////////////////////////////////////////////////////////
    //Media
    //////////////////////////////////////////////////////////

    public function option__thumbnail_size_w()
    {

    }


    public function option__thumbnail_size_h()
    {

    }


    public function option__thumbnail_crop()
    {

    }


    public function option__medium_size_w()
    {

    }


    public function option__medium_size_h()
    {

    }


    public function option__large_size_w()
    {

    }


    public function option__large_size_h()
    {

    }


    public function option__embed_autourls()
    {

    }


    public function option__embed_size_w()
    {

    }


    public function option__embed_size_h()
    {

    }



    //////////////////////////////////////////////////////////
    //Miscellaneous
    //////////////////////////////////////////////////////////

    public function option__hack_file ()
    {

    }


    public function option__html_type ()
    {

    }


    public function option__secret ()
    {

    }


    public function option__upload_path ()
    {

    }


    public function option__upload_url_path ()
    {

    }


    public function option__uploads_use_yearmonth_folders ()
    {

    }


    public function option__use_linksupdate ()
    {

    }



    //////////////////////////////////////////////////////////
    //Permalinks
    //////////////////////////////////////////////////////////

    public function option__permalink_structure ()
    {

    }


    public function option__category_base()
    {

    }


    public function option__tag_base()
    {

    }



    //////////////////////////////////////////////////////////
    //Privacy
    //////////////////////////////////////////////////////////

    public function option__blog_public()
    {

    }



    //////////////////////////////////////////////////////////
    //Reading
    //////////////////////////////////////////////////////////

    public function option__blog_charset ()
    {

    }


    public function option__gzipcompression ()
    {

    }


    public function option__page_on_front ()
    {

    }


    public function option__page_for_posts ()
    {

    }


    public function option__posts_per_page ()
    {

    }


    public function option__posts_per_rss ()
    {

    }


    public function option__rss_language ()
    {

    }


    public function option__rss_use_excerpt ()
    {

    }


    public function option__show_on_front ()
    {

    }



    //////////////////////////////////////////////////////////
    //Themes
    //////////////////////////////////////////////////////////

    public function option__template ()
    {

    }


    public function option__stylesheet ()
    {

    }



    //////////////////////////////////////////////////////////
    //Writing
    //////////////////////////////////////////////////////////

    public function option__default_category ()
    {

    }


    public function option__default_email_category ()
    {

    }


    public function option__default_link_category ()
    {

    }


    public function option__default_post_edit_rows ()
    {

    }


    public function option__mailserver_login ()
    {

    }


    public function option__mailserver_pass ()
    {

    }


    public function option__mailserver_port ()
    {

    }


    public function option__mailserver_url ()
    {

    }


    public function option__ping_sites ()
    {

    }


    public function option__use_balanceTags ()
    {

    }


    public function option__use_smilies ()
    {

    }


    public function option__use_trackback ()
    {

    }


    public function option__enable_app()
    {

    }


    public function option__enable_xmlrpc()
    {

    }



    //////////////////////////////////////////////////////////
    //Uncategorized
    //////////////////////////////////////////////////////////

    public function option__active_plugins()
    {

    }


    public function option__advanced_edit()
    {

    }


    public function option__recently_edited()
    {

    }


    public function option__image_default_link_type()
    {

    }


    public function option__image_default_size()
    {

    }


    public function option__image_default_align()
    {

    }


    public function option__sidebars_widgets ()
    {

    }


    public function option__sticky_posts()
    {

    }


    public function option__widget_categories()
    {

    }


    public function option__widget_text()
    {

    }


    public function option__widget_rss()
    {

    }


}