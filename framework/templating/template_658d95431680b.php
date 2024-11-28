
if(is_array($_data) && sizeof($_data))
extract($_data); 
$_text = [];
$_text[] = "<!DOCTYPE html>
<!--[if IE 8]> <html lang=\"en\" class=\"ie8 no-js\"> <![endif]-->
<!--[if IE 9]> <html lang=\"en\" class=\"ie9 no-js\"> <![endif]-->
<!--[if !IE]><!-->
<html lang=\"en\">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset=\"utf-8\" />
        <title>";
$_text[] =  $app['name'];
$_text[] = "</title>
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
        <meta content=\"width=device-width, initial-scale=1\" name=\"viewport\" />
        <meta content=\"IR Team\" name=\"author\" />
         
        <!-- BEGIN GLOBAL FONTS STYLES -->
        ";function anon_wFLXLmJd($_data){};
$_text[] = anon_wFLXLmJd($_data);
$_text[] = "
        <!-- END GLOBAL FONTS STYLES -->
        
        <!-- BEGIN PAGE GLOBAL STYLES -->
        ";function anon_QvnOVixJ($_data){};
$_text[] = anon_QvnOVixJ($_data);
$_text[] = "
        <!-- END PAGE GLOBAL STYLES -->
        
        <!-- BEGIN PAGE LEVEL STYLES -->
        ";function anon_UCQxspzM($_data){};
$_text[] = anon_UCQxspzM($_data);
$_text[] = "
        <!-- END PAGE LEVEL STYLES -->
        
        <!-- BEGIN FAVICON -->
        <link rel=\"shortcut icon\" href=\"";
$_text[] =  $app['base_url'];
$_text[] = "/images/logos/iresponse_icon.ico\" /> 
        <!-- END FAVICON -->  
    </head>
    <!-- END HEAD -->
    <body class=\" login\">
        <!-- BEGIN PRE-LOADER -->
        <section class=\"wrapper\" style=\"opacity: 0.7 !important;\">
            <div class=\"spinner\"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
        </section>
        <!-- END PRE-LOADER -->
        <!-- BEGIN LOGIN -->
        <div class=\"content\" style=\"display: none;\">
            <!-- BEGIN LOGIN FORM -->
            <form class=\"login-form validate\" action=\"";
$_text[] =  $app['base_url'];
$_text[] = "/auth/login.html\" method=\"post\">
                <h3 class=\"form-title\"><img src=\"";
$_text[] =  $app['base_url'];
$_text[] = "/images/logos/iresponse-black.png\" alt=\"";
$_text[] =  $app['name'];
$_text[] = "\"/></h3>
                <div class=\"form-group\">
                    <label class=\"control-label visible-ie8 visible-ie9\">Email</label>
                    <div class=\"input-icon\"><i class=\"fa fa-user\" style=\"margin-top: 7px;margin-left: 6px;\"></i><input class=\"form-control placeholder-no-fix\" data-validation-message=\"Email is Required !\" data-required=\"true\" type=\"text\" autocomplete=\"off\" placeholder=\"Email\" name=\"email\" /> </div>
                </div>
                <div class=\"form-group\">
                    <label class=\"control-label visible-ie8 visible-ie9\">Password</label>
                    <div class=\"input-icon\"><i class=\"fa fa-lock\" style=\"margin-top: 7px;margin-left: 6px;\"></i><input class=\"form-control placeholder-no-fix\" data-validation-message=\"Password is Required !\" data-required=\"true\" type=\"password\" autocomplete=\"off\" placeholder=\"Password\" name=\"password\" /> </div>
                </div>
                <div class=\"form-actions\">
                    <button type=\"submit\" class=\"btn btn-outline dark pull-right submit-loading\"> Login </button>
                </div>
            </form>
            <!-- END LOGIN FORM -->
        </div>
        <!-- BEGIN COPYRIGHT -->
        <div class=\"copyright\" style=\"display: none;\"> ";
$_text[] =  $app['name'];
$_text[] = " ";
$_text[] =  FW_RELEASE_DATE;
$_text[] = " ( ";
$_text[] =  $app['company'];
$_text[] = " ) </div>
        <!-- END COPYRIGHT -->
        <!-- BEGIN JAVASCRIPTS -->
            <!-- BEGIN CORE PLUGINS -->   
            ";function anon_whIwKdTC($_data){};
$_text[] = anon_whIwKdTC($_data);
$_text[] = "
            <!-- END CORE PLUGINS -->

            <!-- BEGIN CORE PLUGINS -->   
            ";function anon_IPpmdIsS($_data){};
$_text[] = anon_IPpmdIsS($_data);
$_text[] = "
            <!-- END CORE PLUGINS -->
        <!-- END JAVASCRIPTS -->
        <!-- BEGIN PREV MESSAGE -->
        ";if((isset($prev_action_message))) {$_text[] = "
            ";
$_text[] =  $prev_action_message;
$_text[] = "
        ";}$_text[] = "
        <!-- END PREV MESSAGE -->
        <!-- BEGIN PRE-LOADER REMOVER -->
        <script>
        $(function()";$_text[] = "{ 
            $(\"section.wrapper\").fadeOut(500);
            $(\".logo\").css(\"display\",\"block\");
            $(\".content\").css(\"display\",\"block\");
            $(\".copyright\").css(\"display\",\"block\");
            $(\".page-content\").css(\"min-height\", ($(window).height() - 170) + \"px\"); 
        }";$_text[] = "); 
        </script>
        <!-- END PRE-LOADER REMOVER -->
    </body>
</html>";
return implode($_text);
