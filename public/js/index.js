// $(document).ready(function () {
//     $(".filter-button").click(function () {
//         var post_id = $(this).attr('id');
//         var value = $(this).attr("data-filter");
//         if (value == "all-" + post_id){
//             $(".filter-" + post_id).show("1000");
//         }
//         else {
//             $(".filter-" + post_id)
//                 .not("." + value)
//                 .hide("3000");
//             $(".filter-" + post_id)
//                 .filter("." + value)
//                 .show("3000");
//         }
//         $(".active-" + post_id).removeClass("ez-active")
//         console.log('sdfdsf')
//         $(this).addClass("ez-active");
//     });
//     // if ($(".filter-button").removeClass("active"))
//     //     $(this).removeClass("active");
//     // $(this).addClass("active");
// });

let filterPostLikes = (post_id,value) => {
    if (value == "all-" + post_id){
        $(".filter-" + post_id).show("1000");
    }
    else {
        $(".filter-" + post_id)
            .not("." + value)
            .hide("3000");
        $(".filter-" + post_id)
            .filter("." + value)
            .show("3000");
    }
    $(".active-" + post_id).removeClass("ez-active")
    $('.filter-' + value).addClass("ez-active");
}

let filterCommentLikes = (comment_id,value) => {
    if (value == "all-" + comment_id){
        $(".filter-" + comment_id).show("1000");
    }
    else {
        $(".filter-" + comment_id)
            .not("." + value)
            .hide("3000");
        $(".filter-" + comment_id)
            .filter("." + value)
            .show("3000");
    }
    $(".active-" + comment_id).removeClass("ez-active")
    $('.filter-' + value).addClass("ez-active");
}

let filterReplyLikes = (reply_id,value) => {
    if (value == "all-" + reply_id){
        $(".filter-" + reply_id).show("1000");
    }
    else {
        $(".filter-" + reply_id)
            .not("." + value)
            .hide("3000");
        $(".filter-" + reply_id)
            .filter("." + value)
            .show("3000");
    }
    $(".active-" + reply_id).removeClass("ez-active")
    $('.filter-' + value).addClass("ez-active");
}

let openSidenav = () => {
    $('#sideNavbar').css('width', '100%')
    $('#sideNavbar').css('display', 'block')
    $('.navbar').css('position', 'fixed')
}

let closeSidenav = () => {
    $('#sideNavbar').css('display', 'none')
    $('.navbar').css('position', 'static')
}

let commentAttach = () => {
    $("#file1").trigger('click');
}

$("#post-type-service").click(() => $("#post-type-service-content").removeClass("d-none"))
$("#post-type-post").click(() => $("#post-type-service-content").addClass("d-none"))


// $('#showStoryModal').on('hide.bs.modal', function(e) {
//     var $if = $(e.delegateTarget).find('iframe');
//     var src = $if.attr("src");
//     $if.attr("src", '/empty.html');
//     $if.attr("src", src);
// });

// document.getElementsByClassName('story-video').play();
