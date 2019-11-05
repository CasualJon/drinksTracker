//JS helper file for index.php
$('.form').find('input').on('keyup blur focus', function (e) {
    $this = $(this), label = $this.prev('label');

	  if (e.type === 'keyup') {
			if ($this.val() === '') label.removeClass('active highlight');
      else label.addClass('active highlight');
    }
    else if (e.type === 'blur') {
    	if ($this.val() === '') label.removeClass('active highlight');
      else label.removeClass('highlight');
    }
    else if (e.type === 'focus') {
      if ($this.val() === '') label.removeClass('highlight');
			else if ($this.val() !== '') label.addClass('highlight');
    }
});
