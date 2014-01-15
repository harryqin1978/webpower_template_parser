<?php


require_once('../simplehtmldom/simple_html_dom.php');

$html = '<div data-widget-type="form" data-widget-id="52d5fb7ab2979" data-variable=\'{a:10,b:"strimg",c:true}\'>dasfasdfasdfasdf</div>
<p>Hello</p>
<div data-widget-type="form" data-widget-id="52d5fb7ab29e5" data-variable="">dasfasdfasdfasdf</div>
<div data-widget-type="plugin" data-widget-id="blabla" data-variable="">dasfasdfasdfasdf</div>';
$tp = new CkeditorTemplateParser($html);
echo 'Input: <br /><textarea cols="150" rows="25">' . $tp->getOrigin() . '</textarea><br />';
echo 'Output: <br /><textarea cols="150" rows="25">' . $tp->parseTest() . '</textarea><br />';



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

    abstract public function parseTest();
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
            $value = $e->getAttribute('data-variable');
            if (substr($value, 0, 1) == '{') {
                $real_value = "\n" . '{' . "\n";
                $inner_value = trim($value, '{}');
                $inner_values = explode(',', $inner_value);
                foreach ($inner_values as $v) {
                    $real_value .= '  ' . $v . "\n";
                }
                $real_value .= '}';
            } else {
                $real_value = '\'' . $value . '\'';
            }
            $output .= 'variable:' . $real_value . '' . "\n";
            $output .= $this->divider . "\n";
        }
        return $output;
    }
}