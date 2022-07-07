$(document).ready(function(){
    $(".datatable").DataTable();



    $(".copie-asset-url").click(function(){
        let url = $(this).attr('url');
        console.log(url);
 
        navigator.clipboard.writeText(url);

        $(this).html("copiée")
    });


    $("#product_add_town").change(function(){
        var id = $(this).val();

        console.log(id);

        if (id != '') {
            var url = '/city/town/'+id;
            $("#loading").html('chargement...')
            $("#product_add_city").attr('disabled','disabled');
            $.ajax({
                url: url, 
              }).done(function(cities) {
                var blocHTML = '<option value="" selected="selected">Veuillez choisir une valeur</option>';

                cities.forEach(city => {
                    blocHTML+='<option value="'+city.id+'" >'+city.name+'</option>';
                });

                $("#product_add_city").html(blocHTML);
                $("#loading").html('')
                $("#product_add_city").removeAttr('disabled');
                

              });

        }
    });




    
});