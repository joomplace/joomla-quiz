jQuery(document).ready(function(){
    jQuery("body").on("contextmenu selectstart", function(){
        return false;
    });
});