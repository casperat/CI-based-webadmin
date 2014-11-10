<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Image_lib extends CI_Image_lib {

    var $default_file = "na.gif";
    var $default_folder = "../files";
    var $trim_canvas = false;
    var $fill_color = true;
    var $color_fill = array (255, 255, 255);
    var $trim_position = "center center";

    function __construct() {
        parent::__construct();
    }

    function clear() {

        parent::clear();

        $this->default_file = "na.gif";
        $this->default_folder = "../files";
        $this->trim_canvas = false;
        $this->fill_color = true;
        $this->color_fill = array (255, 255, 255);
        $this->trim_position = "center center";
    }

    function get_thumb($filename, $width = 50, $height = 50) {

        $default_folder = $this->default_folder;
        $default_image = $this->default_image;

        if ( ! is_file($filename))
            $filename = $default_folder ."/". $default_file;

        $folder = dirname($filename);
        $file = basename($filename);

        if (strlen($folder) == 0)
            $folder = $default_folder;

        if (is_file($folder ."/thumbs{$width}x{$height}/". $file)) {

            return $folder ."/thumbs{$width}x{$height}/". $file;
        }

        list($orig_width, $orig_height, $type, $attr) = GetImageSize($folder . "/" . $file);
        $orig_file = $folder . "/" . $file;

        if (($orig_width == $width) && ($orig_height == $height)) {
            return $orig_file;
        }

        if ( ! is_dir($folder ."/thumbs{$width}x{$height}")) {
            @mkdir($folder ."/thumbs{$width}x{$height}");
            @chmod($folder ."/thumbs{$width}x{$height}", 0777);
        }

        $this->create_thumb = true;
        $this->new_image = $folder ."/thumbs{$width}x{$height}/". $file;
        $this->width = $width;
        $this->height = $height;

        if ( ! $this->resize()) {
            echo $this->display_errors();

            return FALSE;
        }

        return $folder ."/thumbs{$width}x{$height}/". $file;
    }

    /**
    * make a thumbnail or return an allready created one
    *
    * @param mixed $filename
    * @param mixed $width - int or 'auto'
    * @param mixed $height - int or 'auto'
    * @param string $copyright - whether to have a copyright bar at the bottom or not
    *
    * @return string thumb src
    */
    function make_thumb($filename, $width = 50, $height = 50, $replace = false, $copyright = false) {

        if ( ! is_file($filename)) {
            $filename = $this->default_folder ."/". $this->default_file;
            $replace = false;
        }

        $folder = dirname($filename);
        $file = basename($filename);

        if (strlen($folder) == 0)
            $folder = $this->default_folder;

        list($orig_width, $orig_height, $type, $attr) = GetImageSize($folder . "/" . $file);
        $orig_file = $folder . "/" . $file;

        // calculate sizez for auto
        if (($width == 'auto' && $height == 'auto') || ((int) $width == 0 && (int) $height == 0)) {
            $width = 50;
            $height= 50;
        }
        elseif ($width == 'auto' || (int) $width == 0) {
            $width = round($orig_width * $height / $orig_height);
        }
        elseif ($height == 'auto' || (int) $height == 0) {
            $height = round($orig_height * $width / $orig_width);
        }

        if (is_file($folder ."/thumbs{$width}x{$height}/". $file) && ! $replace) {

            return $folder ."/thumbs{$width}x{$height}/". $file;
        }

        if (($orig_width == $width) && ($orig_height == $height)) {
            $result = $orig_file;
        }
        else {

//        condition with web server root path
//        if ((strlen($file) < 1) || (!file_exists(rtrim($this->config->item("root_path") . $folder, "/") . "/" . ltrim($file, "/")))) {
            if ((strlen($file) < 1) || (!file_exists(rtrim($folder, "/") . "/" . ltrim($file, "/")))) {

                // FILE NOT FOUND
                return null;

                //trigger_error("make_thumbnail :: File not found (<b>$file</b> in folder <b>$folder</b>).", E_USER_ERROR);
            }

            if ($this->fill_color)
                $this->trim_canvas = false;

            if (($type < 1) || ($type > 3)) {
                return $this->make_thumb("", $width, $height);
            }

            if ($file == "na.gif") {
                $this->trim_canvas = false;
                $this->fill_color = true;
                $this->color_fill = array(243, 243, 243);
            }

            $img = $this->ImageCreateFromType($type, $folder . "/" . $file);

            $new_width = $orig_width;
            $new_height = $orig_height;

            $per_x = $new_width / $width;
            $per_y = $new_height / $height;

            $pos_x = 0;
            $pos_y = 0;

            if ($this->trim_canvas)
            {
                $use_per = $per_y;
                if ($per_y < $per_x)
                {
                    $new_width = $new_width / $per_y;
                    $new_height = $new_height / $per_y;
                    $use_per = $per_y;
                }
                else
                {
                    $new_width = $new_width / $per_x;
                    $new_height = $new_height / $per_x;
                    $use_per = $per_x;
                }

                // var_dump($new_width, $new_height);

                list($trim_pos_x, $trim_pos_y) = explode(" ", $this->trim_position, 2);

                $pos_x = ($orig_width - $width*$use_per) / 2;
                $pos_y = ($orig_height - $height*$use_per) / 2;

                // var_dump($height, $new_height);

                $canvas_new_width = $width;
                $canvas_new_height = $height;
            }
            else
            {
                if (($new_width >= $width) || ($new_height >= $height))
                {
                    $use_per = $per_y;
                    if ($per_y > $per_x)
                    {
                        $new_width = $orig_width / $per_y;
                        $new_height = $orig_height / $per_y;
                        $use_per = $per_y;
                    }
                    else
                    {
                        $new_width = $orig_width / $per_x;
                        $new_height = $orig_height / $per_x;
                        $use_per = $per_x;
                    }

                    $pos_x = 0;
                    $pos_y = 0;
                }
            }

            // if the original dimensions are smaller than the target ones,
            // the picture is centered and filled with $fillcolor
            if (($new_width > $orig_width) || ($new_height > $orig_height))
            {
                $this->fill_color = true;
                $this->trim_canvas = false;

                if ($new_width > $orig_width)
                {
                    $pos_x = ($new_width - $orig_width) / 2;
                    $new_width = $orig_width;
                }
                if ($new_height > $orig_height)
                {
                    $pos_y = ($new_height - $orig_height) / 2;
                    $new_height = $orig_height;
                }
            }
            else if ($this->fill_color)
            {
                $pos_x = abs(($width - $new_width) / 2);
                $pos_y = abs(($height - $new_height) / 2);
            }

            if ($type == 1)
            {
                if ($this->trim_canvas || $this->fill_color)
                    $ni = imagecreate($width, $height);
                else
                    $ni = imagecreate($new_width, $new_height);
            }
            else
            {
                if ($this->trim_canvas || $this->fill_color)
                    $ni = ImageCreateTrueColor($width, $height);
                else
                    $ni = ImageCreateTrueColor($new_width, $new_height);
            }

            $white = imagecolorallocate($ni, $this->color_fill[0], $this->color_fill[1], $this->color_fill[2]);

            if ($this->trim_canvas || $this->fill_color)
                imagefilledrectangle($ni, 0, 0, $width, $height, $white);
            else
                imagefilledrectangle($ni, 0, 0, $new_width, $new_height, $white);
            imagepalettecopy($ni,$img);

            if ($this->trim_canvas)
            {
                $src_x = ($trim_pos_x == "left" ? 0 : $pos_x);
                $src_y = ($trim_pos_y == "top" ? 0 : $pos_y);

                imagecopyresampled(
                    $ni, $img,
                    0, 0, $src_x, $src_y,
                    $width, $height,
                    $orig_width - (2 * $pos_x), $orig_height - (2 * $pos_y));
            }
            else
            {
                imagecopyresampled(
                    $ni, $img,
                    $pos_x, $pos_y, 0, 0,
                    $new_width, $new_height,
                    $orig_width, $orig_height);
            }
            @imagedestroy($img);

            if ($replace) {

                $this->save_image($type, $ni, $folder . "/" . $file, 90);

                $result = $folder ."/". $file;
            }
            else {
                //@chmod ($folder, 0777);
                if (!is_dir($folder . "/thumbs" . $width . "x$height"))
                {
                    @mkdir ($folder . "/thumbs" . $width . "x$height");
                    @chmod ($folder . "/thumbs" . $width . "x$height", 0777);
                }
                if (strpos($folder, "/") != -1)
                {
                    $new_dir = substr($file, 0, strrpos($file, "/") + 1);
                    $this->change_mod($new_dir, $folder . "/thumbs" . $width . "x$height");
                }

                $this->save_image($type, $ni, $folder . "/thumbs" . $width . "x$height/$file", 90);

                //@imagedestroy($ni);

                $result = $folder . "/thumbs" . $width . "x$height/$file";
            }
        }

        if ($copyright) {
            $this->copyright_image($result);
        }

        @imagedestroy($ni);

        return $result;
    }

    private function ImageCreateFromType($type,$filename) {
        $im = null;
        switch ($type) {
            case 1:
                $im = ImageCreateFromGif($filename);
                break;
            case 2:
                $im = ImageCreateFromJpeg($filename);
                break;
            case 3:
                $im = ImageCreateFromPNG($filename);
                break;
        }
        return $im;
    }

    private function save_image($type, $im, $filename, $quality, $to_file = true) {
        $res = null;

        if( ! function_exists('imagegif'))
            $type = 3;

        switch ($type) {
            case 1:
                $res = ImageGIF($im,$filename);
                break;
            case 2:
                $res = ImageJPEG($im,$filename,$quality);
                break;
            case 3:
                if (PHP_VERSION >= '5.1.2')
                {
                    $quality = 9 - min( round($quality / 10), 9 );
                    $res = ImagePNG($im, $filename, $quality);
                }
                else
                    $res = ImagePNG($im, $filename);
                break;
        }

        @imagedestroy($im);
    }

    private function change_mod($dir, $start_dir) {
        while (strlen($dir) > 0)
        {
            $temp_dir = substr($dir, 0, strpos($dir, "/"));
            $start_dir .= "/$temp_dir";
            if (!is_dir($start_dir))
            {
                @mkdir($start_dir);
                @chmod ($start_dir, 0777);
            }
            $dir = substr ($dir, strpos($dir, "/") + 1);
        }
    }

    function delete_image($file, $delete_thumbs = true) {

        if (is_file($file)) {
            $CI =& get_instance();
            $CI->load->helper('file');

            $folder = dirname($file);
            $filename = basename($file);
            unlink($file);

            if ($delete_thumbs) {
                $files = get_filenames($folder, true);

                foreach ($files as $item) {
                    $item = trim(str_replace(array($folder, $CI->config->item("root_path")), "", $item), "/");
                    if (substr($item, 0, 6) == "thumbs" && strpos($item, "/") > -1 && strstr($item, $filename))
                        unlink($folder ."/". $item);
                }
            }

            return TRUE;
        }

        return FALSE;
    }

    function copyright($text, $image_file) {

        list($orig_width, $orig_height, $type, $attr) = GetImageSize($image_file);
        $dest = $this->ImageCreateFromType($type, $image_file);

        $src = @imagecreate($orig_width, 25)
              or die('Cannot Initialize new GD image stream');
        $bg = imagecolorallocate($src, 0, 0, 0);
        $text_color = imagecolorallocate($src, 255, 255, 255);
        imagestring($src, 3, 10, 5,  $text, $text_color);

        $success = imagecopymerge($dest, $src, 0, ($orig_height - 25), 0, 0, $orig_width, 25, 100);

        $this->save_image($type, $dest, $image_file, 100);

        imagedestroy($src);
    }

    function copyright_image($image) {

        list($orig_width, $orig_height, $type, $attr) = GetImageSize($image);
        $dest = $this->ImageCreateFromType($type, $image);

        list($width, $height, $type, $attr) = GetImageSize("files/copyright_bar.jpg");
        $src = $this->ImageCreateFromType($type, "files/copyright_bar.jpg");

        $success = imagecopymerge($dest, $src, 0, ($orig_height - $height), 0, 0, $width, $height, 100);

        $this->save_image($type, $dest, $image, 100);

        @imagedestroy($src);
    }
}

/* End of file My_image_lib.php */
/* ./application/libraries/My_image_lib.php */