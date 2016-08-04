Joomla.submitform = function(task, form, validate) {

		if (!form) {
			form = document.getElementById('adminForm');
		}

		if (task) {
			form.task.value = task;
		}

		if (task == 'move_to_cat'){
			document.forms[0].action = '/administrator/index.php?option=com_joomlaquiz&view=questions&layout=move_questions_cat';
				
		}
		
		if (task == 'copy_to_cat'){
			document.forms[0].action = '/administrator/index.php?option=com_joomlaquiz&view=questions&layout=copy_questions_cat';
				
		}

		form.noValidate = !validate;
		
		var button = document.createElement('input');
		button.style.display = 'none';
		button.type = 'submit';

		form.appendChild(button).click();

		form.removeChild(button);
	};
	