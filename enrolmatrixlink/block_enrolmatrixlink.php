<?php

class block_enrolmatrixlink extends block_base
{

    public function get_content()
    {
        if (!empty($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) {
            $uri = 'https://';
        } else {
            $uri = 'http://';
        }
        $uri .= $_SERVER['HTTP_HOST'];
        $uri .= '/moodle/mod/enrolmatrix';

        $this->content = new stdClass();
        // $this->content->header = 'HEADER';
        $this->content->text = '<a href="' . $uri . '">' . get_string('openmatrix', 'block_enrolmatrixlink') . '</a>';
        // $this->content->footer = 'FOOTER';

        return $this->content;
    }

    public function init()
    {
        $this->title = get_string('blocktitle', 'block_enrolmatrixlink');
    }

    public function instance_allow_multiple()
    {
        return true;
    }
}
