<?php

require_once('TemplateParser.php');

if (isset($_POST['input'])) {
  $html = $_POST['input'];
} else {
  $html = '<div data-widget-type="form" id="52d5fb7ab2979" data-widget-role="allen,ramon" data-widget-bind=\'{strvar:"string", number:40, boolvar:true, subobject:{substrvar:"sub string", subsubobj:{deep:"deeply nested"}, strnum:"56"}, false_val:false, false_str:"false"}\' data-widget-language="nl" data-widget-class="myclass">dasfasdfasdfasdf</div>
<p>Hello</p>
<div data-widget-type="form" id="52d5fb7ab29e5" data-widget-bind="">dasfasdfasdfasdf<div data-widget-type="plugin" id="blabla" data-widget-bind="{a:1,b:2}">dasfasdfasdfasdf</div></div>';
}

if (isset($_POST['maxdepth'])) {
  $maxdepth = (int) $_POST['maxdepth'];
} else {
  $maxdepth = 0;
}

$tp = new CkeditorTemplateParser($html);
?>
<form method="post">
  Input: (You can modify input and click submit to see output change or <a href="TemplateParserTest.php">reset</a> to origin sample)<br /><textarea cols="150" rows="15" name="input"><?php echo $tp->getOrigin() ?></textarea><br />
  MaxDepth: (0 means unlimited)<br /><select name="maxdepth"><?php for ($i = 0; $i <= 10; $i ++) { ?><option value="<?php print $i; ?>"<?php if ($maxdepth == $i) { ?> selected="selected"<?php } ?>><?php print $i; ?></option><?php } ?></select><br />
  Output: <br /><textarea cols="150" rows="40" readonly="readony"><?php echo $tp->parseTest(null, 0, $maxdepth) ?></textarea><br />
  <input type="submit" value="Submit">
</form>
