$(document).ready(function () {
    
    var x = 2;
    $("#ajout").click(function(){
        
        if (x < 6) {
            var new_div = '<div class="form-group"><label for="inputCodeBar"> Code bar/Disponible: </label><input type="text" id="inputCodeBar" name="codeBar['+ x +']"><input type="hidden" id="inputDisponible" value = "0"  name="disponible['+ x +']" /><input type="checkbox" id="inputDisponible" value="1" name="disponible['+ x +']"/><input type="button" value="X" id="remove" class="button-form"></div>';
            $("#tag_list").append(new_div);
            x++;
        }
    });
    $("#tag_list").on('click','#remove', function () {
        
            $(this).closest("div").remove();
            x--;
        
    });
})
