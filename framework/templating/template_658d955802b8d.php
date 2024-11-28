
if(is_array($_data) && sizeof($_data))
extract($_data); 
$_text = [];
$_text[] = "<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/styles/pages/login.min.css\" rel=\"stylesheet\" type=\"text/css\" />
<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/styles/custom.min.css\" rel=\"stylesheet\" type=\"text/css\" />";
return implode($_text);
