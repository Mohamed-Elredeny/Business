
let toggleCommentOptions = (id) => {
  if ($('.comment-options-' + id).css('display') == 'block') $('.comment-options-' + id).css('display', 'none')
  else $('.comment-options-' + id).css('display', 'block')
}

let toggleOptions = (id) => {
  if ($('.post-options-' + id).css('display') == 'block') $('.post-options-' + id).css('display', 'none')
  else $('.post-options-' + id).css('display', 'block')
}

let toggleComments = (id) => {
  if ($('.post-comment-list-' + id).css('display') == 'block') $('.post-comment-list-' + id).css('display', 'none')
  else $('.post-comment-list-' + id).css('display', 'block')
}
let commentAttachClick = (id) => {
  $("#comment-attach-" + id).trigger('click');
}

let deletePost = (id) => { $('#post-' + id).css('display', 'none') }


let toggleReply = (id,name) => {
    if ($('#comment-replies-' + id).css('display') == 'block') {
        $('#add-reply-div-' + id).css('display', 'none');
        $('#comment-replies-' + id).css('display', 'none');
    }
    else {
        $('#comment-replies-' + id).css('display', 'block');
        $('#add-reply-div-' + id).css('display', 'block');
        document.getElementById('reply-text-' + id).focus();
        document.getElementById('reply-text-' + id).value = '@'+name.split(" ")[0]
    }
}


let makeReply = (comment_id,name) => {
    $('#add-reply-div-' + comment_id).css('display', 'block');
    document.getElementById('reply-text-' + comment_id).focus();
    document.getElementById('reply-text-' + comment_id).value = '@'+name.split(" ")[0]
}

