
if(is_array($_data) && sizeof($_data))
extract($_data); 
$_text = [];
$_text[] = "<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/font-awesome/css/font-awesome.min.css\" rel=\"stylesheet\" type=\"text/css\" />
<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/fontawesome5/css/all.min.css\" rel=\"stylesheet\" type=\"text/css\" />
<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/simple-line-icons/simple-line-icons.min.css\" rel=\"stylesheet\" type=\"text/css\" />
<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\" type=\"text/css\" />
<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap-sweetalert/sweetalert.css\" rel=\"stylesheet\" type=\"text/css\"/>
<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap-toastr/toastr.min.css\" rel=\"stylesheet\" type=\"text/css\"/>
<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap-switch/css/bootstrap-switch.min.css\" rel=\"stylesheet\" type=\"text/css\" />
<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap-select/css/bootstrap-select.min.css\" rel=\"stylesheet\" type=\"text/css\" />
<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/widearea/widearea.css\" rel=\"stylesheet\" type=\"text/css\" />
<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/styles/components.min.css\" rel=\"stylesheet\" type=\"text/css\"/>
<link href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/styles/plugins.min.css\" rel=\"stylesheet\" type=\"text/css\" />";
return implode($_text);
