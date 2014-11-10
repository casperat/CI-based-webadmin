<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Collections_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_collections_menu() {
        $this->db->select("a.*, b.`name` as `category`")
                ->from("wa_tables as a")
                ->join("wa_categories as b", "a.category_id=b.id", "left")
                ->where("a.visible", "Yes")
                ->order_by("b.order, a.order");
        $r = $this->db->get();

        if ($r->num_rows())
            return $r->result_array();

        return null;
    }

    function get($id) {

        $r = $this->db->get_where("wa_tables", array("id" => $id));

        if ($r->num_rows()) {

            $collection = $r->row_array();
            $collection['fields'] = $this->get_fields($id, null, true);

            return $collection;
        }

        return null;
    }

    function get_by_name($name) {

        $r = $this->db->get_where("wa_tables", array("name" => $name));

        if ($r->num_rows()) {

            $collection = $r->row_array();
            $collection['fields'] = $this->get_fields($collection['id'], null, true);

            return $collection;
        }

        return null;
    }

    function get_fields($collection_id, $values = null, $keep_it_simple = false, $exclude_fks = array()) {

        $this->db->order_by("order");
        $r = $this->db->get_where("wa_cols", array("id_table" => $collection_id));

        if ($r->num_rows()) {
            $res = $r->result_array();

            if ($keep_it_simple)
                return $res;

            /*if ((int) $row_id > 0) {
                $c = $this->db->get_where("wa_tables", array("id" => $collection_id));
                $collection = $c->row_array();

                $q = $this->db->get_where($collection['name'], array($collection['name_equiv_for_pk'] => (int) $row_id));
                $values = $q->row_array();
            }*/

            foreach ($res as $key => $field) {
                if (is_array($values))
                    $field['value'] = $values[$field['name']];
                elseif (isset($field['default_value'])) {
                    $replace = array(
                                "#CURRENT_TIMESTAMP#" => date("Y-m-d H:i:s")
                                );
                    $field['value'] = str_replace(array_keys($replace), array_values($replace), $field['default_value']);
                }

                if ($field['type'] == 'enum') {
                    $field['values'] = explode("\n", $field['enum_list']);
                }
                elseif ($field['type'] == 'fk' && $field['fk_in'] > 0 && !in_array($field['name'], $exclude_fks)) {

                    $qt = $this->db->get_where("wa_tables", array("id" => $field['fk_in']));
                    $fk_table = $qt->row_array();

                    $field['fk_table'] = $fk_table;
                    if ($fk_table['tree'] == 'Yes') {
                        $total = $this->db->count_all_results($fk_table['name']);
                        $cols = array($fk_table['pk'], $fk_table['name_equiv_for_pk']);
                        $this->get_recursive($fk_table, $field['values'], 0, $fk_table['tree_parent'], $total, 0, $cols);
                    }
                    else {
                        $this->db->order_by(($fk_table['order_by'] ? $fk_table['order_by'] : $fk_table['name_equiv_for_pk']), $fk_table['order_how']);
                        $fields = ((strlen($fk_table['display_fields'])) ? "CONCAT_WS(' - ', {$fk_table['display_fields']}) as `{$fk_table['name_equiv_for_pk']}`" : $fk_table['name_equiv_for_pk']);
                        $this->db->select($fk_table['pk'] .", ". $fields, false);
                        $qt = $this->db->get($fk_table['name']);

                        $field['values'] = $qt->result_array();
                    }
                }
                elseif (($field['type'] == 'file' || $field['type'] == 'image') && is_array($values))
                    $field['wa_thumb'] = $this->get_thumb('../'. $values[$field['name']]);

                $res[$key] = $field;
            }


            return $res;
        }

        return null;
    }

    function get_field($id) {
        $q = $this->db->get_where("wa_cols", array("id" => $id));

        if ($q->num_rows())
            return $q->row_array();

        return null;
    }

    function get_records($collection, $setup = array()) {

        $page = max((int) $setup['page'], 1);
        $rpp = $collection['per_page'];

        $results = array();

        if ($collection['tree'] == 'Yes') {
            $last = min($this->db->count_all_results($collection['name']), ($page - 1)*$rpp + $rpp);
            $this->get_recursive($collection, $results, 0, '', $last);

            $results = array_slice($results, ($page - 1) * $rpp, $rpp);
        }
        else {
            $order_by = (strlen($collection['order_by']) ? $collection['order_by'] : $collection['name_equiv_for_pk']);
            $concat = "";
            $this->db->select("* $concat", false)
                    ->from($collection['name'])
                    ->order_by($order_by, $collection['order_how']);

            if ((int) $rpp > 0)
                $this->db->limit($rpp, ($page - 1) * $rpp);

            if (is_array($setup['where']))
                foreach ($setup['where'] as $key => $value)
                    switch ($key ) {
                        case "where_in" : $this->db->where_in($value[0], $value[1]);
                                            break;
                        case "like"     : $this->db->like($value[0], $value[1]);
                                            break;
                        default         : $this->db->where($key, $value);
                    }

            $r = $this->db->get();

            if ($r->num_rows()) {
                foreach ($r->result_array() as $row) {
                    foreach ($collection['fields'] as $field) {
                        if ($field['type'] == 'fk' && $field['relevant'] == 'Yes') {
                            $this->db->select("name, name_equiv_for_pk, pk")
                                    ->from("wa_tables")
                                    ->where("id", $field['fk_in']);
                            $q = $this->db->get();

                            $fk_table = $q->row_array();

                            $this->db->select($fk_table['name_equiv_for_pk'])
                                    ->from($fk_table['name'])
                                    ->where($fk_table['pk'], $row[$field['name']]);
                            $q = $this->db->get();

                            $fk_row = $q->row_array();

                            $row['select_id'] = $row[$field['name']];
                            $row[$field['name']] = $fk_row[$fk_table['name_equiv_for_pk']];
                        }
                        elseif ($field['type'] == 'image' || $field['type'] == 'file') {
                            $row['wa_thumb'] = $this->get_thumb('../'. trim($row[$field['name']], "/"));
                        }
                    }

                    if (strlen($collection['display_fields'])) {
                        $fields = explode(",", str_replace(" ", "", $collection['display_fields']));
                        foreach ($fields as $tmp_field)
                            $equiv .= " - ". $row[$tmp_field];
                        $row['name_equiv_for_pk'] = substr($equiv, 2);
                    }

                    $results[] = $row;
                }
            }
        }

        return $results;
    }

    function get_recursive($collection, &$results, $parent_id, $parent_name, $last, $depth = 0, $cols = array(), $setup = array()) {

        $order_by = (strlen($collection['order_by']) ? $collection['order_by'] : $collection['name_equiv_for_pk']);

        $select = (count($cols) ? implode(", ", $cols) : "*");
        $this->db->select($select)
                ->from($collection['name'])
                ->where($collection['tree_parent'], $parent_id)
                ->order_by($order_by, $collection['order_how']);

        if (is_array($setup['where']))
                foreach ($setup['where'] as $key => $value)
                    switch ($key ) {
                        case "where_in" : $this->db->where_in($value[0], $value[1]);
                                            break;
                        case "like"     : $this->db->like($value[0], $value[1]);
                                            break;
                        default         : $this->db->where($key, $value);
                    }

        $r = $this->db->get();

        if ($r->num_rows())
            foreach ($r->result_array() as $item) {

                $item[$collection['tree_parent']] = $parent_name;
                $item['depth'] = $depth;

                if (count($results) >= ($last))
                    return ;

                $results[] = $item;

                $this->get_recursive($collection, $results, $item[$collection['pk']], $item[$collection['name_equiv_for_pk']], $last, $depth + 1, $cols, $setup);
            }

        return ;
    }

    function get_record($collection, $row_id) {

        $q = $this->db->get_where($collection['name'], array($collection['pk'] => $row_id));

        if ($q->num_rows())
            return $q->row_array();

        return null;
    }

    function get_tabs($collection_id, $row_id = 0) {

        $tabs = array();

        // get fk_tabs
        $this->db->order_by('order');
        $r = $this->db->get_where('wa_tabs', array('parent_id' => $collection_id));

        if ($r->num_rows()) {
            foreach ($r->result_array() as $item) {
                $tab = $this->get($item['tab_id']);
                $tab['multiple_upload'] = $item['multiple_upload'];
                $tab['tab_type'] = 'fk';
                $tab['caption'] = $item['caption'];
                $tab['parent_id'] = '__FK__';
                $tab['parent_pk'] = $item['parent_pk'];
                $tab['tab_fk'] = $item['tab_fk'];
                $tab['fields'] = $this->get_fields($tab['id'], null, false, array($item['tab_fk']));

                if ((int) $row_id > 0) {
                    $setup = array("where" => array($tab['tab_fk'] => (int) $row_id));
                    $tab['records'] = $this->get_records($tab, $setup);

                    $tab['parent_id'] = (int) $row_id;

                    $this->db->where($tab['tab_fk'], (int) $row_id);
                    $tab['all_records'] = $this->db->count_all_results($tab['name']);
                }

                $tabs[] = $tab;
            }
        }

        // get assoc_tabs
        $this->db->order_by('order');
        $r = $this->db->get_where('wa_assoc', array('tbl_1' => $collection_id));

        if ($r->num_rows()) {
            foreach ($r->result_array() as $item) {
                $tab = $this->get($item['tbl_assoc']);
                $tab['multiple_upload'] = $item['multiple_upload'];
                $tab['tab_type'] = 'assoc';
                $tab['caption'] = $item['caption'];
                $tab['tab_fk'] = $item['fk_1'];
                $tab['fk_2'] = $item['fk_2'];
                $tab['parent_id'] = '__FK__';

                $tab['fields'] = $this->get_fields($tab['id'], null, false, array($item['fk_1']));

                if ((int) $row_id > 0) {
                    $setup = array(
                                "page" => 1,
                                "per_page" => 50,
                                "where" => array($tab['tab_fk'] => (int) $row_id));
                    $tab['records'] = $this->get_records($tab, $setup);

                    $tab['parent_id'] = (int) $row_id;

                    $this->db->where($tab['tab_fk'], (int) $row_id);
                    $tab['all_records'] = $this->db->count_all_results($tab['name']);
                }

                $tabs[] = $tab;
            }
        }

        return $tabs;
    }

    function get_tab_details($collection_id, $tab_id) {

        $r = $this->db->get_where("wa_tabs", array("parent_id" => $collection_id, "tab_id" => $tab_id));

        if ($r->num_rows()) {

            return $r->row_array();
        }

        return null;
    }

    function get_thumb($file_path) {

        $parts = pathinfo($file_path);

        switch (strtolower($parts['extension'])) {
            case "jpg"  :
            case "jpeg" :
            case "png"  :
            case "tif"  :
            case "tiff" :
            case "bmp"  :
            case "gif"  :   $thumb = $this->image_lib->make_thumb($file_path, 75, 75);
                            break;

            case "avi"  :
            case "mov"  :
            case "mpeg" :
            case "swf"  :
            case "flv"  :
            case "mp4"  :   $thumb = "img/icon-video.png";
                            break;

            case "mp3"  :
            case "mp2"  :
            case "aif"  :
            case "aiff" :
            case "aifc" :
            case "mid"  :
            case "midi" :
            case "wav"  :   $thumb = "img/icon-audio.png";
                            break;

            case "doc"  :
            case "docx" :
            case "xlsx" :
            case "xls"  :
            case "rtf"  :   $thumb = "img/icon-doc.png";
                            break;

            case "pdf"  :   $thumb = "img/icon-pdf.png";
                            break;

            case "zip"  :
            case "rar"  :
            case "tar"  :
            case "gz"   :
            case "tgz"  :
            case "ace"  :   $thumb = "img/icon-archive.png";
                            break;

            default     :   $thumb = "img/icon-file.png";
        }

        return $thumb;
    }

    function count_all_results($setup) {

        if (is_array($setup))
            foreach ($setup['where'] as $key => $value)
                switch ($key ) {
                    case "where_in" : $this->db->where_in($value[0], $value[1]);
                                        break;
                    case "like"     : $this->db->like($value[0], $value[1]);
                                        break;
                    default         : $this->db->where($key, $value);
                }

        return $this->db->count_all_results($setup['collection_name']);
    }
}

/* End of file collections_model.php */
/* Location ./admin/application/models/collections_model.php */