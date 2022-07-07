$(document).ready(function(){
    $(".shop-item").click(function(){
        const id = $(this).attr('prod');
        window.location="/product-details/"+id;
    })
})