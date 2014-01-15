<?php


require_once('../simplehtmldom/simple_html_dom.php');

$html = 'aaaaa<div data-widget-type="form" data-widget-id="52d5fb7ab2979" data-variable="">dasfasdfasdfasdf</div>bbbbb';
$tp = new TemplateParser($html);
echo '<textarea cols="150" rows="50">' . $tp->parseTest() . '</textarea>';

class TemplateParser
{
    private $origin = '';
    private $plugin_file_guid_mappers = array(
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

    public function parseTest() {
        $dom = str_get_html($this->origin);
        foreach($dom->find('div[data-widget-id]') as $e) {
            $attrs = $e->getAllAttributes();
            $attrs_php = '$attrs = array(' . "\n";
            foreach ($attrs as $key => $value) {
                $attrs_php .= '  $' . $key . ' => \'' . $value . '\',' . "\n";
            }
            $attrs_php .= ');';
            $e->outertext =
                "\n" . 
                '<?php' . "\n" . 
                $attrs_php . "\n" .
                'include("' . $this->plugin_file_guid_mappers[$e->getAttribute('data-widget-id')] . '");' . "\n" .
                '?>' . "\n";
        }
        return $dom->save();
    }

}
