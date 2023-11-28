function redirection_scheduler_form_listener() {
	if (document.querySelector('#redirection-scheduler-set-form')) {
		document.querySelector('#redirection-scheduler-set-form').addEventListener('submit', (e) => {
			e.preventDefault();

			const formData = new FormData(e.target);

			fetch(e.target.action, {
				method: 'POST',
				body: formData
			})
			.then(res => res.json())
			.then(res => {
				if (!res.success) {
					alert(res.data[0].message);
					return;
				}

				alert('Redirect successfully scheduled.');
				window.location.reload();
			})
			.catch(error => {
				console.log(error);
			});
		});
	}
}
window.addEventListener('DOMContentLoaded', redirection_scheduler_form_listener);
