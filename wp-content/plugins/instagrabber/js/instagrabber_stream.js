jQuery(function($){
	var $post_types = '#post_type',
		$post_type_container = $('#post_type_container')
		$taxonomy = '#taxonomy',
		$taxonomy_container = $('#taxonomy_container')
		$terms = '#terms',
		$taxonomy_term_container = $('#taxonomy_term_container'),
		$taxonomy_tag = '#taxonomy_tag',
		$taxonomy_tag_container = $('#taxonomy_tag_container');

		$post_type_container.on('change', $post_types, function(){
			$type = $(this).val();
			$.get(ajaxurl, { action: 'instagrabber_load_taxonomies', is_ajax: "yes", type: $type }, function(data){
				$taxonomy_container.html(data);
			})

			$.get(ajaxurl, { action: 'instagrabber_load_tags', is_ajax: "yes", type: $type }, function(data){
				$taxonomy_tag_container.html(data);
			})
		});
		$taxonomy_container.on('change', $taxonomy, function(){
			console.log('change');
			$type = $(this).val();
			$.get(ajaxurl, { action: 'instagrabber_load_terms', is_ajax: "yes", tax: $type }, function(data){
				$taxonomy_term_container.html(data);
			})
		});

		$post_types.trigger('change');

});