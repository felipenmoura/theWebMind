/* 
    Document   : mind-RIDE
    Created on : Jun 10, 2011, 8:18:20 PM
    Author     : felipenmoura
    Description:
        Main Mind Rich IDE Script.
*/
$(document).ready(function(){
    if(document.getElementById('btn_login'))
    {
        $('#btn_login').click(function(){
            
            $.ajax({
                type:'post',
                url: '../../index.php',
                data: $('#frm_login').serialize(),
                success: function(data){
                    top.location.href= top.location.href;
                },
                error: function(xhr){
                    alert(xhr.responseText);
                }
            })
        });
    }else{
        $(window).bind('load', function(){
            
            $('#appBody').css({
                height: $(document).height() - 33+'px'
            })
        })
        var editor= document.getElementById('editor');
        editor.editableMode= true;
        editor.contentEditable= true;
    }
});