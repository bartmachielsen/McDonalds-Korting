var page = 0;

function add_check_coupon(coupon){
    $('#check_coupons').append(`
    <div id="box" code='`+coupon.CODE+`' class="part_visible">
        <img id="promo" src='http://az798212.vo.msecnd.net/xy/800/800/?path=`+coupon.IMAGE+`'/>
        <img id="barcode" src='/service/barcode/generator.php?data=`+coupon.CODE+`'/>
    </div>
    `);
        // <div id="CONFIRM"><a id='CONFIRM_BUTTON' href="/service/confirm.php?coupon=`+coupon.COUPON+`">Deze coupon werkt nog</a></div>
}

function add_coupon(coupon){
    $('#current_coupons').append(`
    <div code='`+coupon.CODE+`' id="box">
    <img id="promo" img='http://az798212.vo.msecnd.net/xy/800/800/?path=`+coupon.IMAGE+`' src='http://az798212.vo.msecnd.net/xy/800/800/?path=`+coupon.IMAGE+`'/>
    <img id="barcode" src='/service/barcode/generator.php?data=`+coupon.CODE+`'/></div>
    `);
}

function load_page_content(page){
    $.ajax({
        url: "http://mcdonalds-korting.nl/service/coupons.php?page="+page
    }).then(function(data) {
        data.OPEN.forEach(add_coupon, this);
        data.CHECK.forEach(add_check_coupon, this)
    });
    
    // $.ajax({
    //     url: "http://mcdonalds-korting.nl/service/coupons.php?action=CHECK&page="+page
    // }).then(function(data) {
    //     data.CHECK.forEach(add_check_coupon, this);
    // });
}
$(document).ready(function() {
    load_page_content(page);
    // $('.load_more').on("click", function(){
    //     page++;
    //     load_page_content(page);
    // });;
    $("DIV").on('click', "#box",function(){
        window.open('graph.html?code='+$(this).attr("code"),'_blank');
    });
});
