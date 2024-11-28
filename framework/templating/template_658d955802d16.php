
if(is_array($_data) && sizeof($_data))
extract($_data); 
$_text = [];
$_text[] = "<!--[if lt IE 9]>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/respond.min.js\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/excanvas.min.js\"></script> 
<![endif]-->
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/jquery.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap/js/bootstrap.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/js.cookie.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/jquery-slimscroll/jquery.slimscroll.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/jquery.blockui.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap-switch/js/bootstrap-switch.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap-sweetalert/sweetalert.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap-toastr/toastr.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap-select/js/bootstrap-select.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/bootstrap-tabdrop/js/bootstrap-tabdrop.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/moment.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js\" type=\"text/javascript\"></script>
<script src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/plugins/widearea/widearea.js\" type=\"text/javascript\"></script> ";
return implode($_text);
