 <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<fb:like send="true" width="688" show_faces="false" font="lucida grande" href="<?php the_permalink(); ?>"></fb:like>

<fb:comments href="<?php the_permalink(); ?>" num_posts="50" width="688"></fb:comments>