$(document).ready(function() {
    $('input[type=radio]').change(function(){
        $('form').submit();
    });
});