$(document).ready(function(){
    console.log("JS IS READY");

    /*** add product  */

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




    $("#main-search-form").on('submit',function(e){
        e.preventDefault();

        const data = $( this ).serializeArray();
        console.log(data);

        // /products/{pageIndex}/{category}/{minPrice}/{maxPrice}/{city}/{keyWords}

        /**
         *  0: {name: 'keyWords', value: 'ninja'}
         *  1: {name: 'city', value: '2'}
         *  2: {name: 'category', value: '24'}
         */

         let category = data[2].value;
         if(category==''){
             category=0;
         }


         let city = data[1].value;
         if(city==''){
            city=0;
         }
         let keyWords = data[0].value;
         if(keyWords==''){
            keyWords='';
         }
           
 
        let url = '/products/1/'+category+'/0/250000/'+city+'/'+keyWords 
        window.location = url;

    })





    /**
   <form method="POST" enctype="multipart/form-data" id="update-photo-form">
                                    <input type="hidden" name="photo-user-update" id="photo-update-trigger" value="null">
                                    <input type="file" name="photo" id="photo" style="visibility:hidden;">
                                    
                                    <img src="{{app.user.photoURL}}"  class="w-50 update-profile-photo" alt="">
                                </form>
     */

    $(".update-profile-photo").click(function(){
        $("#photo").click();

    });

    $("#photo").change(function(){
        $("#update-photo-form").submit();
        
    });



    $(".category-filter-element").click( function(e) {
        console.log("parent");
        e.preventDefault();
        $(this).parent().children(".sub-list").slideToggle();

    });


    $('.select-input').select2();


});


/**
 * MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNmmddddddddddmmmdddhmMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMmhyso//+ossyhhhhyhyhhdmNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMNNNmdhhysosss+oooooossyhmNMMMMMMMMMMMMms++/+osyhmNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
dyysoooossyyyhhhyyyyysoso++ooyhNMMMMMMMMdso+sooydmMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMNmmmmmmNMMMMMMMNmdddhhsoosooosdMMMMMMmsoo+sshmMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMNdhhyysoooohMMMMMysoooyhdMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMNddhyyooosdMMMNsooosydNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMmhhhysossmNNms++oshNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMNdddy+::/++/:--:/sdNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMNhso+/::-::-----:::+sdNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMmy+///:::::::-----::-://+syhmNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMmhso++++/::-:::--------:://+soo/:/ymMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMNy/:/+oss+++/::::---.--::/+soomhd+:::+sdMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMm+:::+mdmdsyhs+//:--..--://ohyyhdmh/:::/oyNmmNNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMh/-::/hmmmmdhhho+//:----:://+ooo/oyh+:::/ooo//oohmNmNNMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMm+/++ohNMMMNhyssoo++/:::::////+oo/::/::///++/::///++++oosydmNMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMmhdmNNMMMMMMdhsoo++o+/::://////++/::----://:::::::---------:+oyyyyhdmNNNNNmdmmmmdyyy
MMMMMMMMMMMMMMMMMMMMMMMMMMMMmhysoo+oo+/:://////++/:::::::::::::::---------------.---::::::-------...
MMMMMMMMMMMMMMMMMMMMMMMMMMMMNhhsssooo+/--:////+o+//::::::::::::::------------------.................
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMNdysooo+//::::://+o++//::::://::::::----------------------------.......
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMhyss+/so+o+oo:/+o++///////////::::---::::::---:::::-::::::-----------
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNhys++ydddhyo:/oo++////////////:::::::::::::::::::::::::::::-::------
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNyyo++oyhho/:/ooo+++////+++//////::::://:::::::/:/:::::::::::-:---:-
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMmyyyyyyhhys++oooo+++///+++//////////////:////://://::::/::/::::::::
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMdyyyhhhhyooooooo++++/+++++//////++++++/////////::////://::/:::::::
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNhyhyyysosssoooo++++++++++/+++/++ooo++/+//::::/://////////://:::::
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMmyysyso+ssyssos+++++o++/+/+++/+++++++///////://:///+/++////////::
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMhyssso+ssysossoooooo++/++++++/+++//+++/:////////////++/+/////:::
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNhssso+syysossoooo++o++//+++o+//++////////:///////+/+///////////
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNsoso+shyyosso+o++++//////+ooo/+o+/+/:/:///:////+++///++///////
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMdoso+yhyyssosoo+++///////+++oo/++/+o+////+////+++++++/++//::::
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMdo+oyyyysssyooo+++///////o++o+/o+++o++////+++/++++++++/+++///
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMhsyyyysssssooo++/////oo+oo++s++/++++++///+++//+++++++++/++//
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMmhhyysyssooo++/////oooo+++++o+/+++/++/+//++/+/++++++++//+/
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMmyyyyyssssoo++/+++so++o+++++++++s/+++/++/+/++/+++++++++++
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMyyyyyysssooo++o/+ss++++++++o//++o+o++/++++/+++++++oooo++
developed by the goat CHOURABI TAHER tchourabi@gmail.com
 */