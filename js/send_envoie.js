$(document).ready(function () {

    var x = 2;
    var id = $("#id").val();
    
    
    
    $("#ajout").click(function(){
        
        if (x < 6) {
            $.ajax({
                url: "add_send.php",
                method: "POST",
                data: {
                    id: id,
                    x: x
                },
    
                success: function (data) {
                    $("#tag_list").append(data);
    
    
                }
            });
            x++;
        }
    });

    $("#tag_list").on('click','#remove', function () {
        
            $(this).closest("div").remove();
            x--;
        
    });


})
