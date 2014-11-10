<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paginator {

    /**
    * the default rendering of pages
    *
    * @param mixed $page - the selected page
    * @param mixed $total_pages - the last page number
    * @param mixed $where - necessary for custom styling at the begining and end of the list
    */
    function render($config = array()) {

        $ret = "
        <div class='pages'>
            <span>Page: </span>";

        if ($config['link'])
            $link = trim($config['link'], "/");
        else {
            $named = nice_url(false);
            if (isset($named['page']))
                unset($named['page']);
            $link = array_to_url($named);
        }

        $page = $config['page'];
        $total_pages = $config['total_pages'];

        if ($total_pages <= 1)
            return "";

        if ($page > 1)
            $ret .= "<a href='$link/". ($page - 1) ."' class='prev' rel='noindex'>&laquo;</a>";

        $start = max(1, ($page - 3));
        if ($start < 4)
            $start = 1;
        $end = min($total_pages, ($page + 3));
        if ($end > ($total_pages - 3))
            $end = $total_pages;

        if ($start > 3)
            $ret .= "<a href='$link/1'>1</a><span>...</span>";

        for($i = $start; $i <= $end; $i++) {
            if ($i == $page)
                $ret .= "
                <a class='selected'>$i</a>";
            else
                $ret .= "
                <a href='$link/{$i}' rel='noindex'>{$i}</a>";
        }

        if ($end < ($total_pages - 2))
            $ret .= "<span>...</span><a href='$link/{$total_pages}'>{$total_pages}</a>";

        if ($page < $total_pages)
            $ret .= "
            <a href='$link/". ($page + 1) ."' class='next' rel='noindex'>&raquo;</a>";

        $ret .= "
            <div class='clearfloat'><!-- --></div>
        </div>";

        return $ret;
    }

    function render_with_get($config = array()) {

        $page = $config['page'];
        $total_pages = $config['total_pages'];
        $link = $config['link'];

        if ($total_pages <= 1)
            return "";

        $ret = "
        <div class='pages'>
            <span>Page: </span>";

        unset($_GET['p']);

        $get = $this->EncodeArray($_GET);

        if ($page > 1)
            $ret .= "<a href='$link?p=". ($page - 1) . $get ."' class='prev' rel='noindex'>&laquo;</a>";

        $start = max(1, ($page - 3));
        if ($start < 4)
            $start = 1;
        $end = min($total_pages, ($page + 3));
        if ($end > ($total_pages - 3))
            $end = $total_pages;

        if ($start > 3)
            $ret .= "<a href='$link?p=1". $get ."'>1</a><span>...</span>";

        for($i = $start; $i <= $end; $i++) {
            if ($i == $page)
                $ret .= "
                <a class='selected'>$i</a>";
            else
                $ret .= "
                <a href='$link?p={$i}". $get ."' rel='noindex'>{$i}</a>";
        }

        if ($end < ($total_pages - 2))
            $ret .= "<span>...</span><a href='$link?p={$total_pages}". $get ."'>{$total_pages}</a>";

        if ($page < $total_pages)
            $ret .= "
            <a href='$link?p=". ($page + 1) . $get ."' class='next' rel='noindex'>&raquo;</a>";

        $ret .= "
            <div class='clearfloat'><!-- --></div>
        </div>";

        return $ret;
    }

    function EncodeArray($args)
    {
        if(!is_array($args))
            return urlencode($args);
        $c = 0;
        $out = '';
        foreach($args as $name => $value) {
            $out .= '&';
            $out .= urlencode("$name").'=';

            if(is_array($value)) {
                $out .= urlencode(serialize($value));
            }
            else {
                $out .= urlencode("$value");
            }
        }

        return $out;
    }
}

/* End of file paginator.php */
/* Location ./application/libraries/paginator.php */