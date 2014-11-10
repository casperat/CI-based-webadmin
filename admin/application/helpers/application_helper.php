<?php

function get_settings($key) {

    $CI =& get_instance();
    $CI->db->select("value")
            ->from("settings")
            ->where("key", $key);
    $result = $CI->db->get();

    if ($result->num_rows()) {
        $item = $result->row_array();
        return $item['value'];
    }

    return null;
}

function nice_url($rerouted_url = true) {

    $CI =& get_instance();
    if ($rerouted_url)
        $url_array = $CI->uri->rsegment_array();
    else
        $url_array = $CI->uri->segment_array();

    $ret = array();
    foreach ($url_array as $value) {
        if (strpos($value, ":")) {
            $parts = explode(":", $value, 2);
            $ret[$parts[0]] = $parts[1];
        }
        else if (substr($value, 0, 4) == "page")
            $ret["page"] = substr($value, strrpos($value, "-") + 1);
        else if (substr($value, 0, 3) == "ppp")
            $ret["ppp"] = substr($value, strrpos($value, "-") + 1);
        else
            $ret[] = $value;
    }

    return $ret;
}

/* End of file application_helper.php */
/* Location ./admin/application/helpers/application_helper.php */