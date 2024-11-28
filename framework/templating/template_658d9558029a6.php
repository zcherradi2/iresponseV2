
if(is_array($_data) && sizeof($_data))
extract($_data); 
$_text = [];
$_text[] = "<script src=\"https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js\"></script>
<script>
  WebFont.load(";$_text[] = "{
    google: {\"families\":[\"Poppins:300,400,500,600,700\",\"Roboto:300,400,500,600,700\"]}";$_text[] = ",
    active: function() ";$_text[] = "{
        sessionStorage.fonts = true;
    }";$_text[] = "
  });
</script>


";
return implode($_text);
