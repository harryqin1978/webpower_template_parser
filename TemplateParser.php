<?php

require_once('../simplehtmldom/simple_html_dom.php');



abstract class TemplateParser
{
    protected $origin = '';
    protected $plugin_file_guid_mappers = array(
        '52d5fb7ab2827' => 'addtosafesenderlist.php',
        '52d5fb7ab289e' => 'agenda_service.php',
        '52d5fb7ab290b' => 'edit_data.php',
        '52d5fb7ab2979' => 'last_mailing.php',
        '52d5fb7ab29e5' => 'list_help.php',
        '52d5fb7ab2a50' => 'mail_a_friend.php',
        '52d5fb7ab2aba' => 'mailing_archive.php',
        '52d5fb7ab2b26' => 'overall_subscribe.php',
        '52d5fb7ab2b91' => 'poll_results.php',
        '52d5fb7ab2bfd' => 'script_check_email.php',
        '52d5fb7ab2c77' => 'script_subscribe.php',
        '52d5fb7ab2ce3' => 'script_unsubscribe.php',
        '52d5fb7ab2d4e' => 'subscribe.php',
        '52d5fb7ab2db9' => 'unsubscribe.php'
    );

    function __construct($origin) {
        $this->origin = $origin;
    }

    public function getOrigin() {
      return $this->origin;
    }

    protected function parseJson($json_str) {
        $json_str = preg_replace("/([a-zA-Z0-9_]+?):/" , "\"$1\":", $json_str);
        $json_array = json_decode($json_str, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json_array;
        } else {
            return false;
        }
    }

    abstract public function parseTest();
    abstract protected function parseJsonArray($json_array);
}



class CkeditorTemplateParser extends TemplateParser
{

    protected $divider = '';
    protected $dom_indent = 4;
    protected $json_indent = 2;

    function __construct($origin) {
        parent::__construct($origin);
        $this->divider = str_repeat('=', 40);
    }

    public function parseTest($dom = null, $depth = 0, $maxdepth = 0) {
        if ($maxdepth && $depth >= $maxdepth) {
            return '';
        }
        if ($dom && $dom->hasAttribute('id')) {
            $parent_id = $dom->getAttribute('id');
        }
        if (!$dom) {
            $dom = str_get_html($this->origin);
        }
        $output = '';
        $children = $dom->childNodes();
        foreach($children as $e) {
            if ($e->hasAttribute('data-widget-type')) {
                $dom_indent_str = str_repeat(' ', $this->dom_indent * $depth);
                $output .= $dom_indent_str . 'widget-depth:' . ($depth + 1) . "\n";
                $output .= $dom_indent_str . 'widget-id:\'' . $e->getAttribute('id') . '\'' . "\n";
                if (isset($parent_id)) {
                    $output .= $dom_indent_str . 'widget-parent-id:\'' . $parent_id . '\'' . "\n";
                }
                $output .= $dom_indent_str . 'widget-type:\'' . $e->getAttribute('data-widget-type') . '\'' . "\n";
                $output .= $dom_indent_str . 'widget-name:\'' . (isset($this->plugin_file_guid_mappers[$e->getAttribute('id')]) ? $this->plugin_file_guid_mappers[$e->getAttribute('id')] : 'undefined') . '\'' . "\n";
                $output .= $dom_indent_str . 'widget-role:\'' . $e->getAttribute('data-widget-role') . '\'' . "\n";
                $value = $e->getAttribute('data-widget-bind');
                if (substr($value, 0, 1) == '{') {
                    $json_array = $this->parseJson($value);
                    if ($json_array) {
                        // $real_value = "\n" . $dom_indent_str . '{' . "\n" . $this->parseJsonArray($json_array, 0, $depth) . $dom_indent_str . '}' . "\n";
                        $result = trim(print_r($json_array, true));
                        $results = explode("\n", $result);
                        for ($i=0; $i<count($results); $i++) {
                            $results[$i] = $dom_indent_str . $results[$i];
                        }
                        $result = implode("\n", $results);
                        $real_value = "\n" . $result . "\n";
                    } else {
                        $real_value = '\'' . '[JSON_PARSE_ERROR]' . '\'' . "\n";
                    }
                } else {
                    $real_value = '\'' . $value . '\'' . "\n";
                }
                $output .= $dom_indent_str . 'widget-bind:' . $real_value;
                $output .= $dom_indent_str . 'widget-language:\'' . $e->getAttribute('data-widget-language') . '\'' . "\n";
                $output .= $dom_indent_str . 'widget-class:\'' . $e->getAttribute('data-widget-class') . '\'' . "\n";
                $output .= $this->divider . "\n";
                $output .= $this->parseTest($e, $depth + 1, $maxdepth);
            }
        }
        return $output;
    }

    protected function parseJsonArray($json_array, $depth = 0, $dom_depth = 0) {
        $output = '';
        foreach ($json_array as $key => $value) {
            $json_depth_str = str_repeat(' ', $depth * $this->json_indent + $this->json_indent + $this->dom_indent * $dom_depth);
            if (is_array($value)) {
                $output .= $json_depth_str . $key . ':' . "\n" . $this->parseJsonArray($value, $depth + 1, $dom_depth);
            } else {
                if (is_bool($value)) {
                    $value = (int) $value;
                }
                $output .= $json_depth_str . $key . ':' . (is_string($value) ? '"' . $value . '"' : $value) . "\n";
            }
        }
        return $output;
    }
}