function updateTimeline() {
    // First, reinitiate the Facebook comments for the newly added posts
    FB.XFBML.parse();
    
    // Next, we need to add the new items to the menu nav
    var nav = $('#box-scrool-bar ul');
    var items = [];
    $('article.hentry').each(function(i,e) {
        var item = new Object();
        item.id = $(this).attr('id');
        item.time = $(this).find('.date-post').text();
        items[i] = item;
    });
    var li = '<li class="anchor_post_current"><a id="back-top" class="anchor_post" href="#top">Now</a></li>';
    $.map(items, function(value, key) {
        var a = '<a href="#'+value.id+'">'+value.time+'</a>';
        li += '<li>' + a + '</li>';
    });
    nav.html(li);
}