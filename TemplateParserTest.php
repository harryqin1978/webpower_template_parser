<?php

require_once('TemplateParser.php');

$html = '<div data-widget-type="form" id="52d5fb7ab2979" data-widget-role="allen,ramon" data-widget-bind=\'{strvar:"string", number:40, boolvar:true, subobject:{substrvar:"sub string", subsubobj:{deep:"deeply nested"}, strnum:"56"}, false_val:false, false_str:"false"}\' data-widget-language="nl" data-widget-class="myclass">dasfasdfasdfasdf</div>
<p>Hello</p>
<div data-widget-type="form" id="52d5fb7ab29e5" data-widget-bind="">dasfasdfasdfasdf<div data-widget-type="plugin" id="blabla" data-widget-bind="{a:1,b:2}">dasfasdfasdfasdf</div></div>';
$tp = new CkeditorTemplateParser($html);
echo 'Input: <br /><textarea cols="150" rows="15">' . $tp->getOrigin() . '</textarea><br />';
echo 'Output: <br /><textarea cols="150" rows="40">' . $tp->parseTest() . '</textarea><br />';
