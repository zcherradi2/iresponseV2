
if(is_array($_data) && sizeof($_data))
extract($_data); 
$_text = [];
$_text[] = "<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/scripts/global/iresponse.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/scripts/global/datatable.min.js\" type=\"text/javascript\"></script>";
return implode($_text);
