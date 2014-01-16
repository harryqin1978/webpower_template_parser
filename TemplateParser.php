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
    protected $divider = "========================";

    function __construct($origin) {
        $this->origin = $origin;
    }

    public function getOrigin() {
      return $this->origin;
    }

    protected function parseJson($json_str) {
        $json_str = preg_replace("/([a-zA-Z0-9_]+?):/" , "\"$1\":", $json_str);
        $json_array = json_decode($json_str, true);
        return $json_array;
    }

    abstract public function parseTest();
    abstract protected function parseJsonArray($json_array);
}



class CkeditorTemplateParser extends TemplateParser
{
    public function parseTest() {
        $dom = str_get_html($this->origin);
        $output = '';
        foreach($dom->find('div[data-widget-id]') as $e) {
            $output .= 'widget-id:\'' . $e->getAttribute('data-widget-id') . '\'' . "\n";
            $output .= 'widget-type:\'' . $e->getAttribute('data-widget-type') . '\'' . "\n";
            $output .= 'widget-name:\'' . (isset($this->plugin_file_guid_mappers[$e->getAttribute('data-widget-id')]) ? $this->plugin_file_guid_mappers[$e->getAttribute('data-widget-id')] : 'undefined') . '\'' . "\n";
            $output .= 'widget-role:\'' . $e->getAttribute('data-widget-role') . '\'' . "\n";
            $value = $e->getAttribute('data-widget-bind');
            if (substr($value, 0, 1) == '{') {
                $json_array = $this->parseJson($value);
                $real_value = "\n" . '{' . "\n" . $this->parseJsonArray($json_array) . '}';
            } else {
                $real_value = '\'' . $value . '\'';
            }
            $output .= 'widget-bind:' . $real_value . '' . "\n";
            $output .= 'widget-language:\'' . $e->getAttribute('data-widget-language') . '\'' . "\n";
            $output .= 'widget-class:\'' . $e->getAttribute('data-widget-class') . '\'' . "\n";
            $output .= $this->divider . "\n";
        }
        return $output;
    }

    protected function parseJsonArray($json_array, $depth = 0) {
        $output = '';
        foreach ($json_array as $key => $value) {
            if (is_array($value)) {
                $output .= str_repeat(' ', $depth*2+2) . $key . ':' . "\n" . $this->parseJsonArray($value, $depth+1);
            } else {
                if (is_bool($value)) {
                    $value = (int) $value;
                }
                $output .= str_repeat(' ', $depth*2+2) . $key . ':' . (is_string($value) ? '"'.$value.'"' : $value) . "\n";
            }
        }
        return $output;
    }
}